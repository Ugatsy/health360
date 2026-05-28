<?php

namespace App\Services;

use App\Models\SymptomEntry;
use App\Models\MedicalProfile;
use App\Models\AiResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAnalysisService
{
    private string $apiKey;
    private string $endpoint;
    private string $model;
    private int $maxTokens;
    private float $temperature;

    public function __construct()
    {
        $this->apiKey = config('services.ai.api_key');
        $this->endpoint = config('services.ai.endpoint', 'https://api.openai.com/v1/chat/completions');
        $this->model = config('services.ai.model', 'gpt-4o-mini');
        $this->maxTokens = config('services.ai.max_tokens', 1024);
        $this->temperature = config('services.ai.temperature', 0.3);
    }

    public function analyze(SymptomEntry $entry, ?MedicalProfile $profile = null): AiResponse
    {
        $payload = $this->buildPayload($entry, $profile);

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post($this->endpoint, $payload);

            if ($response->failed()) {
                Log::error('AI analysis API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'symptom_entry_id' => $entry->id,
                ]);
                return $this->fallbackResponse($entry, 'AI service temporarily unavailable. Please try again.');
            }

            $parsed = $this->parseResponse($response->json());
            return $this->createResponse($entry, $parsed);

        } catch (\Exception $e) {
            Log::error('AI analysis exception', [
                'message' => $e->getMessage(),
                'symptom_entry_id' => $entry->id,
            ]);
            return $this->fallbackResponse($entry, 'Unable to complete analysis at this time.');
        }
    }

    private function buildPayload(SymptomEntry $entry, ?MedicalProfile $profile): array
    {
        $user = $entry->user;

        $systemPrompt = <<<'PROMPT'
You are a medical analysis assistant. Analyze the described symptoms and return a structured JSON response.

Rules:
- Be conservative — never diagnose, only list possible explanations.
- Use clinical but plain language patients can understand.
- Risk levels: "low" (self-care), "medium" (see doctor within days), "high" (see doctor within 24h), "emergency" (immediate care).
- For emergency symptoms, always set risk to "emergency".
- Likelihood values must be between 0.0 and 1.0.

For "risk_factors", list ONLY factors that are DIRECTLY tied to the current symptoms and patient history — such as:
  - Specific symptom characteristics that raise concern (e.g., "Pain lasting over a week", "Accompanied by fever")
  - Pre-existing conditions that complicate this specific symptom (e.g., "History of heart condition with chest pain")
  - DO NOT list generic lifestyle risk factors like "age", "smoking", "obesity", "sedentary lifestyle" unless they are explicitly documented in the patient history provided below.

Your entire response must be ONLY valid JSON with no markdown, no code blocks, no extra text. Use this exact schema:
{
  "possible_explanations": [
    {"name": "string", "description": "string", "likelihood": 0.0-1.0}
  ],
  "home_remedies": ["string"],
  "when_to_see_doctor": "string",
  "ai_risk_level": "low|medium|high|emergency",
  "risk_factors": ["string"]
}
PROMPT;

        $userMessage = "## Symptom Details\n";
        $userMessage .= "- Body Region: {$entry->bodyRegion?->name}\n";
        $userMessage .= "- Description: {$entry->symptom_text}\n";
        $userMessage .= "- Pain Type: {$entry->pain_type}\n";
        $userMessage .= "- Pain Intensity: {$entry->pain_intensity}/10\n";
        $userMessage .= "- Duration: {$entry->pain_duration}\n";
        $userMessage .= "- Additional Symptoms: " . $this->formatArray($entry->additional_symptoms) . "\n";

        if ($profile || $user) {
            $userMessage .= "\n## Patient History\n";
            if ($user?->date_of_birth) {
                $userMessage .= "- Age: {$user->age}\n";
            }
            if ($user?->biological_sex) {
                $userMessage .= "- Sex: {$user->biological_sex}\n";
            }
            if ($profile) {
                $conditions = $profile->getCriticalConditionsList();
                if ($conditions) {
                    $userMessage .= "- Pre-existing Conditions: " . implode(', ', $conditions) . "\n";
                }
                if ($profile->current_medications) {
                    $userMessage .= "- Current Medications: " . $this->formatArray($profile->current_medications) . "\n";
                }
                if ($profile->allergies) {
                    $allergens = is_array($profile->allergies)
                        ? array_column($profile->allergies, 'substance')
                        : [];
                    $userMessage .= "- Allergies: " . implode(', ', $allergens) . "\n";
                }
            }
        }

        return [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ],
                'max_tokens' => $this->maxTokens,
                'temperature' => $this->temperature,
            ];
    }

    private function parseResponse(array $responseData): array
    {
        $content = $responseData['choices'][0]['message']['content'] ?? '{}';

        // Strip markdown code fences if the AI wrapped JSON in them
        $content = preg_replace('/^```(?:json)?\s*\n?/', '', $content);
        $content = preg_replace('/\n?```\s*$/', '', $content);
        $content = trim($content);

        $parsed = json_decode($content, true);

        return [
            'possible_explanations' => $parsed['possible_explanations'] ?? [],
            'home_remedies' => $parsed['home_remedies'] ?? [],
            'when_to_see_doctor' => $parsed['when_to_see_doctor'] ?? 'Monitor your symptoms. If they persist or worsen, consult a healthcare provider.',
            'ai_risk_level' => in_array($parsed['ai_risk_level'] ?? '', ['low', 'medium', 'high', 'emergency'])
                ? $parsed['ai_risk_level']
                : 'low',
            'risk_factors' => $parsed['risk_factors'] ?? [],
        ];
    }

    private function createResponse(SymptomEntry $entry, array $data): AiResponse
    {
        return AiResponse::create([
            'symptom_entry_id' => $entry->id,
            'raw_ai_response' => $data,
            'possible_explanations' => $data['possible_explanations'],
            'home_remedies' => $data['home_remedies'],
            'when_to_see_doctor' => $data['when_to_see_doctor'],
            'ai_risk_level' => $data['ai_risk_level'],
            'risk_factors' => $data['risk_factors'],
        ]);
    }

    private function formatArray(mixed $value): string
    {
        if (is_array($value)) {
            return implode(', ', $value);
        }
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? implode(', ', $decoded) : $value;
        }
        return 'none';
    }

    private function fallbackResponse(SymptomEntry $entry, string $reason): AiResponse
    {
        return AiResponse::create([
            'symptom_entry_id' => $entry->id,
            'raw_ai_response' => ['error' => $reason],
            'possible_explanations' => [],
            'home_remedies' => ['Monitor your symptoms', 'Rest and stay hydrated', 'Consult a healthcare provider if symptoms persist'],
            'when_to_see_doctor' => 'If your symptoms worsen or persist for more than a few days, please consult a healthcare provider.',
            'ai_risk_level' => 'low',
            'risk_factors' => [],
        ]);
    }
}
