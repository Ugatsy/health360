<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Health360') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600&family=instrument-serif:400i&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --teal: #0a9688; --teal-dark: #00796b; --teal-light: #e0f2f1;
            --rose: #e53935; --rose-light: #ffebee;
            --border: #e8ecf0; --ink: #1a2332; --muted: #64748b;
            --radius: 12px; --radius-sm: 8px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { font-family: 'DM Sans', ui-sans-serif, sans-serif; font-size: 15px; }
        body { background: #f7f9fc; color: var(--ink); min-height: 100vh; display: flex; }
        a { color: var(--teal); text-decoration: none; }
        a:hover { color: var(--teal-dark); }

        .form-label { display: block; font-size: 13px; font-weight: 500; color: var(--muted); margin-bottom: 5px; }
        .form-input { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-sm); font-family: inherit; font-size: 14px; color: var(--ink); background: #fff; transition: border-color .15s, box-shadow .15s; }
        .form-input:focus { outline: none; border-color: var(--teal); box-shadow: 0 0 0 3px rgba(10,150,136,.12); }
        .form-input::placeholder { color: #94a3b8; }
        .btn-primary { display: flex; align-items: center; justify-content: center; gap: 7px; width: 100%; padding: 11px 20px; border: none; border-radius: var(--radius-sm); background: var(--teal); color: #fff; font-family: inherit; font-size: 14px; font-weight: 500; cursor: pointer; transition: background .15s; }
        .btn-primary:hover { background: var(--teal-dark); }
        .error-msg { font-size: 12px; color: var(--rose); margin-top: 4px; }
        .alert-error { padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid #fecaca; background: var(--rose-light); color: var(--rose); font-size: 13px; margin-bottom: 16px; }

        /* Brand panel */
        .brand-panel {
            width: 420px; flex-shrink: 0;
            background: linear-gradient(160deg, #004d40 0%, #00796b 50%, #0a9688 100%);
            padding: 48px 40px;
            display: flex; flex-direction: column; justify-content: space-between;
            position: relative; overflow: hidden;
        }
        .brand-panel::before {
            content: ''; position: absolute;
            width: 320px; height: 320px; border-radius: 50%;
            background: rgba(255,255,255,.05);
            bottom: -80px; right: -80px;
        }
        .brand-panel::after {
            content: ''; position: absolute;
            width: 200px; height: 200px; border-radius: 50%;
            background: rgba(255,255,255,.04);
            top: 30%; right: 40px;
        }

        .form-panel {
            flex: 1; display: flex; align-items: center; justify-content: center;
            padding: 32px 24px;
        }
        .form-box {
            width: 100%; max-width: 400px;
            background: #fff; border: 1px solid var(--border);
            border-radius: var(--radius); padding: 36px;
            box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 10px 30px -10px rgba(0,0,0,.08);
        }
        .form-row { margin-bottom: 18px; }
        .form-row:last-child { margin-bottom: 0; }

        @media (max-width: 768px) {
            .brand-panel { display: none; }
        }
    </style>
</head>
<body>
    {{-- Brand panel (hidden on mobile) --}}
    <div class="brand-panel" style="display:none;" class="hidden lg:flex flex-col">
        @include('layouts.guest-brand')
    </div>
    <div class="brand-panel hidden lg:flex lg:flex-col">
        @include('layouts.guest-brand')
    </div>

    {{-- Form panel --}}
    <div class="form-panel">
        <div class="form-box">
            {{-- Mobile logo --}}
            <div class="lg:hidden" style="text-align:center; margin-bottom:28px;">
                <a href="/" style="display:inline-flex; align-items:center; gap:10px; text-decoration:none;">
                    <div style="width:36px; height:36px; background:var(--teal); border-radius:9px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-heart-pulse" style="color:#fff; font-size:14px;"></i>
                    </div>
                    <span style="font-size:18px; font-weight:600; color:var(--ink);">Health<span style="color:var(--teal);">360</span></span>
                </a>
            </div>
            {{ $slot }}
        </div>
        <p style="margin-top:16px; text-align:center; font-size:12px; color:#94a3b8;">
            Protected by industry-standard encryption &amp; HIPAA-compliant.
        </p>
    </div>
</body>
</html>
