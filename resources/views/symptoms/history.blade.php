@extends('layouts.app')

@section('title', 'Symptom History — Health360')
@section('header', 'Symptom History')
@section('subheader', 'All your past symptom checks in one place')

@section('header-actions')
    <a href="{{ route('symptoms.index') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus"></i> New check
    </a>
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6">

    {{-- Filters --}}
    <div class="card shadow" style="padding:14px 18px; margin-bottom:20px; display:flex; flex-wrap:wrap; align-items:center; gap:10px;">
        <div style="position:relative; flex:1; min-width:180px;">
            <i class="fas fa-search" style="position:absolute; left:11px; top:50%; transform:translateY(-50%); font-size:12px; color:#94a3b8;"></i>
            <input type="text" placeholder="Search symptoms or body region…"
                   style="width:100%; padding:8px 12px 8px 32px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; color:var(--ink); background:#fff;"
                   id="search-input">
        </div>
        <select style="padding:8px 32px 8px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; color:var(--ink); background:#fff url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%2364748b' d='M8 11L3 6h10z'/%3E%3C/svg%3E&quot;) no-repeat right 8px center / 14px; appearance:none;" id="risk-filter">
            <option value="">All risk levels</option>
            <option value="emergency">Emergency</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
        </select>
        <select style="padding:8px 32px 8px 12px; border:1px solid var(--border); border-radius:8px; font-size:13px; font-family:inherit; color:var(--ink); background:#fff url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%2364748b' d='M8 11L3 6h10z'/%3E%3C/svg%3E&quot;) no-repeat right 8px center / 14px; appearance:none;">
            <option>All time</option>
            <option>This week</option>
            <option>This month</option>
            <option>Last 3 months</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="card shadow" style="overflow:hidden;">
        @forelse($sessions as $session)
            @php
                $risk = $session->aiResponse?->ai_risk_level ?? 'low';
                $riskLabel = ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'emergency' => 'Emergency'];
            @endphp
            <div class="history-row" data-risk="{{ $risk }}" data-text="{{ strtolower($session->description ?? '') }} {{ strtolower($session->bodyRegion->name ?? '') }}"
                 style="display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 20px; border-bottom:1px solid var(--border);">
                <div style="display:flex; align-items:center; gap:12px; min-width:0; flex:1;">
                    <div style="width:38px; height:38px; border-radius:9px; background:var(--surface); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; flex-shrink:0; color:var(--muted);">
                        <i class="fas fa-location-dot" style="font-size:14px;"></i>
                    </div>
                    <div style="min-width:0;">
                        <p style="font-size:14px; font-weight:500; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            <strong>{{ $session->bodyRegion->name ?? 'Unknown' }}</strong> — {{ Str::limit($session->description ?? '', 50) }}
                        </p>
                        <p style="font-size:12px; color:var(--muted); margin-top:2px;">
                            {{ $session->created_at->format('M j, Y') }} · {{ $session->created_at->diffForHumans() }} · Pain {{ $session->pain_intensity ?? '—' }}/10
                        </p>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:6px; flex-shrink:0;">
                    <span class="chip risk-{{ $risk }}" style="font-size:11px;">
                        <span style="width:5px; height:5px; border-radius:50%; background:currentColor; display:inline-block;"></span>
                        {{ $riskLabel[$risk] ?? ucfirst($risk) }}
                    </span>
                    <a href="{{ route('symptoms.results', $session) }}" class="btn btn-outline btn-sm" style="display:inline-flex; align-items:center; gap:5px;">
                        <i class="fas fa-eye"></i> <span class="hidden-xs">View</span>
                    </a>
                </div>
            </div>
        @empty
            <div style="padding:60px 20px; text-align:center;">
                <div style="width:52px; height:52px; margin:0 auto 12px; background:var(--surface); border-radius:12px; display:flex; align-items:center; justify-content:center; color:#94a3b8;">
                    <i class="fas fa-clock-rotate-left" style="font-size:20px;"></i>
                </div>
                <p style="font-size:14px; font-weight:500; color:var(--ink);">No symptom history yet</p>
                <p style="font-size:13px; color:var(--muted); margin-top:4px;">Your completed symptom checks will appear here.</p>
                <a href="{{ route('symptoms.index') }}" class="btn btn-primary btn-sm" style="margin-top:14px; display:inline-flex;">
                    <i class="fas fa-plus"></i> Run your first check
                </a>
            </div>
        @endforelse

        @if(isset($sessions) && method_exists($sessions, 'links') && $sessions->hasPages())
            <div style="padding:14px 20px; border-top:1px solid var(--border);">
                {{ $sessions->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
const searchInput = document.getElementById('search-input');
const riskFilter = document.getElementById('risk-filter');
function filterRows() {
    const q = searchInput.value.toLowerCase();
    const r = riskFilter.value;
    document.querySelectorAll('.history-row').forEach(row => {
        const matchText = !q || row.dataset.text.includes(q);
        const matchRisk = !r || row.dataset.risk === r;
        row.style.display = matchText && matchRisk ? '' : 'none';
    });
}
searchInput.addEventListener('input', filterRows);
riskFilter.addEventListener('change', filterRows);
</script>
@endpush
@endsection
