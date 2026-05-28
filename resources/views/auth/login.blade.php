<x-guest-layout>
    <h2 style="font-size:20px; font-weight:600; color:var(--ink); margin-bottom:4px;">Welcome back</h2>
    <p style="font-size:13px; color:var(--muted); margin-bottom:24px;">Sign in to your Health360 account</p>

    @if($errors->any())
        <div class="alert-error">
            <i class="fas fa-triangle-exclamation"></i>
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('status'))
        <div style="padding:10px 14px; border-radius:var(--radius-sm); border:1px solid #bbf7d0; background:#f0fdf4; color:#15803d; font-size:13px; margin-bottom:16px;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-row">
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="form-input" placeholder="you@example.com">
            @error('email') <p class="error-msg">{{ $message }}</p> @enderror
        </div>

        <div class="form-row">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:5px;">
                <label for="password" class="form-label" style="margin-bottom:0;">Password</label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="font-size:12px; color:var(--teal);">Forgot password?</a>
                @endif
            </div>
            <div style="position:relative;">
                <input id="password" type="password" name="password" required
                       class="form-input" placeholder="••••••••" style="padding-right:42px;">
                <button type="button" onclick="togglePw(this)"
                        style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--muted); padding:0;">
                    <i class="fas fa-eye" style="font-size:14px;"></i>
                </button>
            </div>
            @error('password') <p class="error-msg">{{ $message }}</p> @enderror
        </div>

        <div class="form-row" style="display:flex; align-items:center; gap:8px;">
            <input id="remember" type="checkbox" name="remember"
                   style="width:15px; height:15px; accent-color:var(--teal); cursor:pointer;">
            <label for="remember" style="font-size:13px; color:var(--muted); cursor:pointer;">Keep me signed in</label>
        </div>

        <div class="form-row" style="margin-top:8px;">
            <button type="submit" class="btn-primary">
                <i class="fas fa-arrow-right-to-bracket"></i> Sign in
            </button>
        </div>
    </form>

    @if(Route::has('register'))
        <p style="text-align:center; font-size:13px; color:var(--muted); margin-top:20px;">
            Don't have an account? <a href="{{ route('register') }}" style="color:var(--teal); font-weight:500;">Create one free</a>
        </p>
    @endif
</x-guest-layout>

<script>
function togglePw(btn) {
    const input = btn.previousElementSibling;
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>
