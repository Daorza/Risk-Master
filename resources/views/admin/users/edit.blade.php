@extends('layouts.app')
@section('title', 'Edit User')
@section('header', 'Edit User')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" style="display:flex; flex-direction:column; gap:1.25rem;">
                @csrf @method('PUT')

                {{-- Nama --}}
                <div class="form-group">
                    <label class="form-label form-label-required">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="form-input {{ $errors->has('name') ? 'is-error' : '' }}">
                    @error('name') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label form-label-required">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="form-input {{ $errors->has('email') ? 'is-error' : '' }}">
                    @error('email') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                {{-- Password (opsional saat edit) --}}
                <div class="form-group">
                    <label class="form-label">
                        Password Baru
                        <span style="font-size:var(--font-size-xs); font-weight:normal; color:var(--color-text-subtle); margin-left:0.25rem;">(kosongkan jika tidak diubah)</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                               autocomplete="new-password"
                               class="form-input {{ $errors->has('password') ? 'is-error' : '' }}" style="padding-right:2.5rem;">
                        <button type="button" onclick="togglePassword('password', 'eye-password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2" style="color:var(--color-text-subtle);">
                            <svg id="eye-password" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Strength indicator — hanya muncul saat mulai ketik --}}
                    <div style="margin-top:0.5rem; display:flex; flex-direction:column; gap:0.5rem;" id="password-feedback" class="hidden">
                        <div class="flex gap-1">
                            <div id="bar-1" style="height:0.375rem; flex:1; border-radius:9999px; background:var(--glass-bg); transition:background-color 0.3s;"></div>
                            <div id="bar-2" style="height:0.375rem; flex:1; border-radius:9999px; background:var(--glass-bg); transition:background-color 0.3s;"></div>
                            <div id="bar-3" style="height:0.375rem; flex:1; border-radius:9999px; background:var(--glass-bg); transition:background-color 0.3s;"></div>
                            <div id="bar-4" style="height:0.375rem; flex:1; border-radius:9999px; background:var(--glass-bg); transition:background-color 0.3s;"></div>
                        </div>
                        <p id="strength-label" style="font-size:var(--font-size-xs); color:var(--color-text-subtle);"></p>
                        <ul style="display:flex; flex-direction:column; gap:0.25rem; margin-top:0.5rem;">
                            <li id="req-length" style="display:flex; align-items:center; gap:0.5rem; font-size:var(--font-size-xs); color:var(--color-text-subtle);">
                                <span id="icon-length">○</span> Minimal 8 karakter
                            </li>
                            <li id="req-upper" style="display:flex; align-items:center; gap:0.5rem; font-size:var(--font-size-xs); color:var(--color-text-subtle);">
                                <span id="icon-upper">○</span> Huruf kapital (A–Z)
                            </li>
                            <li id="req-lower" style="display:flex; align-items:center; gap:0.5rem; font-size:var(--font-size-xs); color:var(--color-text-subtle);">
                                <span id="icon-lower">○</span> Huruf kecil (a–z)
                            </li>
                            <li id="req-number" style="display:flex; align-items:center; gap:0.5rem; font-size:var(--font-size-xs); color:var(--color-text-subtle);">
                                <span id="icon-number">○</span> Angka (0–9)
                            </li>
                            <li id="req-symbol" style="display:flex; align-items:center; gap:0.5rem; font-size:var(--font-size-xs); color:var(--color-text-subtle);">
                                <span id="icon-symbol">○</span> Simbol (!@#$%^&* dll)
                            </li>
                        </ul>
                    </div>

                    @error('password') <span class="form-error">{{ $message }}</span> @enderror
                </div>

                {{-- Konfirmasi password --}}
                <div id="confirm-wrapper" class="hidden form-group">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               autocomplete="new-password"
                               class="form-input" style="padding-right:2.5rem;">
                        <button type="button" onclick="togglePassword('password_confirmation', 'eye-confirm')"
                                class="absolute right-3 top-1/2 -translate-y-1/2" style="color:var(--color-text-subtle);">
                            <svg id="eye-confirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <span id="match-msg" class="form-error hidden" style="margin-top:0.25rem;"></span>
                </div>

                {{-- Role --}}
                <div class="form-group">
                    <label class="form-label form-label-required">Role</label>
                    <select name="role" class="form-input">
                        <option value="user"  @selected(old('role', $user->role) === 'user')>User</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                    </select>
                </div>

                <div style="display:flex; align-items:center; gap:0.75rem; margin-top:0.5rem;">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword(inputId, eyeId) {
        const input = document.getElementById(inputId);
        const eye   = document.getElementById(eyeId);
        const show  = input.type === 'password';
        input.type  = show ? 'text' : 'password';
        eye.innerHTML = show
            ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`
            : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
    }

    const passwordInput  = document.getElementById('password');
    const confirmInput   = document.getElementById('password_confirmation');
    const feedbackBox    = document.getElementById('password-feedback');
    const confirmWrapper = document.getElementById('confirm-wrapper');

    const bars  = [1,2,3,4].map(i => document.getElementById(`bar-${i}`));
    const label = document.getElementById('strength-label');

    const checks = {
        length: { el: document.getElementById('req-length'), icon: document.getElementById('icon-length'), fn: v => v.length >= 8 },
        upper:  { el: document.getElementById('req-upper'),  icon: document.getElementById('icon-upper'),  fn: v => /[A-Z]/.test(v) },
        lower:  { el: document.getElementById('req-lower'),  icon: document.getElementById('icon-lower'),  fn: v => /[a-z]/.test(v) },
        number: { el: document.getElementById('req-number'), icon: document.getElementById('icon-number'), fn: v => /[0-9]/.test(v) },
        symbol: { el: document.getElementById('req-symbol'), icon: document.getElementById('icon-symbol'), fn: v => /[^A-Za-z0-9]/.test(v) },
    };

    const levels = [
        { color: 'var(--color-danger-text)',  text: 'Sangat Lemah' },
        { color: 'var(--color-warning-text)', text: 'Lemah' },
        { color: 'var(--color-warning-text)', text: 'Cukup' },
        { color: 'var(--color-primary)',      text: 'Kuat' },
        { color: 'var(--color-success-text)', text: 'Sangat Kuat' },
    ];

    passwordInput.addEventListener('input', () => {
        const value = passwordInput.value;

        // Tampilkan feedback & konfirmasi saat mulai ketik
        feedbackBox.classList.toggle('hidden', !value);
        confirmWrapper.classList.toggle('hidden', !value);

        if (!value) {
            bars.forEach(b => { b.style.backgroundColor = 'var(--glass-bg)'; });
            label.textContent = '';
            return;
        }

        let score = 0;
        Object.values(checks).forEach(({ el, icon, fn }) => {
            const ok = fn(value);
            if (ok) score++;
            el.style.color = ok ? 'var(--color-success-text)' : 'var(--color-text-subtle)';
            icon.textContent = ok ? '✓' : '○';
        });

        const lvl = levels[score - 1] ?? levels[0];
        bars.forEach((bar, i) => {
            bar.style.backgroundColor = (i < score ? lvl.color : 'var(--glass-bg)');
        });
        label.textContent = lvl.text;
        label.style.color = lvl.color;

        checkMatch();
    });

    function checkMatch() {
        const matchMsg = document.getElementById('match-msg');
        if (!confirmInput.value) { matchMsg.classList.add('hidden'); return; }
        const match = passwordInput.value === confirmInput.value;
        matchMsg.classList.remove('hidden');
        matchMsg.textContent = match ? '✓ Password cocok' : '✗ Password tidak cocok';
        matchMsg.style.color = match ? 'var(--color-success-text)' : 'var(--color-danger-text)';
    }

    confirmInput.addEventListener('input', checkMatch);
</script>
@endpush

@endsection
