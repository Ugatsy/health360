<x-guest-layout>
    <h2 style="font-size:20px; font-weight:600; color:var(--ink); margin-bottom:4px;">Reset your password</h2>
    <p style="font-size:13px; color:var(--muted); margin-bottom:24px;">
        Enter your email and we'll send you a reset link.
    </p>

    @if(session('status'))
        <div style="padding:12px 14px; border-radius:var(--radius-sm); border:1px solid #bbf7d0; background:#f0fdf4; color:#15803d; font-size:13px; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
            <i class="fas fa-circle-check"></i> {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-row">
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="form-input" placeholder="you@example.com">
            @error('email') <p class="error-msg">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="btn-primary" style="margin-top:8px;">
            <i class="fas fa-paper-plane"></i> Send reset link
        </button>
    </form>

    <p style="text-align:center; font-size:13px; color:var(--muted); margin-top:20px;">
        <a href="{{ route('login') }}" style="color:var(--teal); font-weight:500;">
            <i class="fas fa-arrow-left" style="font-size:11px;"></i> Back to sign in
        </a>
    </p>
</x-guest-layout>
