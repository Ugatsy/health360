<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SymptomSession;
use App\Models\UserFeedback;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // If user is not logged in, show a simplified dashboard for guests
        if (!$user) {
            return view('dashboard', [
                'recentSessions' => collect([]), // Empty collection
                'stats' => [
                    'total_sessions' => 0,
                    'emergencies_detected' => 0,
                    'avg_pain_level' => 0,
                    'helpful_responses' => 0,
                ]
            ]);
        }

        // Get recent symptom sessions for authenticated user
        $recentSessions = SymptomSession::where('user_id', $user->id)
->with(['symptomEntries' => function($query) {
    $query->with('bodyRegion');
}])
            ->latest()
            ->take(5)
            ->get();

        // Get stats for authenticated user
        $stats = [
            'total_sessions' => SymptomSession::where('user_id', $user->id)->count(),
            'emergencies_detected' => SymptomSession::where('user_id', $user->id)
                ->where('was_emergency_detected', true)
                ->count(),
            'avg_pain_level' => \App\Models\SymptomEntry::where('user_id', $user->id)
                ->avg('pain_intensity') ?? 0,
            'helpful_responses' => UserFeedback::where('user_id', $user->id)
                ->where('was_helpful', true)
                ->count(),
        ];

        return view('dashboard', compact('recentSessions', 'stats'));
    }
}
