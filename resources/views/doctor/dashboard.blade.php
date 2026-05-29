@extends('layouts.app')

@section('title', 'Doctor Portal — Health360')
@section('header', 'Doctor Portal')
@section('subheader', 'Review AI assessments and monitor patient activity')

@section('header-actions')
    <span class="h360-chip bg-slate-100 text-slate-700">
        <i class="fas fa-id-card text-slate-400"></i>
        License: {{ auth()->user()->doctor_license_number ?? 'Not set' }}
    </span>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    {{-- Stats --}}
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="h360-card h360-shadow p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Pending reviews</p>
                    <p class="mt-1 text-3xl font-semibold text-slate-900">{{ $stats['pending'] }}</p>
                    <p class="mt-2 text-xs text-slate-500">Awaiting physician sign-off</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 ring-1 ring-amber-100 flex items-center justify-center">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
        </div>

        <div class="h360-card h360-shadow p-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-slate-500">Total reviewed</p>
                    <p class="mt-1 text-3xl font-semibold text-slate-900">{{ $stats['total_reviews'] }}</p>
                    <p class="mt-2 text-xs text-emerald-600"><i class="fas fa-arrow-trend-up"></i> +8% this week</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100 flex items-center justify-center">
                    <i class="fas fa-circle-check"></i>
                </div>
            </div>
        </div>

        <div class="h360-card h360-shadow p-5 bg-gradient-to-br from-rose-50 to-white border-rose-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-rose-700">Emergency cases</p>
                    <p class="mt-1 text-3xl font-semibold text-rose-900">{{ $stats['emergency_cases'] }}</p>
                    <p class="mt-2 text-xs text-rose-700/80">Flagged for urgent review</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center">
                    <i class="fas fa-truck-medical"></i>
                </div>
            </div>
        </div>
    </section>

    {{-- Pending list --}}
    <section class="h360-card h360-shadow overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-[var(--h360-border)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <h3 class="font-semibold text-slate-900">Pending AI response reviews</h3>
                <p class="text-xs text-slate-500 mt-0.5">Validate or correct AI-generated assessments</p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                <div class="relative flex-1 sm:flex-none">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" placeholder="Search…"
                           class="w-full sm:w-48 pl-8 pr-3 py-2 rounded-lg border border-[var(--h360-border)] text-sm h360-ring-focus bg-white">
                </div>
                <select class="px-3 py-2 rounded-lg border border-[var(--h360-border)] text-sm bg-white w-full sm:w-auto">
                    <option>All risk levels</option>
                    <option>Emergency</option>
                    <option>High</option>
                    <option>Medium</option>
                    <option>Low</option>
                </select>
            </div>
        </div>

        <div class="divide-y divide-[var(--h360-border)]">
            @forelse($pendingReviews as $review)
                @php
                    $risk = $review->ai_risk_level ?? 'low';
                    $riskMap = [
                        'low'       => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500'],
                        'medium'    => ['bg' => 'bg-amber-50',   'text' => 'text-amber-700',   'dot' => 'bg-amber-500'],
                        'high'      => ['bg' => 'bg-orange-50',  'text' => 'text-orange-700',  'dot' => 'bg-orange-500'],
                        'emergency' => ['bg' => 'bg-rose-50',    'text' => 'text-rose-700',    'dot' => 'bg-rose-500'],
                    ];
                    $rc = $riskMap[$risk] ?? $riskMap['low'];
                @endphp
                <div class="p-4 sm:p-5 hover:bg-slate-50/70 transition">
                    <div class="flex flex-wrap items-start justify-between gap-3 sm:gap-4">
                        <div class="flex items-start gap-3 sm:gap-4 min-w-0 flex-1">
                            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-gradient-to-br from-slate-200 to-slate-300 text-slate-700 flex items-center justify-center font-semibold shrink-0">
                                {{ strtoupper(substr($review->symptomEntry->user->name ?? 'P', 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center flex-wrap gap-1.5 sm:gap-2">
                                    <span class="font-semibold text-slate-900 text-sm sm:text-base">{{ $review->symptomEntry->user->name }}</span>
                                    <span class="h360-chip {{ $rc['bg'] }} {{ $rc['text'] }} text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $rc['dot'] }}"></span>
                                        {{ ucfirst($risk) }}
                                    </span>
                                    <span class="text-xs text-slate-400">· {{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="mt-1 text-sm text-slate-700">
                                    <i class="fas fa-location-dot text-slate-400 mr-1 text-xs"></i>
                                    <strong>{{ $review->symptomEntry->bodyRegion->name }}</strong> —
                                    {{ Str::limit($review->symptomEntry->description, 100) }}
                                </p>
                                <p class="mt-2 text-xs text-slate-500 bg-slate-50 rounded-lg px-2 sm:px-3 py-2 border border-[var(--h360-border)]">
                                    <i class="fas fa-brain text-[var(--h360-primary)] mr-1"></i>
                                    <strong>AI:</strong> {{ Str::limit($review->ai_summary, 120) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('doctor.review', $review) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg h360-btn-primary text-sm font-medium">
                                <i class="fas fa-eye"></i> <span class="hidden-xs">Review</span>
                            </a>
                            <button class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-[var(--h360-border)] hover:bg-slate-50 text-sm">
                                <i class="fas fa-ellipsis"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-16 text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 mb-3">
                        <i class="fas fa-check-double text-xl"></i>
                    </div>
                    <p class="text-sm font-medium text-slate-700">You’re all caught up</p>
                    <p class="text-xs text-slate-500 mt-1">No pending reviews right now.</p>
                </div>
            @endforelse
        </div>

        @if(isset($pendingReviews) && method_exists($pendingReviews, 'links'))
            <div class="px-6 py-4 border-t border-[var(--h360-border)]">
                {{ $pendingReviews->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
