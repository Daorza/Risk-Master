@extends('layouts.app')
@section('title', 'Edit User')
@section('header', 'Edit User')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
            @csrf @method('PUT')

            {{-- Nama --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Password (opsional saat edit) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Password Baru
                    <span class="text-gray-400 text-xs font-normal">(kosongkan jika tidak diubah)</span>
                </label>
                <div class="relative">
                    <input type="password" name="password" id="password"
                           autocomplete="new-password"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('password') border-red-400 @enderror">
                    <button type="button" onclick="togglePassword('password', 'eye-password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg id="eye-password" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>

                {{-- Strength indicator — hanya muncul saat mulai ketik --}}
                <div class="mt-2 space-y-2 hidden" id="password-feedback">
                    <div class="flex gap-1">
                        <div id="bar-1" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                        <div id="bar-2" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                        <div id="bar-3" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                        <div id="bar-4" class="h-1.5 flex-1 rounded-full bg-gray-200 transition-colors duration-300"></div>
                    </div>
                    <p id="strength-label" class="text-xs text-gray-400"></p>
                    <ul class="space-y-1">
                        <li id="req-length" class="flex items-center gap-2 text-xs text-gray-400">
                            <span id="icon-length">○</span> Minimal 8 karakter
                        </li>
                        <li id="req-upper" class="flex items-center gap-2 text-xs text-gray-400">
                            <span id="icon-upper">○</span> Huruf kapital (A–Z)
                        </li>
                        <li id="req-lower" class="flex items-center gap-2 text-xs text-gray-400">
                            <span id="icon-lower">○</span> Huruf kecil (a–z)
                        </li>
                        <li id="req-number" class="flex items-center gap-2 text-xs text-gray-400">
                            <span id="icon-number">○</span> Angka (0–9)
                        </li>
                        <li id="req-symbol" class="flex items-center gap-2 text-xs text-gray-400">
                            <span id="icon-symbol">○</span> Simbol (!@#$%^&* dll)
                        </li>
                    </ul>
                </div>

                @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Konfirmasi password --}}
            <div id="confirm-wrapper" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Konfirmasi Password Baru
                </label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           autocomplete="new-password"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="button" onclick="togglePassword('password_confirmation', 'eye-confirm')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg id="eye-confirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                <p id="match-msg" class="text-xs mt-1 hidden"></p>
            </div>

            {{-- Role --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Role</label>
                <select name="role"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="user"  @selected(old('role', $user->role) === 'user')>User</option>
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition">
                    Simpan
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="text-sm text-gray-500 hover:text-gray-700 self-center">Batal</a>
            </div>
        </form>
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
        { color: 'bg-red-400',    text: 'Sangat Lemah', textClass: 'text-red-500' },
        { color: 'bg-orange-400', text: 'Lemah',        textClass: 'text-orange-500' },
        { color: 'bg-yellow-400', text: 'Cukup',        textClass: 'text-yellow-600' },
        { color: 'bg-blue-500',   text: 'Kuat',         textClass: 'text-blue-600' },
        { color: 'bg-green-500',  text: 'Sangat Kuat',  textClass: 'text-green-600' },
    ];

    passwordInput.addEventListener('input', () => {
        const value = passwordInput.value;

        // Tampilkan feedback & konfirmasi saat mulai ketik
        feedbackBox.classList.toggle('hidden', !value);
        confirmWrapper.classList.toggle('hidden', !value);

        if (!value) {
            bars.forEach(b => b.className = 'h-1.5 flex-1 rounded-full bg-gray-200 transition-colors duration-300');
            label.textContent = '';
            return;
        }

        let score = 0;
        Object.values(checks).forEach(({ el, icon, fn }) => {
            const ok = fn(value);
            if (ok) score++;
            el.classList.toggle('text-green-600', ok);
            el.classList.toggle('text-gray-400', !ok);
            icon.textContent = ok ? '✓' : '○';
        });

        const lvl = levels[score - 1] ?? levels[0];
        bars.forEach((bar, i) => {
            bar.className = 'h-1.5 flex-1 rounded-full transition-colors duration-300 ' +
                (i < score ? lvl.color : 'bg-gray-200');
        });
        label.textContent = lvl.text;
        label.className   = `text-xs ${lvl.textClass}`;

        checkMatch();
    });

    function checkMatch() {
        const matchMsg = document.getElementById('match-msg');
        if (!confirmInput.value) { matchMsg.classList.add('hidden'); return; }
        const match = passwordInput.value === confirmInput.value;
        matchMsg.classList.remove('hidden');
        matchMsg.textContent = match ? '✓ Password cocok' : '✗ Password tidak cocok';
        matchMsg.className   = `text-xs mt-1 ${match ? 'text-green-600' : 'text-red-500'}`;
    }

    confirmInput.addEventListener('input', checkMatch);
</script>
@endpush

@endsection
