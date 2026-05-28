<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SymptomSession;
use App\Models\SymptomEntry;
use App\Models\BodyRegion;
use App\Models\AiResponse;
use App\Services\AiAnalysisService;
use Illuminate\Support\Str;
use App\Events\EmergencyDetected;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SymptomController extends Controller
{
    public function index()
    {
        // Get all body regions for the 3D model
        $bodyRegions = BodyRegion::active()
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        // Get or create active session
        $session = SymptomSession::where('user_id', auth()->id())
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$session) {
            $session = SymptomSession::create([
                'user_id' => auth()->id(),
                'session_uuid' => Str::uuid(),
                'started_at' => now(),
                'status' => 'active'
            ]);
        }

        return view('symptoms.index', compact('bodyRegions', 'session'));
    }

    public function store(Request $request)
    {
 $validated = $request->validate([
        'session_id' => 'required|exists:symptom_sessions,id',
        'body_region_id' => 'required|exists:body_regions,id',
        'symptom_text' => 'required|string|min:3',
        'pain_type' => 'nullable|string',
        'pain_intensity' => 'nullable|integer|min:0|max:10',
        'duration' => 'nullable|string',
        'additional_symptoms' => 'nullable|array',
    ]);

        // Create symptom entry
   $symptomEntry = SymptomEntry::create([
        'session_id' => $validated['session_id'],
        'user_id' => auth()->id(),
        'body_region_id' => $validated['body_region_id'],
        'symptom_text' => $validated['symptom_text'],
        'pain_type' => $validated['pain_type'] ?? null,
        'pain_intensity' => $validated['pain_intensity'] ?? null,
        'pain_duration' => $validated['duration'] ?? null,  // Map duration to pain_duration
        'additional_symptoms' => $validated['additional_symptoms'] ?? null,
        'symptom_started_at' => now(),
        'recorded_at' => now(),
    ]);

        // Check for emergency keywords
        if ($symptomEntry->isEmergencyKeywordDetected()) {
            return $this->handleEmergency($symptomEntry);
        }

        // Process with AI
        $aiResponse = $this->processWithAI($symptomEntry);

        // Fire EmergencyDetected event for high-risk results
        if (in_array($aiResponse->ai_risk_level, ['high', 'emergency'])) {
            EmergencyDetected::dispatch(
                user: auth()->user(),
                riskLevel: $aiResponse->ai_risk_level,
                symptomText: $symptomEntry->symptom_text,
                recommendation: $aiResponse->when_to_see_doctor ?? 'Seek medical attention immediately.',
                symptomEntry: $symptomEntry,
            );
        }

        return redirect()->route('symptoms.results', $symptomEntry->id);
    }

    public function results($id)
    {
        $symptomEntry = SymptomEntry::with(['bodyRegion', 'aiResponse'])
            ->findOrFail($id);

        return view('symptoms.results', compact('symptomEntry'));
    }

    public function history()
    {
        $symptomEntries = SymptomEntry::with(['bodyRegion', 'aiResponse'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('symptoms.history', compact('symptomEntries'));
    }

    public function feedback(Request $request, $id)
    {
        $validated = $request->validate([
            'helpful' => 'required|boolean',
        ]);

        $symptomEntry = SymptomEntry::findOrFail($id);
        $aiResponse = $symptomEntry->aiResponse;

        if ($aiResponse) {
            $aiResponse->feedback()->create([
                'user_id' => auth()->id(),
                'was_helpful' => $validated['helpful'],
            ]);
        }

        return redirect()->route('symptoms.results', $id)->with('success', 'Thank you for your feedback.');
    }

    private function handleEmergency($symptomEntry)
    {
        // Mark session as emergency
        $symptomEntry->session->markEmergency(
            'Emergency symptoms detected. Please seek immediate medical attention.'
        );

        // Create emergency alert
        \App\Models\EmergencyAlert::create([
            'user_id' => auth()->id(),
            'symptom_session_id' => $symptomEntry->session_id,
            'trigger_keyword' => $symptomEntry->getEmergencyKeywordMatched(),
            'user_symptom_text' => $symptomEntry->symptom_text,
            'action_taken' => 'displayed_emergency_message',
        ]);

        // Fire EmergencyDetected event for keyword-based emergencies
        EmergencyDetected::dispatch(
            user: auth()->user(),
            riskLevel: 'emergency',
            symptomText: $symptomEntry->symptom_text,
            recommendation: 'Emergency symptoms detected. Please seek immediate medical attention.',
            symptomEntry: $symptomEntry,
        );

        return redirect()->route('symptoms.results', $symptomEntry->id)->with(
            'error', 'Your symptoms require immediate medical attention. Please call emergency services (911) or go to the nearest emergency room.'
        );
    }

    private function processWithAI($symptomEntry)
    {
        $service = app(AiAnalysisService::class);
        $response = $service->analyze($symptomEntry, auth()->user()->medicalProfile);

        // Refresh to get fresh response with loaded relationships
        return $response->fresh();
    }

    private function sendToN8N($symptomEntry)
    {
        $webhookUrl = env('N8N_WEBHOOK_URL', 'http://localhost:5678/webhook/symptom-analysis');

        try {
            Http::post($webhookUrl, [
                'symptom_entry_id' => $symptomEntry->id,
                'symptom_text' => $symptomEntry->symptom_text,
                'body_region' => $symptomEntry->bodyRegion->name,
                'pain_intensity' => $symptomEntry->pain_intensity,
                'user_conditions' => auth()->user()->medicalProfile?->getCriticalConditionsList(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send to n8n: ' . $e->getMessage());
        }
    }
}
