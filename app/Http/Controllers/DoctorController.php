<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AiResponse;
use App\Models\DoctorReview;

class DoctorController extends Controller
{
    private function ensureDoctor(): void
    {
        if (!auth()->user()?->isDoctor()) {
            abort(403, 'Unauthorized - Doctor access only');
        }
    }

    public function dashboard()
    {
        $this->ensureDoctor();
        $pendingReviews = AiResponse::pendingDoctorReview()
            ->with(['symptomEntry.user', 'symptomEntry.bodyRegion'])
            ->latest()
            ->get();

        $recentReviews = DoctorReview::with(['doctor', 'aiResponse.symptomEntry'])
            ->latest()
            ->take(20)
            ->get();

        $stats = [
            'total_reviews' => DoctorReview::count(),
            'pending' => $pendingReviews->count(),
            'emergency_cases' => AiResponse::emergency()->count(),
        ];

        return view('doctor.dashboard', compact('pendingReviews', 'recentReviews', 'stats'));
    }

    public function review($id)
    {
        $this->ensureDoctor();

        $aiResponse = AiResponse::with(['symptomEntry.user', 'symptomEntry.bodyRegion'])
            ->findOrFail($id);

        return view('doctor.review', compact('aiResponse'));
    }

    public function approve(Request $request, $id)
    {
        $this->ensureDoctor();

        $validated = $request->validate([
            'decision' => 'required|in:approved,modified,rejected',
            'modified_response' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $aiResponse = AiResponse::findOrFail($id);

        if ($validated['decision'] === 'approved') {
            $aiResponse->approveByDoctor(auth()->id());
        } else {
            $aiResponse->update([
                'doctor_approved' => false,
                'doctor_modified_response' => $validated['modified_response'] ?? null,
            ]);
        }

        DoctorReview::create([
            'doctor_id' => auth()->id(),
            'ai_response_id' => $id,
            'review_decision' => $validated['decision'],
            'review_notes' => $validated['notes'] ?? null,
            'modified_advice' => $validated['modified_response'] ?? null,
            'doctor_license_number' => auth()->user()->doctor_license_number,
        ]);

        return redirect()->route('doctor.dashboard')
            ->with('success', 'Response reviewed successfully');
    }
}
