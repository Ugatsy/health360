@extends('layouts.app')

@section('title', 'Symptom Checker — Health360')
@section('header', 'Symptom Checker')
@section('subheader', 'Tell us where it hurts — we\'ll do a clinically-informed first pass.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6">

    {{-- Stepper --}}
    <div class="symptom-stepper" style="display:flex; align-items:center; gap:6px; margin-bottom:24px;">
        @php
            $stepNumbers = ['1', '2', '3'];
            $stepLabels = ['Locate', 'Describe', 'Review'];
        @endphp

        @for($i = 0; $i < 3; $i++)
            @if($i > 0)
                <div style="flex:1; max-width:40px; height:1px; background:{{ $i === 1 ? 'var(--teal)' : 'var(--border)' }};"></div>
            @endif
            <div style="display:flex; align-items:center; gap:7px;">
                <div style="width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:600; flex-shrink:0; background:{{ $i === 0 ? 'var(--teal)' : 'var(--surface)' }}; color:{{ $i === 0 ? '#fff' : 'var(--muted)' }}; border:1px solid {{ $i === 0 ? 'var(--teal)' : 'var(--border)' }};">
                    {{ $stepNumbers[$i] }}
                </div>
                <span style="font-size:13px; font-weight:{{ $i === 0 ? '600' : '400' }}; color:{{ $i === 0 ? 'var(--teal-dark)' : 'var(--muted)' }};">
                    {{ $stepLabels[$i] }}
                </span>
            </div>
        @endfor
    </div>

    <div style="display:grid; grid-template-columns:1fr 380px; gap:20px;" class="symptom-grid">

        {{-- Body Map --}}
        <div class="card shadow" style="overflow:hidden;">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <h3 style="font-size:15px; font-weight:600; color:var(--ink);">
                        <i class="fas fa-person" style="color:var(--teal); margin-right:6px;"></i> Interactive body map
                    </h3>
                    <p style="font-size:12px; color:var(--muted); margin-top:1px;">Click the area where you feel symptoms <span style="color:var(--rose);">*</span></p>
                </div>
                <button id="reset-btn" style="font-size:12px; font-weight:500; color:var(--muted); background:none; border:1px solid var(--border); border-radius:6px; padding:5px 10px; cursor:pointer; display:flex; align-items:center; gap:5px;"
                        onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='none'">
                    <i class="fas fa-rotate-left" style="font-size:11px;"></i> Reset
                </button>
            </div>

            <div style="padding:24px; background:linear-gradient(180deg,#f8fafb 0%,#fff 100%);">
                <svg viewBox="0 0 340 520" style="width:100%; max-width:260px; margin:0 auto; display:block; cursor:pointer; user-select:none;" id="body-svg">
                    <defs>
                        <filter id="sel-glow">
                            <feGaussianBlur in="SourceAlpha" stdDeviation="3" result="blur"/>
                            <feFlood flood-color="#0a9688" flood-opacity="0.3" result="color"/>
                            <feComposite in="color" in2="blur" operator="in" result="shadow"/>
                            <feMerge><feMergeNode in="shadow"/><feMergeNode in="SourceGraphic"/></feMerge>
                        </filter>
                    </defs>

                    {{-- Neck --}}
                    <rect x="152" y="83" width="36" height="22" rx="8"
                          class="body-region" data-id="2" data-name="Neck" fill="#eef0f3" stroke="#dde0e6" stroke-width="1.5"/>
                    {{-- Head --}}
                    <circle cx="170" cy="55" r="34"
                            class="body-region" data-id="1" data-name="Head" fill="#eef0f3" stroke="#dde0e6" stroke-width="1.5"/>
                    {{-- Chest --}}
                    <rect x="112" y="105" width="116" height="112" rx="14"
                          class="body-region" data-id="3" data-name="Chest" fill="#eef0f3" stroke="#dde0e6" stroke-width="1.5"/>
                    {{-- Abdomen --}}
                    <rect x="117" y="215" width="106" height="88" rx="10"
                          class="body-region" data-id="4" data-name="Abdomen" fill="#eef0f3" stroke="#dde0e6" stroke-width="1.5"/>
                    {{-- Left arm --}}
                    <path d="M112 118 Q82 160 75 220 Q73 240 72 260" stroke-width="26" stroke-linecap="round" fill="none"
                          class="body-region" data-id="8" data-name="Left Arm" stroke="#dde0e6"/>
                    {{-- Right arm --}}
                    <path d="M228 118 Q258 160 265 220 Q267 240 268 260" stroke-width="26" stroke-linecap="round" fill="none"
                          class="body-region" data-id="9" data-name="Right Arm" stroke="#dde0e6"/>
                    {{-- Left leg --}}
                    <path d="M148 302 Q142 380 138 430 Q136 460 135 490" stroke-width="28" stroke-linecap="round" fill="none"
                          class="body-region" data-id="14" data-name="Left Leg" stroke="#dde0e6"/>
                    {{-- Right leg --}}
                    <path d="M192 302 Q198 380 202 430 Q204 460 205 490" stroke-width="28" stroke-linecap="round" fill="none"
                          class="body-region" data-id="15" data-name="Right Leg" stroke="#dde0e6"/>

                    {{-- Labels --}}
                    <text x="170" y="58" text-anchor="middle" font-size="9" fill="#94a3b8" pointer-events="none">Head</text>
                    <text x="170" y="165" text-anchor="middle" font-size="9" fill="#94a3b8" pointer-events="none">Chest</text>
                    <text x="170" y="262" text-anchor="middle" font-size="9" fill="#94a3b8" pointer-events="none">Abdomen</text>
                </svg>

                <div id="region-display" style="margin-top:16px; text-align:center; font-size:13px; color:var(--muted); padding:10px; background:var(--surface); border-radius:8px; border:1px solid var(--border);">
                    <i class="fas fa-hand-pointer" style="margin-right:5px; color:#94a3b8;"></i>
                    Select a body region above to continue <span style="color:var(--rose);">*</span>
                </div>
            </div>

            <style>
                .body-region { transition: fill .12s, stroke .12s; cursor: pointer; }
                .body-region:not([stroke-linecap]):hover { fill: #d0f5f0; stroke: var(--teal); }
                .body-region:not([stroke-linecap]).selected { fill: var(--teal) !important; stroke: var(--teal-dark) !important; filter: url(#sel-glow); }
                [stroke-linecap="round"].body-region:hover { stroke: var(--teal-mid); }
                [stroke-linecap="round"].body-region.selected { stroke: var(--teal) !important; }
                @media (max-width: 860px) {
                    .symptom-grid { grid-template-columns: 1fr !important; }
                }
                @media (max-width: 480px) {
                    .symptom-stepper { font-size: 12px; gap: 4px !important; flex-wrap: wrap; justify-content: center; }
                    .symptom-stepper > div { margin-bottom: 4px; }
                    .duration-grid { grid-template-columns: 1fr 1fr !important; }
                }
                input[type="radio"].peer:checked + .duration-opt {
                    background: var(--teal-light); border-color: var(--teal); color: var(--teal-dark); font-weight: 500;
                }
                .acchk:checked + .acc-tag {
                    background: var(--teal-light); border-color: var(--teal); color: var(--teal-dark);
                }
            </style>
        </div>

        {{-- Form --}}
        <div class="card shadow" style="overflow:hidden; display:flex; flex-direction:column;">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border);">
                <h3 style="font-size:15px; font-weight:600; color:var(--ink);">
                    <i class="fas fa-pen-to-square" style="color:var(--teal); margin-right:6px;"></i> Describe your symptoms
                </h3>
                <p style="font-size:12px; color:var(--muted); margin-top:1px;">Be specific — severity, duration, triggers</p>
            </div>

            <form action="{{ route('symptoms.store') }}" method="POST" style="padding:20px; display:flex; flex-direction:column; gap:18px; flex:1;" id="symptom-form">
                @csrf
                <input type="hidden" name="session_id" value="{{ $session->id }}">
                <input type="hidden" name="body_region_id" id="body_region_id" required>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <i class="fas fa-circle-xmark"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <div>
                    <label class="form-label">What are you feeling?</label>
<textarea name="symptom_text" rows="4" required
          class="form-input"
          placeholder="e.g. Dull throbbing pain behind my right eye since this morning, worse when I bend over…">{{ old('symptom_text') }}</textarea>                </div>

                <div>
                    <label class="form-label" style="display:flex; justify-content:space-between;">
                        <span>Pain intensity</span>
                        <strong id="pain-label" style="color:var(--teal);">5 / 10</strong>
                    </label>
                    <input type="range" name="pain_intensity" min="0" max="10" value="5" id="pain-slider"
                           style="width:100%; accent-color:var(--teal); height:4px; margin:6px 0;">
                    <div style="display:flex; justify-content:space-between; font-size:11px; color:#94a3b8;">
                        <span>None</span><span>Mild</span><span>Moderate</span><span>Severe</span><span>Worst</span>
                    </div>
                </div>

                <div>
                    <label class="form-label">Duration</label>
                    <div class="duration-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                        @foreach(['< 1 hour' => 'lt_1hr', 'Today' => 'today', 'Few days' => 'few_days', 'Over a week' => 'week_plus'] as $label => $val)
                            <label style="cursor:pointer;">
                                <input type="radio" name="duration" value="{{ $val }}" class="peer" style="display:none;" {{ old('duration') === $val ? 'checked' : '' }}>
                                <div class="duration-opt" style="text-align:center; font-size:13px; padding:8px 6px; border-radius:8px; border:1px solid var(--border); background:#fff; transition:all .12s; cursor:pointer;">
                                    {{ $label }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="form-label">Accompanying symptoms <span style="font-weight:400; color:#94a3b8;">(optional)</span></label>
                    <div style="display:flex; flex-wrap:wrap; gap:7px;">
                        @foreach(['Fever','Nausea','Dizziness','Fatigue','Shortness of breath','Swelling','Chills','Headache'] as $s)
                            <label style="cursor:pointer;">
                                <input type="checkbox" name="additional_symptoms[]" value="{{ $s }}" style="display:none;"
                                       class="acchk" {{ in_array($s, old('additional_symptoms', [])) ? 'checked' : '' }}>
                                <span class="acc-tag" style="display:inline-flex; align-items:center; padding:5px 12px; border-radius:999px; border:1px solid var(--border); font-size:12px; color:var(--muted); background:#fff; cursor:pointer; transition:all .12s;">
                                    {{ $s }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div style="margin-top:auto; padding-top:16px; border-top:1px solid var(--border);">
                    <button type="submit" class="btn btn-primary btn-lg" style="width:100%; justify-content:center;">
                        <i class="fas fa-wand-magic-sparkles"></i> Analyze with AI
                    </button>
                    <p style="margin-top:10px; font-size:11px; color:#94a3b8; text-align:center;">
                        For informational purposes only. Not a substitute for medical advice.
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const svg = document.getElementById('body-svg');
    const regions = svg.querySelectorAll('.body-region');
    const display = document.getElementById('region-display');
    const input = document.getElementById('body_region_id');
    const form = document.getElementById('symptom-form');

    // Track if a region is selected
    let isRegionSelected = false;

    // Add click handlers to each body region
    regions.forEach(r => {
        r.addEventListener('click', () => {
            regions.forEach(x => x.classList.remove('selected'));
            r.classList.add('selected');
            input.value = r.dataset.id;
            isRegionSelected = true;
            display.innerHTML = `<i class="fas fa-check-circle" style="color:var(--teal); margin-right:5px;"></i> Selected: <strong>${r.dataset.name}</strong>`;
            display.style.background = 'var(--teal-light)';
            display.style.borderColor = 'var(--teal-mid)';
            display.style.color = 'var(--teal-dark)';
        });
    });

    // Reset button handler
    const resetBtn = document.getElementById('reset-btn');
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            regions.forEach(x => x.classList.remove('selected'));
            input.value = '';
            isRegionSelected = false;
            display.innerHTML = '<i class="fas fa-hand-pointer" style="margin-right:5px; color:#94a3b8;"></i> Select a body region above to continue <span style="color:var(--rose);">*</span>';
            display.style.background = 'var(--surface)';
            display.style.borderColor = 'var(--border)';
            display.style.color = 'var(--muted)';
        });
    }

    // Form validation - prevent submission if no body region selected
    if (form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        form.addEventListener('submit', (e) => {
            if (!isRegionSelected && !input.value) {
                e.preventDefault();
                display.innerHTML = '<i class="fas fa-exclamation-triangle" style="color:var(--rose); margin-right:5px;"></i> Please select a body region first';
                display.style.background = 'var(--rose-light)';
                display.style.borderColor = 'var(--rose)';
                display.style.color = 'var(--rose)';
                display.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing…';
            }
        });
    }

    // Pain slider handler
    const slider = document.getElementById('pain-slider');
    const label = document.getElementById('pain-label');
    if (slider && label) {
        slider.addEventListener('input', () => label.textContent = slider.value + ' / 10');
    }

    // Accompanying symptoms checkbox toggle
    document.querySelectorAll('.acchk').forEach(chk => {
        chk.addEventListener('change', () => {
            const tag = chk.nextElementSibling;
            if (chk.checked) {
                tag.style.background = 'var(--teal-light)';
                tag.style.borderColor = 'var(--teal)';
                tag.style.color = 'var(--teal-dark)';
            } else {
                tag.style.background = '#fff';
                tag.style.borderColor = 'var(--border)';
                tag.style.color = 'var(--muted)';
            }
        });
        // Apply old state on load if checkbox was checked
        if (chk.checked) {
            const tag = chk.nextElementSibling;
            tag.style.background = 'var(--teal-light)';
            tag.style.borderColor = 'var(--teal)';
            tag.style.color = 'var(--teal-dark)';
        }
    });
});
</script>
@endpush
