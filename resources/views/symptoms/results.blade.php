@extends('layouts.app')

@section('title', 'Analysis Results — Health360')
@section('header', 'Analysis Results')
@section('subheader', 'AI-generated insights based on your described symptoms')

@section('header-actions')
    <a href="{{ route('symptoms.index') }}" class="btn btn-outline btn-sm">
        <i class="fas fa-plus"></i> New check
    </a>
@endsection

@section('content')
@php
    $aiResponse = $symptomEntry->aiResponse;
    $risk = $aiResponse?->ai_risk_level ?? 'low';
    $riskConfig = [
        'low'       => ['grad' => 'linear-gradient(135deg,#059669,#0a9688)', 'icon' => 'fa-face-smile',          'label' => 'Low Risk',    'msg' => 'Likely manageable at home with self-care.'],
        'medium'    => ['grad' => 'linear-gradient(135deg,#d97706,#f59e0b)', 'icon' => 'fa-face-meh',            'label' => 'Medium Risk', 'msg' => 'Consider scheduling a primary care visit.'],
        'high'      => ['grad' => 'linear-gradient(135deg,#ea580c,#dc2626)', 'icon' => 'fa-face-frown',          'label' => 'High Risk',   'msg' => 'Seek medical attention within 24 hours.'],
        'emergency' => ['grad' => 'linear-gradient(135deg,#dc2626,#9f1239)', 'icon' => 'fa-triangle-exclamation','label' => 'Emergency',   'msg' => 'Call emergency services immediately.'],
    ];
    $rc = $riskConfig[$risk] ?? $riskConfig['low'];
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 space-y-6">

    {{-- Risk hero --}}
    <div class="card shadow fade-up" style="overflow:hidden;">
        <div style="background:{{ $rc['grad'] }}; padding:28px 32px; color:#fff; position:relative; overflow:hidden;">
            <div style="position:absolute; inset:0; background-image:radial-gradient(circle at 1px 1px, rgba(255,255,255,.1) 1px, transparent 0); background-size:20px 20px; pointer-events:none;"></div>
            <div style="position:relative; display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:16px;">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div style="width:52px; height:52px; border-radius:14px; background:rgba(255,255,255,.2); backdrop-filter:blur(6px); display:flex; align-items:center; justify-content:center; font-size:22px;">
                        <i class="fas {{ $rc['icon'] }}"></i>
                    </div>
                    <div>
                        <p style="font-size:11px; opacity:.8; text-transform:uppercase; letter-spacing:.06em; margin-bottom:3px;">Assessment</p>
                        <h2 style="font-size:22px; font-weight:600; line-height:1.1;">{{ $rc['label'] }}</h2>
                        <p style="font-size:14px; opacity:.9; margin-top:3px;">{{ $rc['msg'] }}</p>
                    </div>
                </div>
                <div style="text-align:right; font-size:12px; opacity:.85;">
                    <div>Analyzed</div>
                    <div style="font-weight:500; margin-top:2px;">{{ $symptomEntry->created_at->format('M j, Y · g:i A') }}</div>
                </div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns:repeat(4,1fr); border-top:1px solid var(--border);">
            @foreach([
                ['Body Region', $symptomEntry->bodyRegion->name ?? '—'],
                ['Pain Level', ($symptomEntry->pain_intensity ?? '—') . '/10'],
                ['Duration', $symptomEntry->pain_duration ?? '—'],
                ['Risk Level', $rc['label']],
            ] as [$label, $val])
                <div style="padding:14px 16px; text-align:center; border-right:1px solid var(--border);">
                    <p style="font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--muted);">{{ $label }}</p>
                    <p style="font-size:16px; font-weight:600; color:var(--ink); margin-top:5px;">{{ $val }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 300px; gap:20px;" class="results-grid">
        <div style="display:flex; flex-direction:column; gap:16px;">

            {{-- Description --}}
            <div class="card shadow fade-up delay-1" style="padding:20px;">
                <h3 style="font-size:14px; font-weight:600; color:var(--ink); margin-bottom:12px; display:flex; align-items:center; gap:7px;">
                    <i class="fas fa-message-medical" style="color:var(--teal);"></i> Your description
                </h3>
                <p style="font-size:14px; color:#374151; line-height:1.65; background:var(--surface); padding:14px 16px; border-radius:9px; border:1px solid var(--border);">
                    {{ $symptomEntry->symptom_text }}
                </p>
            </div>

            {{-- AI analysis --}}
            <div class="card shadow fade-up delay-2" style="padding:20px;">
                <h3 style="font-size:14px; font-weight:600; color:var(--ink); margin-bottom:16px; display:flex; align-items:center; gap:7px;">
                    <i class="fas fa-brain" style="color:var(--teal);"></i> AI analysis
                </h3>

                @if($aiResponse?->possible_explanations)
                    <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:20px;">
                        @foreach($aiResponse->possible_explanations as $condition)
                            <div style="padding:14px 16px; border-radius:9px; border:1px solid var(--border); background:#fff;">
                                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px;">
                                    <div>
                                        <p style="font-size:14px; font-weight:500; color:var(--ink);">{{ $condition['name'] ?? 'Possible condition' }}</p>
                                        <p style="font-size:13px; color:var(--muted); margin-top:4px; line-height:1.5;">{{ $condition['description'] ?? '' }}</p>
                                    </div>
                                    @if(isset($condition['likelihood']))
                                        <span class="chip chip-teal" style="flex-shrink:0;">
                                            {{ round($condition['likelihood'] * 100) }}%
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="padding:16px; background:var(--surface); border-radius:9px; border:1px solid var(--border); margin-bottom:16px;">
                        <p style="font-size:13px; color:var(--muted);">No specific conditions identified. Monitor your symptoms and consult a provider if they persist.</p>
                    </div>
                @endif

                @if($aiResponse?->home_remedies)
                    <div>
                        <h4 style="font-size:13px; font-weight:600; color:var(--ink); margin-bottom:10px;">Recommended next steps</h4>
                        <div style="display:flex; flex-direction:column; gap:8px;">
                            @foreach($aiResponse->home_remedies as $remedy)
                                <div style="display:flex; align-items:flex-start; gap:9px; font-size:13px; color:#374151;">
                                    <i class="fas fa-circle-check" style="color:var(--teal); margin-top:2px; flex-shrink:0;"></i>
                                    <span>{{ $remedy }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Feedback --}}
            <div class="card shadow fade-up delay-3" style="padding:18px 20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px;">
                <p style="font-size:14px; font-weight:600; color:var(--ink);">Was this analysis helpful?</p>
                <div style="display:flex; gap:8px;">
                    <form action="{{ route('symptoms.feedback', $symptomEntry) }}" method="POST">
                        @csrf
                        <input type="hidden" name="helpful" value="1">
                        <button type="submit" class="btn btn-outline btn-sm"
                                onmouseover="this.style.background='#f0fdf4'; this.style.borderColor='#86efac'; this.style.color='#16a34a';"
                                onmouseout="this.style.background='#fff'; this.style.borderColor='var(--border)'; this.style.color='var(--ink)';">
                            <i class="fas fa-thumbs-up"></i> Yes, helpful
                        </button>
                    </form>
                    <form action="{{ route('symptoms.feedback', $symptomEntry) }}" method="POST">
                        @csrf
                        <input type="hidden" name="helpful" value="0">
                        <button type="submit" class="btn btn-outline btn-sm"
                                onmouseover="this.style.background='var(--rose-light)'; this.style.borderColor='#fca5a5'; this.style.color='var(--rose)';"
                                onmouseout="this.style.background='#fff'; this.style.borderColor='var(--border)'; this.style.color='var(--ink)';">
                            <i class="fas fa-thumbs-down"></i> Not helpful
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div style="display:flex; flex-direction:column; gap:16px;">
            @if($risk === 'emergency' || $risk === 'high')
                <div class="card shadow fade-up" style="padding:18px; background:#fff8f8; border-color:#fecaca;">
                    <div style="display:flex; align-items:flex-start; gap:12px;">
                        <div style="width:38px; height:38px; background:var(--rose); border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas fa-truck-medical" style="color:#fff; font-size:15px;"></i>
                        </div>
                        <div>
                            <h3 style="font-size:14px; font-weight:600; color:#7f1d1d;">Seek care now</h3>
                            <p style="font-size:12px; color:#991b1b; margin-top:4px; line-height:1.5;">Based on your symptoms, please don't wait — get medical attention.</p>
                            <a href="tel:911" class="btn btn-danger btn-sm" style="margin-top:12px; display:inline-flex;">
                                <i class="fas fa-phone"></i> Call emergency
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card shadow fade-up delay-1" style="padding:18px;">
                <h3 style="font-size:14px; font-weight:600; color:var(--ink);">Find care nearby</h3>
                <p style="font-size:12px; color:var(--muted); margin-top:2px; margin-bottom:14px;">Doctors matching this concern</p>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <a href="#" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:9px; border:1px solid var(--border); transition:background .12s;"
                       onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='transparent'">
                        <div style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,var(--teal),#4db6ac); display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:600; flex-shrink:0;">DR</div>
                        <div>
                            <p style="font-size:13px; font-weight:500; color:var(--ink);">Dr. Reyes, General Medicine</p>
                            <p style="font-size:11px; color:var(--muted);">Earliest: Tomorrow 9:30 AM</p>
                        </div>
                    </a>
                    <a href="#" style="display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:9px; border:1px solid var(--border); transition:background .12s;"
                       onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='transparent'">
                        <div style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#3b82f6,#6366f1); display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:600; flex-shrink:0;">TK</div>
                        <div>
                            <p style="font-size:13px; font-weight:500; color:var(--ink);">Dr. Tanaka, Internist</p>
                            <p style="font-size:11px; color:var(--muted);">Telehealth · Today 4:00 PM</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="card shadow fade-up delay-2" style="padding:18px;">
                <h3 style="font-size:14px; font-weight:600; color:var(--ink); margin-bottom:4px;">Share with your doctor</h3>
                <p style="font-size:12px; color:var(--muted); margin-bottom:14px;">Export this assessment as PDF</p>
                <button class="btn btn-outline" style="width:100%; justify-content:center;">
                    <i class="fas fa-file-pdf" style="color:var(--rose);"></i> Download report
                </button>
            </div>

            <div class="card shadow fade-up delay-3" style="padding:18px;">
                <h3 style="font-size:14px; font-weight:600; color:var(--ink); margin-bottom:10px;">Risk factors noted</h3>
                @if($aiResponse?->risk_factors)
                    <div style="display:flex; flex-direction:column; gap:6px;">
                        @foreach($aiResponse->risk_factors as $rf)
                            <div style="display:flex; align-items:center; gap:7px; font-size:13px; color:#374151;">
                                <i class="fas fa-circle-dot" style="font-size:9px; color:var(--amber);"></i> {{ $rf }}
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="font-size:13px; color:var(--muted);">No specific risk factors flagged.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 860px) {
    .results-grid { grid-template-columns: 1fr !important; }
}
</style>
@endsection
