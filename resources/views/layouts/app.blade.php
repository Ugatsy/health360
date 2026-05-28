<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Health360')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600&family=instrument-serif:400i&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --teal: #0a9688;
            --teal-dark: #00796b;
            --teal-light: #e0f2f1;
            --teal-mid: #4db6ac;
            --rose: #e53935;
            --rose-light: #ffebee;
            --amber: #f57c00;
            --amber-light: #fff3e0;
            --surface: #f7f9fc;
            --card: #ffffff;
            --border: #e8ecf0;
            --ink: #1a2332;
            --muted: #64748b;
            --radius: 12px;
            --radius-sm: 8px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { font-family: 'DM Sans', ui-sans-serif, sans-serif; font-size: 15px; }
        body { background: var(--surface); color: var(--ink); min-height: 100vh; display: flex; flex-direction: column; }
        a { color: inherit; text-decoration: none; }

        /* Cards */
        .card { background: var(--card); border: 1px solid var(--border); border-radius: var(--radius); }
        .card-sm { border-radius: var(--radius-sm); }
        .shadow { box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 8px 24px -8px rgba(0,0,0,.08); }

        /* Chips */
        .chip { display: inline-flex; align-items: center; gap: 5px; padding: 3px 10px; border-radius: 999px; font-size: 12px; font-weight: 500; }
        .chip-teal { background: var(--teal-light); color: var(--teal-dark); }
        .chip-rose { background: var(--rose-light); color: var(--rose); }
        .chip-amber { background: var(--amber-light); color: var(--amber); }
        .chip-slate { background: #f1f5f9; color: #475569; }
        .chip-green { background: #f0fdf4; color: #16a34a; }

        /* Risk colors */
        .risk-low { background: #f0fdf4; color: #15803d; border-color: #bbf7d0; }
        .risk-medium { background: var(--amber-light); color: var(--amber); border-color: #fed7aa; }
        .risk-high { background: #fff7ed; color: #c2410c; border-color: #fdba74; }
        .risk-emergency { background: var(--rose-light); color: var(--rose); border-color: #fecaca; }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 7px; padding: 9px 18px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 500; cursor: pointer; transition: all .15s; border: 1px solid transparent; }
        .btn-primary { background: var(--teal); color: #fff; }
        .btn-primary:hover { background: var(--teal-dark); }
        .btn-outline { background: #fff; border-color: var(--border); color: var(--ink); }
        .btn-outline:hover { background: var(--surface); }
        .btn-danger { background: var(--rose); color: #fff; }
        .btn-danger:hover { background: #c62828; }
        .btn-sm { padding: 6px 14px; font-size: 13px; }
        .btn-lg { padding: 12px 24px; font-size: 15px; border-radius: var(--radius); }

        /* Form elements */
        .form-label { display: block; font-size: 13px; font-weight: 500; color: var(--muted); margin-bottom: 6px; }
        .form-input { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-family: inherit; font-size: 14px; color: var(--ink); background: #fff; transition: border-color .15s, box-shadow .15s; }
        .form-input:focus { outline: none; border-color: var(--teal); box-shadow: 0 0 0 3px rgba(10,150,136,.12); }
        .form-input::placeholder { color: #94a3b8; }
        textarea.form-input { resize: vertical; min-height: 100px; }
        select.form-input { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%2364748b' d='M8 11L3 6h10z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; background-size: 16px; padding-right: 36px; }

        /* Page header */
        .page-header { background: #fff; border-bottom: 1px solid var(--border); padding: 20px 0; }
        .page-title { font-size: 20px; font-weight: 600; color: var(--ink); }
        .page-sub { font-size: 13px; color: var(--muted); margin-top: 2px; }

        /* Nav */
        .nav-link { display: inline-flex; align-items: center; gap: 7px; padding: 7px 12px; border-radius: var(--radius-sm); font-size: 14px; font-weight: 500; color: var(--muted); transition: all .15s; }
        .nav-link:hover { background: var(--surface); color: var(--ink); }
        .nav-link.active { background: var(--teal-light); color: var(--teal-dark); }

        /* Divider */
        .divider { border: none; border-top: 1px solid var(--border); margin: 16px 0; }

        /* Stat card */
        .stat-card { padding: 20px; }
        .stat-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: var(--muted); }
        .stat-value { font-size: 28px; font-weight: 600; color: var(--ink); margin-top: 6px; line-height: 1; }
        .stat-sub { font-size: 12px; color: var(--muted); margin-top: 6px; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

        /* Animations */
        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .fade-up { animation: fadeUp .3s ease both; }
        .delay-1 { animation-delay: .05s; }
        .delay-2 { animation-delay: .1s; }
        .delay-3 { animation-delay: .15s; }
        .delay-4 { animation-delay: .2s; }

        /* Alert */
        .alert { padding: 14px 16px; border-radius: var(--radius-sm); border: 1px solid; font-size: 14px; display: flex; align-items: flex-start; gap: 10px; }
        .alert-success { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }
        .alert-danger { background: var(--rose-light); border-color: #fecaca; color: var(--rose); }
        .alert-warning { background: var(--amber-light); border-color: #fed7aa; color: var(--amber); }
        .alert-info { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
    </style>
    @stack('styles')
</head>
<body>
    @include('layouts.navigation')

    @hasSection('header')
        <div class="page-header">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="page-title">@yield('header')</h1>
                    @hasSection('subheader')
                        <p class="page-sub">@yield('subheader')</p>
                    @endif
                </div>
                <div class="flex items-center gap-2">@yield('header-actions')</div>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="max-w-6xl mx-auto px-4 sm:px-6 mt-4">
            <div class="alert alert-success"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-6xl mx-auto px-4 sm:px-6 mt-4">
            <div class="alert alert-danger"><i class="fas fa-circle-xmark"></i> {{ session('error') }}</div>
        </div>
    @endif

    <main class="flex-1 py-8">
        @yield('content')
    </main>

    @include('layouts.footer')

    <div id="toast" class="fixed bottom-5 right-5 z-50 space-y-2"></div>
    @stack('scripts')
</body>
</html>
