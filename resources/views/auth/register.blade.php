<x-guest-layout>
    <h2 style="font-size:20px; font-weight:600; color:var(--ink); margin-bottom:4px;">Create your account</h2>
    <p style="font-size:13px; color:var(--muted); margin-bottom:24px;">Start monitoring your health with AI-assisted insights</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-row">
            <label for="name" class="form-label">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                   class="form-input" placeholder="Juan dela Cruz">
            @error('name') <p class="error-msg">{{ $message }}</p> @enderror
        </div>

        <div class="form-row">
            <label for="email" class="form-label">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   class="form-input" placeholder="you@example.com">
            @error('email') <p class="error-msg">{{ $message }}</p> @enderror
        </div>

            <div class="register-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:18px;">
            <div>
                <label for="date_of_birth" class="form-label">Date of birth</label>
                <input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                       class="form-input">
                @error('date_of_birth') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="biological_sex" class="form-label">Biological sex</label>
                <select id="biological_sex" name="biological_sex" class="form-input">
                    <option value="">Select…</option>
                    <option value="male" {{ old('biological_sex') === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('biological_sex') === 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('biological_sex') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <label for="password" class="form-label">Password</label>
            <div style="position:relative;">
                <input id="password" type="password" name="password" required
                       class="form-input" placeholder="Min. 8 characters" style="padding-right:42px;">
                <button type="button" onclick="togglePw('password', this)"
                        style="position:absolute; right:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--muted);">
                    <i class="fas fa-eye" style="font-size:14px;"></i>
                </button>
            </div>
            @error('password') <p class="error-msg">{{ $message }}</p> @enderror
        </div>

        <div class="form-row">
            <label for="password_confirmation" class="form-label">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   class="form-input" placeholder="Repeat password">
        </div>

        {{-- Emergency contact --}}
        <div style="background:#fff8f8; border:1px solid #fecaca; border-radius:var(--radius-sm); padding:14px 16px; margin-bottom:18px;">
            <p style="font-size:12px; font-weight:600; color:#7f1d1d; margin-bottom:10px; text-transform:uppercase; letter-spacing:.04em;">
                <i class="fas fa-phone"></i> Emergency contact <span style="color:var(--rose);">*</span>
            </p>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:8px;" class="register-grid">
                <div>
                    <label for="emergency_contact_name" class="form-label">Full name</label>
                    <input id="emergency_contact_name" type="text" name="emergency_contact_name"
                           value="{{ old('emergency_contact_name') }}" required
                           class="form-input" placeholder="Contact person">
                    @error('emergency_contact_name') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="emergency_contact_phone" class="form-label">Phone number</label>
                    <input id="emergency_contact_phone" type="tel" name="emergency_contact_phone"
                           value="{{ old('emergency_contact_phone') }}" required
                           class="form-input" placeholder="+63 912 345 6789">
                    @error('emergency_contact_phone') <p class="error-msg">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label for="emergency_contact_relationship" class="form-label">Relationship <span style="font-weight:400; color:#94a3b8;">(optional)</span></label>
                <input id="emergency_contact_relationship" type="text" name="emergency_contact_relationship"
                       value="{{ old('emergency_contact_relationship') }}"
                       class="form-input" placeholder="e.g. Spouse, Parent, Sibling">
                @error('emergency_contact_relationship') <p class="error-msg">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Consent checkboxes --}}
        <div style="background:var(--teal-light); border-radius:var(--radius-sm); padding:14px 16px; margin-bottom:18px;">
            <p style="font-size:12px; font-weight:600; color:var(--teal-dark); margin-bottom:10px; text-transform:uppercase; letter-spacing:.04em;">
                Required consents
            </p>
            <label style="display:flex; align-items:flex-start; gap:9px; margin-bottom:8px; cursor:pointer;">
                <input type="checkbox" name="consent_to_store_symptoms" value="1" required
                       style="width:15px; height:15px; accent-color:var(--teal); margin-top:2px; flex-shrink:0;">
                <span style="font-size:13px; color:var(--ink);">I consent to storing my symptom records securely</span>
            </label>
            <label style="display:flex; align-items:flex-start; gap:9px; cursor:pointer;">
                <input type="checkbox" name="consent_to_ai_processing" value="1" required
                       style="width:15px; height:15px; accent-color:var(--teal); margin-top:2px; flex-shrink:0;">
                <span style="font-size:13px; color:var(--ink);">I consent to AI processing of my symptom data for insights</span>
            </label>
        </div>

        <button type="submit" class="btn-primary">
            <i class="fas fa-user-plus"></i> Create account
        </button>
    </form>

    <p style="text-align:center; font-size:13px; color:var(--muted); margin-top:20px;">
        Already have an account? <a href="{{ route('login') }}" style="color:var(--teal); font-weight:500;">Sign in</a>
    </p>
</x-guest-layout>

<script>
function togglePw(id, btn) {
    const input = document.getElementById(id);
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
