@extends('layouts.app')

@section('title', 'Dashboard — Health360')
@section('header', 'Health Dashboard')
@section('subheader', 'Your AI-assisted health overview')

@section('header-actions')
    @auth
    <a href="{{ route('symptoms.index') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> New symptom check
    </a>
    @else
    <a href="{{ route('login') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-sign-in-alt"></i> Sign in to get started
    </a>
    @endauth
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 space-y-6">

    {{-- Welcome banner --}}
    <div class="card shadow fade-up" style="padding:24px 28px; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:16px;">
        <div>
            <p style="font-size:12px; color:var(--teal); font-weight:600; text-transform:uppercase; letter-spacing:.05em; margin-bottom:4px;">
                <span style="display:inline-block; width:6px; height:6px; border-radius:50%; background:var(--teal); margin-right:5px;"></span>
                @auth All systems healthy @else Your health companion @endauth
            </p>
            <h2 style="font-size:22px; font-weight:600; color:var(--ink);">
                @auth
                    Good to see you, {{ auth()->user()->name }}.
                @else
                    Welcome to Health360
                @endauth
            </h2>
            <p style="font-size:14px; color:var(--muted); margin-top:4px;">
                @auth
                    Track symptoms, review AI insights, and stay ahead of your health.
                @else
                    Sign in to track symptoms, get AI insights, and monitor your health.
                @endauth
            </p>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            @auth
            <a href="{{ route('symptoms.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-stethoscope"></i> Check symptoms
            </a>
            <a href="{{ route('symptoms.history') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-clock-rotate-left"></i> History
            </a>
            @else
            <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus"></i> Create account
            </a>
            <a href="{{ route('login') }}" class="btn btn-outline btn-sm">
                <i class="fas fa-sign-in-alt"></i> Sign in
            </a>
            @endauth
        </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:14px;">
        <div class="card shadow stat-card fade-up delay-1">
            <div style="display:flex; align-items:flex-start; justify-content:space-between;">
                <div>
                    <p class="stat-label">Total Sessions</p>
                    <p class="stat-value">{{ $stats['total_sessions'] }}</p>
                    <p class="stat-sub" style="color:var(--teal);">+12% this month</p>
                </div>
                <div style="width:38px; height:38px; background:var(--teal-light); border-radius:9px; display:flex; align-items:center; justify-content:center; color:var(--teal);">
                    <i class="fas fa-notes-medical" style="font-size:15px;"></i>
                </div>
            </div>
        </div>

        <div class="card shadow stat-card fade-up delay-2">
            <div style="display:flex; align-items:flex-start; justify-content:space-between;">
                <div>
                    <p class="stat-label">Emergency Alerts</p>
                    <p class="stat-value" style="color:var(--rose);">{{ $stats['emergencies_detected'] }}</p>
                    <p class="stat-sub">Seek care if needed</p>
                </div>
                <div style="width:38px; height:38px; background:var(--rose-light); border-radius:9px; display:flex; align-items:center; justify-content:center; color:var(--rose);">
                    <i class="fas fa-truck-medical" style="font-size:15px;"></i>
                </div>
            </div>
        </div>

        <div class="card shadow stat-card fade-up delay-3">
            <div style="display:flex; align-items:flex-start; justify-content:space-between;">
                <div>
                    <p class="stat-label">Avg. Pain Level</p>
                    <p class="stat-value">{{ number_format($stats['avg_pain_level'], 1) }}<span style="font-size:15px; color:var(--muted);">/10</span></p>
                    <p class="stat-sub">Across recent reports</p>
                </div>
                <div style="width:38px; height:38px; background:#fff3e0; border-radius:9px; display:flex; align-items:center; justify-content:center; color:var(--amber);">
                    <i class="fas fa-wave-square" style="font-size:15px;"></i>
                </div>
            </div>
        </div>

        <div class="card shadow stat-card fade-up delay-4">
            <div style="display:flex; align-items:flex-start; justify-content:space-between;">
                <div>
                    <p class="stat-label">Helpful Responses</p>
                    <p class="stat-value" style="color:#16a34a;">{{ $stats['helpful_responses'] }}</p>
                    <p class="stat-sub">AI accuracy feedback</p>
                </div>
                <div style="width:38px; height:38px; background:#f0fdf4; border-radius:9px; display:flex; align-items:center; justify-content:center; color:#16a34a;">
                    <i class="fas fa-thumbs-up" style="font-size:15px;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Main grid --}}
    <div style="display:grid; grid-template-columns:1fr 320px; gap:20px;" class="lg-grid">
        {{-- Recent sessions --}}
        <div class="card shadow" style="overflow:hidden;">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <h3 style="font-size:15px; font-weight:600; color:var(--ink);">Recent sessions</h3>
                    <p style="font-size:12px; color:var(--muted); margin-top:1px;">
                        @auth Your last 5 symptom checks @else Sign in to see your history @endauth
                    </p>
                </div>
                @auth
                <a href="{{ route('symptoms.history') }}" style="font-size:13px; color:var(--teal); font-weight:500;">View all →</a>
                @endauth
            </div>

            @auth
                @forelse($recentSessions as $session)
                    @php
                        $risk = $session->aiResponse?->ai_risk_level ?? 'low';
                        $riskLabels = ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'emergency' => 'Emergency'];
                    @endphp
                    <a href="{{ route('symptoms.results', $session) }}"
                       style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 20px; border-bottom:1px solid var(--border); transition:background .12s;"
                       onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='transparent'">
                        <div style="display:flex; align-items:center; gap:12px; min-width:0; flex:1;">
                            <div style="width:38px; height:38px; border-radius:9px; background:var(--surface); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; flex-shrink:0; color:var(--muted);">
                                <i class="fas fa-location-dot" style="font-size:14px;"></i>
                            </div>
                            <div style="min-width:0;">
                                <p style="font-size:14px; font-weight:500; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $session->symptomEntries->first()?->bodyRegion?->name ?? 'Unknown' }} — {{ Str::limit($session->symptomEntries->first()?->description ?? '', 55) }}
                                </p>
                                <p style="font-size:12px; color:var(--muted); margin-top:2px;">
                                    {{ $session->created_at->diffForHumans() }} · Pain {{ $session->symptomEntries->first()?->pain_intensity ?? '—' }}/10
                                </p>
                            </div>
                        </div>
                        <div style="display:flex; align-items:center; gap:8px; flex-shrink:0;">
                            <span class="chip risk-{{ $risk }}" style="font-size:11px;">
                                <span style="width:6px; height:6px; border-radius:50%; background:currentColor; display:inline-block;"></span>
                                {{ $riskLabels[$risk] ?? ucfirst($risk) }}
                            </span>
                            <i class="fas fa-chevron-right" style="font-size:11px; color:#cbd5e1;"></i>
                        </div>
                    </a>
                @empty
                    <div style="padding:48px 20px; text-align:center;">
                        <div style="width:52px; height:52px; margin:0 auto 12px; background:var(--surface); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#94a3b8;">
                            <i class="fas fa-notes-medical" style="font-size:20px;"></i>
                        </div>
                        <p style="font-size:14px; font-weight:500; color:var(--ink);">No sessions yet</p>
                        <p style="font-size:13px; color:var(--muted); margin-top:4px;">Run your first symptom check to see it here.</p>
                        <a href="{{ route('symptoms.index') }}" class="btn btn-primary btn-sm" style="margin-top:14px; display:inline-flex;">
                            <i class="fas fa-plus"></i> Start a check
                        </a>
                    </div>
                @endforelse
            @else
                <div style="padding:48px 20px; text-align:center;">
                    <div style="width:52px; height:52px; margin:0 auto 12px; background:var(--surface); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#94a3b8;">
                        <i class="fas fa-lock" style="font-size:20px;"></i>
                    </div>
                    <p style="font-size:14px; font-weight:500; color:var(--ink);">Sign in to view your history</p>
                    <p style="font-size:13px; color:var(--muted); margin-top:4px;">Create an account to start tracking your symptoms.</p>
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm" style="margin-top:14px; display:inline-flex;">
                        <i class="fas fa-sign-in-alt"></i> Sign in
                    </a>
                </div>
            @endauth
        </div>

        {{-- Sidebar --}}
        <div style="display:flex; flex-direction:column; gap:16px;">
            {{-- Quick actions --}}
            <div class="card shadow" style="padding:18px;">
                <h3 style="font-size:14px; font-weight:600; color:var(--ink); margin-bottom:12px;">Quick actions</h3>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                    @auth
                    <a href="{{ route('symptoms.index') }}" style="display:flex; flex-direction:column; align-items:flex-start; gap:8px; padding:12px; border-radius:9px; background:var(--surface); border:1px solid var(--border); transition:background .12s;"
                       onmouseover="this.style.background='var(--teal-light)'" onmouseout="this.style.background='var(--surface)'">
                        <i class="fas fa-stethoscope" style="font-size:16px; color:var(--teal);"></i>
                        <span style="font-size:13px; font-weight:500; color:var(--ink);">New check</span>
                    </a>
                    <a href="{{ route('symptoms.history') }}" style="display:flex; flex-direction:column; align-items:flex-start; gap:8px; padding:12px; border-radius:9px; background:var(--surface); border:1px solid var(--border); transition:background .12s;"
                       onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='var(--surface)'">
                        <i class="fas fa-clock-rotate-left" style="font-size:16px; color:#3b82f6;"></i>
                        <span style="font-size:13px; font-weight:500; color:var(--ink);">History</span>
                    </a>
                    @else
                    <a href="{{ route('login') }}" style="display:flex; flex-direction:column; align-items:flex-start; gap:8px; padding:12px; border-radius:9px; background:var(--surface); border:1px solid var(--border); transition:background .12s;"
                       onmouseover="this.style.background='var(--teal-light)'" onmouseout="this.style.background='var(--surface)'">
                        <i class="fas fa-sign-in-alt" style="font-size:16px; color:var(--teal);"></i>
                        <span style="font-size:13px; font-weight:500; color:var(--ink);">Sign in</span>
                    </a>
                    <a href="{{ route('register') }}" style="display:flex; flex-direction:column; align-items:flex-start; gap:8px; padding:12px; border-radius:9px; background:var(--surface); border:1px solid var(--border); transition:background .12s;"
                       onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='var(--surface)'">
                        <i class="fas fa-user-plus" style="font-size:16px; color:#3b82f6;"></i>
                        <span style="font-size:13px; font-weight:500; color:var(--ink);">Register</span>
                    </a>
                    @endauth
                    <a href="#" style="display:flex; flex-direction:column; align-items:flex-start; gap:8px; padding:12px; border-radius:9px; background:var(--surface); border:1px solid var(--border); transition:background .12s;"
                       onmouseover="this.style.background='#f5f3ff'" onmouseout="this.style.background='var(--surface)'">
                        <i class="fas fa-file-medical" style="font-size:16px; color:#7c3aed;"></i>
                        <span style="font-size:13px; font-weight:500; color:var(--ink);">Records</span>
                    </a>
                    <a href="#" style="display:flex; flex-direction:column; align-items:flex-start; gap:8px; padding:12px; border-radius:9px; background:var(--surface); border:1px solid var(--border); transition:background .12s;"
                       onmouseover="this.style.background='#f0fdf4'" onmouseout="this.style.background='var(--surface)'">
                        <i class="fas fa-user-doctor" style="font-size:16px; color:#16a34a;"></i>
                        <span style="font-size:13px; font-weight:500; color:var(--ink);">Find a doctor</span>
                    </a>
                </div>
            </div>

            {{-- Emergency card --}}
            <div class="card shadow" style="padding:18px; border-color:#fecaca; background:#fff8f8;">
                <div style="display:flex; align-items:flex-start; gap:12px;">
                    <div style="width:38px; height:38px; background:var(--rose-light); border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; color:var(--rose);">
                        <i class="fas fa-triangle-exclamation" style="font-size:15px;"></i>
                    </div>
                    <div>
                        <h3 style="font-size:14px; font-weight:600; color:var(--ink);">Emergency?</h3>
                        <p style="font-size:12px; color:var(--muted); margin-top:4px; line-height:1.5;">
                            Chest pain, difficulty breathing, or sudden weakness — call emergency services now.
                        </p>
                        <a href="tel:911" class="btn btn-danger btn-sm" style="margin-top:12px; display:inline-flex;">
                            <i class="fas fa-phone"></i> Call 911
                        </a>
                    </div>
                </div>
            </div>

            {{-- Tip --}}
            <div class="card shadow" style="padding:18px;">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                    <i class="fas fa-lightbulb" style="font-size:13px; color:var(--amber);"></i>
                    <h3 style="font-size:14px; font-weight:600; color:var(--ink);">Wellness tip</h3>
                </div>
                <p style="font-size:13px; color:var(--muted); line-height:1.6;">
                    Logging symptoms consistently — even small ones — helps your physician spot patterns earlier. Aim for a brief weekly check-in.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 900px) {
    .lg-grid { grid-template-columns: 1fr !important; }
}
</style>
@endsection
