@extends('layouts.app')

@section('title', 'Review Case — Health360')
@section('header', 'Review Case')
@section('subheader', 'Validate or modify the AI-generated assessment')

@section('header-actions')
    <a href="{{ route('doctor.dashboard') }}" class="btn btn-outline btn-sm">
        <i class="fas fa-arrow-left"></i> Back to portal
    </a>
@endsection

@section('content')
@php
    $risk = $aiResponse->ai_risk_level ?? 'low';
    $riskConfig = [
        'low'       => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#bbf7d0'],
        'medium'    => ['bg' => '#fff3e0', 'text' => '#b45309', 'border' => '#fed7aa'],
        'high'      => ['bg' => '#fff7ed', 'text' => '#c2410c', 'border' => '#fdba74'],
        'emergency' => ['bg' => '#fff8f8', 'text' => '#b91c1c', 'border' => '#fecaca'],
    ];
    $rc = $riskConfig[$risk] ?? $riskConfig['low'];
    $patient = $aiResponse->symptomEntry->user;
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6">
    <div style="display:grid; grid-template-columns:1fr 320px; gap:20px;" class="review-grid">

        <div style="display:flex; flex-direction:column; gap:16px;">

            {{-- Patient info --}}
            <div class="card shadow fade-up" style="padding:20px;">
                <h3 style="font-size:14px; font-weight:600; color:var(--ink); margin-bottom:14px; display:flex; align-items:center; gap:7px;">
                    <i class="fas fa-user-injured" style="color:var(--teal);"></i> Patient information
                </h3>
                <div style="display:flex; align-items:center; gap:14px; margin-bottom:16px;">
                    <div style="width:48px; height:48px; border-radius:50%; background:linear-gradient(135deg,var(--teal),#4db6ac); display:flex; align-items:center; justify-content:center; font-size:16px; font-weight:600; color:#fff; flex-shrink:0;">
                        {{ strtoupper(substr($patient->name ?? 'P', 0, 1)) }}
                    </div>
                    <div>
                        <p style="font-size:15px; font-weight:600; color:var(--ink);">{{ $patient->name }}</p>
                        <p style="font-size:12px; color:var(--muted); margin-top:2px;">{{ $patient->email }}</p>
                    </div>
                </div>
                            <div class="patient-details-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:13px;">
                    <div>
                        <span style="color:var(--muted);">Age</span>
                        <p style="font-weight:500; color:var(--ink); margin-top:2px;">{{ $patient->age ?? '—' }} years old</p>
                    </div>
                    <div>
                        <span style="color:var(--muted);">Blood type</span>
                        <p style="font-weight:500; color:var(--ink); margin-top:2px;">{{ $patient->blood_type ?? '—' }}</p>
                    </div>
                    @if($patient->medicalProfile)
                        <div class="patient-conditions" style="grid-column:span 2;">
                            <span style="color:var(--muted);">Pre-existing conditions</span>
                            <div style="margin-top:4px; display:flex; flex-wrap:wrap; gap:5px;">
                                @foreach($patient->medicalProfile->getCriticalConditionsList() as $cond)
                                    <span class="chip chip-rose" style="font-size:11px;">{{ $cond }}</span>
                                @endforeach
                                @if(empty($patient->medicalProfile->getCriticalConditionsList()))
                                    <span style="color:var(--muted);">None reported</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- AI response --}}
            <div class="card shadow fade-up delay-1" style="padding:20px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px;">
                    <h3 style="font-size:14px; font-weight:600; color:var(--ink); display:flex; align-items:center; gap:7px;">
                        <i class="fas fa-brain" style="color:var(--teal);"></i> AI assessment
                    </h3>
                    <span class="chip" style="font-size:11px; background:{{ $rc['bg'] }}; color:{{ $rc['text'] }}; border:1px solid {{ $rc['border'] }};">
                        {{ ucfirst($risk) }} risk
                    </span>
                </div>

                <div style="background:var(--surface); border-radius:9px; border:1px solid var(--border); padding:14px 16px; margin-bottom:14px;">
                    <p style="font-size:13px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.04em; margin-bottom:6px;">Symptom described</p>
                    <p style="font-size:14px; color:var(--ink); line-height:1.6;">{{ $aiResponse->symptomEntry->description }}</p>
                </div>

                @if($aiResponse->possible_explanations)
                    <div style="margin-bottom:14px;">
                        <p style="font-size:13px; font-weight:600; color:var(--ink); margin-bottom:8px;">Possible explanations</p>
                        <div style="display:flex; flex-direction:column; gap:8px;">
                            @foreach($aiResponse->possible_explanations as $exp)
                                <div style="padding:10px 12px; border-radius:8px; border:1px solid var(--border); font-size:13px; color:#374151;">
                                    <strong>{{ $exp['name'] ?? '' }}</strong>
                                    @if(!empty($exp['description'])) — {{ $exp['description'] }} @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($aiResponse->home_remedies)
                    <div>
                        <p style="font-size:13px; font-weight:600; color:var(--ink); margin-bottom:8px;">AI-suggested remedies</p>
                        <div style="display:flex; flex-direction:column; gap:6px;">
                            @foreach($aiResponse->home_remedies as $remedy)
                                <div style="display:flex; align-items:flex-start; gap:8px; font-size:13px; color:#374151;">
                                    <i class="fas fa-circle-check" style="color:var(--teal); margin-top:2px; flex-shrink:0; font-size:12px;"></i>
                                    {{ $remedy }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Review form --}}
            <div class="card shadow fade-up delay-2" style="padding:20px;">
                <h3 style="font-size:14px; font-weight:600; color:var(--ink); margin-bottom:16px; display:flex; align-items:center; gap:7px;">
                    <i class="fas fa-stethoscope" style="color:var(--teal);"></i> Your review
                </h3>

                <form action="{{ route('doctor.approve', $aiResponse) }}" method="POST" style="display:flex; flex-direction:column; gap:16px;">
                    @csrf

                    <div>
                        <label class="form-label">Review decision</label>
                    <div class="review-decision-grid" style="display:grid; grid-template-columns:repeat(4,1fr); gap:8px;">
                        @foreach(['approved' => ['icon' => 'fa-check', 'label' => 'Approve', 'color' => '#16a34a'], 'modified' => ['icon' => 'fa-pencil', 'label' => 'Modify', 'color' => '#2563eb'], 'rejected' => ['icon' => 'fa-xmark', 'label' => 'Reject', 'color' => 'var(--rose)'], 'flagged_for_human' => ['icon' => 'fa-flag', 'label' => 'Flag', 'color' => 'var(--amber)']] as $val => $cfg)
                                <label style="cursor:pointer;">
                                    <input type="radio" name="review_decision" value="{{ $val }}" style="display:none;" class="dec-radio">
                                    <div class="dec-opt" data-color="{{ $cfg['color'] }}"
                                         style="text-align:center; padding:10px 6px; border-radius:8px; border:1px solid var(--border); background:#fff; cursor:pointer; transition:all .12s;">
                                        <i class="fas {{ $cfg['icon'] }}" style="font-size:15px; margin-bottom:4px; display:block; color:var(--muted);"></i>
                                        <span style="font-size:12px; font-weight:500; color:var(--muted);">{{ $cfg['label'] }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Modified risk level <span style="font-weight:400; color:#94a3b8;">(if different from AI)</span></label>
                        <select name="modified_risk_level" class="form-input">
                            <option value="">Keep AI assessment ({{ ucfirst($risk) }})</option>
                            <option value="low">Low risk</option>
                            <option value="medium">Medium risk</option>
                            <option value="high">High risk</option>
                            <option value="emergency">Emergency</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Review notes</label>
                        <textarea name="review_notes" class="form-input" rows="3" placeholder="Add your clinical notes here…"></textarea>
                    </div>

                    <div>
                        <label class="form-label">Modified advice <span style="font-weight:400; color:#94a3b8;">(optional)</span></label>
                        <textarea name="modified_advice" class="form-input" rows="3" placeholder="Provide alternative or additional recommendations…"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg" style="justify-content:center;">
                        <i class="fas fa-paper-plane"></i> Submit review
                    </button>
                </form>
            </div>
        </div>

        {{-- Sidebar --}}
        <div style="display:flex; flex-direction:column; gap:16px;">
            <div class="card shadow fade-up" style="padding:18px;">
                <h3 style="font-size:14px; font-weight:600; color:var(--ink); margin-bottom:12px;">Session details</h3>
                @foreach([
                    ['Body region', $aiResponse->symptomEntry->bodyRegion->name ?? '—'],
                    ['Pain level', ($aiResponse->symptomEntry->pain_intensity ?? '—') . '/10'],
                    ['Duration', $aiResponse->symptomEntry->duration ?? '—'],
                    ['Submitted', $aiResponse->created_at->format('M j, Y g:i A')],
                ] as [$k, $v])
                    <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--border); font-size:13px;">
                        <span style="color:var(--muted);">{{ $k }}</span>
                        <span style="font-weight:500; color:var(--ink);">{{ $v }}</span>
                    </div>
                @endforeach
            </div>

            @if($aiResponse->risk_factors)
                <div class="card shadow fade-up delay-1" style="padding:18px;">
                    <h3 style="font-size:14px; font-weight:600; color:var(--ink); margin-bottom:12px;">
                        <i class="fas fa-triangle-exclamation" style="color:var(--amber); margin-right:5px;"></i> Risk factors
                    </h3>
                    @foreach($aiResponse->risk_factors as $rf)
                        <div style="display:flex; align-items:center; gap:7px; padding:6px 0; font-size:13px; color:#374151; border-bottom:1px solid var(--border);">
                            <i class="fas fa-circle-dot" style="font-size:9px; color:var(--amber);"></i> {{ $rf }}
                        </div>
                    @endforeach
                </div>
            @endif

            @if($patient->medicalProfile?->current_medications)
                <div class="card shadow fade-up delay-2" style="padding:18px;">
                    <h3 style="font-size:14px; font-weight:600; color:var(--ink); margin-bottom:12px;">
                        <i class="fas fa-pills" style="color:var(--teal); margin-right:5px;"></i> Current medications
                    </h3>
                    @foreach($patient->medicalProfile->current_medications as $med)
                        <div style="font-size:13px; color:#374151; padding:5px 0; border-bottom:1px solid var(--border);">{{ $med }}</div>
                    @endforeach
                </div>
            @endif

            <div class="card shadow fade-up delay-3" style="padding:18px; background:var(--rose-light); border-color:#fecaca;">
                <p style="font-size:12px; color:#7f1d1d; font-weight:600; margin-bottom:6px;">⚠ Reminder</p>
                <p style="font-size:12px; color:#991b1b; line-height:1.5;">Your review will be permanently associated with this case and visible to the patient's care team.</p>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 860px) {
    .review-grid { grid-template-columns: 1fr !important; }
}
@media (max-width: 640px) {
    .review-decision-grid { grid-template-columns: repeat(2,1fr) !important; }
}
</style>

@push('scripts')
<script>
document.querySelectorAll('.dec-radio').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.dec-opt').forEach(opt => {
            opt.style.background = '#fff';
            opt.style.borderColor = 'var(--border)';
            opt.querySelector('i').style.color = 'var(--muted)';
            opt.querySelector('span').style.color = 'var(--muted)';
        });
        const selected = radio.nextElementSibling;
        const color = selected.dataset.color;
        selected.style.background = 'var(--surface)';
        selected.style.borderColor = color;
        selected.querySelector('i').style.color = color;
        selected.querySelector('span').style.color = color;
    });
});
</script>
@endpush
@endsection
