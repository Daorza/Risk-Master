<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Risk Master</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
</head>
<body style="min-height:100dvh; display:flex; align-items:center; justify-content:center; padding:1.5rem;
             background: radial-gradient(ellipse 80% 60% at 50% -10%, oklch(37.8% 0.015 216) 0%, oklch(14.8% 0.004 228.8) 100%);">

    <div style="width:100%; max-width:26rem;">

        {{-- Logo --}}
        <div style="text-align:center; margin-bottom:2rem;">
            <div style="display:inline-flex; align-items:center; justify-content:center;
                        width:3.5rem; height:3.5rem; border-radius:var(--radius-xl);
                        background:var(--color-primary); margin-bottom:1rem;
                        box-shadow: 0 0 0 8px color-mix(in srgb, var(--color-primary) 15%, transparent);">
                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h1 style="font-size:var(--font-size-2xl); font-weight:700; color:white; letter-spacing:-0.03em; margin:0;">Risk Master</h1>
            <p style="font-size:var(--font-size-sm); color:oklch(56% 0.021 213.5); margin-top:0.375rem;">SPK Mitigasi Risiko Keamanan Informasi</p>
        </div>

        {{-- Card --}}
        <div class="card bg-gray-700 dark:bg-gray-800" style="border-radius:var(--radius-xl); padding:2rem; box-shadow:var(--shadow-lg);">

            @if($errors->any())
                <div class="alert alert-danger" style="margin-bottom:1.25rem;">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" style="display:flex; flex-direction:column; gap:1.125rem;">
                @csrf

                <div class="form-group">
                    <label class="form-label form-label-required" for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           required autofocus autocomplete="email"
                           class="form-input {{ $errors->has('email') ? 'is-error' : '' }}">
                    @error('email')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label form-label-required" for="password">Password</label>
                    <div style="position:relative;">
                        <input type="password" id="password" name="password"
                               required autocomplete="current-password"
                               class="form-input {{ $errors->has('password') ? 'is-error' : '' }}"
                               style="padding-right:2.75rem;">
                        <button type="button" onclick="togglePwd()"
                                style="position:absolute; right:0.75rem; top:50%; transform:translateY(-50%);
                                       background:none; border:none; cursor:pointer; color:var(--color-text-subtle); padding:0;">
                            <svg id="eye-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display:flex; align-items:center; gap:0.5rem;">
                    <input type="checkbox" id="remember" name="remember"
                           style="width:1rem; height:1rem; border-radius:var(--radius-sm); accent-color:var(--color-primary);">
                    <label for="remember" style="font-size:var(--font-size-sm); color:var(--color-text-muted); cursor:pointer;">Ingat saya</label>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:0.375rem;">
                    Masuk
                </button>
            </form>
        </div>

        <p style="text-align:center; font-size:var(--font-size-xs); color:oklch(56% 0.021 213.5); margin-top:1.5rem;">
            © {{ date('Y') }} Risk Master — EDAS Decision Support System
        </p>
    </div>

    <script>
        function togglePwd() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('eye-icon');
            const show  = input.type === 'password';
            input.type  = show ? 'text' : 'password';
            icon.innerHTML = show
                ? `<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`
                : `<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
        }
    </script>
</body>
</html>
