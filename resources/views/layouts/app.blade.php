<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Risk Master') — SPK EDAS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:300,400,500,600,700&family=inter:400,500,600,700&family=jetbrains-mono:400,500" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div style="display:flex; min-height:100dvh;">

    {{-- ── Sidebar ─────────────────────────────────────────────────────────── --}}
    <aside class="sidebar">

        {{-- Logo --}}
        <div class="sidebar-logo">
            <a href="{{ route('dashboard') }}" style="display:flex; align-items:center; gap:0.625rem; text-decoration:none;">
                <div style="
                    width:2.125rem; height:2.125rem;
                    border-radius:var(--radius-lg);
                    background: linear-gradient(135deg, var(--color-accent-500), var(--color-accent-400));
                    display:flex; align-items:center; justify-content:center; flex-shrink:0;
                    box-shadow: 0 0 16px oklch(65% 0.16 188 / 0.4);
                ">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="oklch(14.8% 0.004 228.8)" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <span style="display:block; font-size:var(--font-size-base); font-weight:700; color:var(--color-mist-50); letter-spacing:-0.025em; line-height:1.2;">Risk Master</span>
                    <span style="display:block; font-size:var(--font-size-xs); color:var(--color-mist-600); letter-spacing:0.03em; font-weight:400;">SPK EDAS</span>
                </div>
            </a>
        </div>

        {{-- Nav --}}
        <nav class="sidebar-nav">
            <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </x-slot>
                Dashboard
            </x-nav-link>

            <x-nav-link href="{{ route('assessments.index') }}" :active="request()->routeIs('assessments.*')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </x-slot>
                Assessment
            </x-nav-link>

            <x-nav-link href="{{ route('alternatives.index') }}" :active="request()->routeIs('alternatives.*')">
                <x-slot:icon>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4 6h16M4 10h16M4 14h16M4 18h7"/>
                </x-slot>
                Alternatif
            </x-nav-link>

            @if(auth()->user()->isAdmin())
                <p class="sidebar-section-label">Admin</p>

                <x-nav-link href="{{ route('admin.criteria.index') }}" :active="request()->routeIs('admin.criteria.*')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </x-slot>
                    Kriteria
                </x-nav-link>

                <x-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </x-slot>
                    Pengguna
                </x-nav-link>

                <x-nav-link href="{{ route('admin.logs.index') }}" :active="request()->routeIs('admin.logs.*')">
                    <x-slot:icon>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </x-slot>
                    Audit Logs
                </x-nav-link>
            @endif
        </nav>

        {{-- User footer --}}
        <div class="sidebar-footer">
            <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.75rem; padding:0.625rem 0.5rem; border-radius:var(--radius-lg); background: oklch(21.8% 0.008 223.9 / 0.6);">
                <div style="
                    width:2.125rem; height:2.125rem; border-radius:var(--radius-full);
                    background: linear-gradient(135deg, var(--color-accent-500), var(--color-accent-600));
                    display:flex; align-items:center; justify-content:center;
                    font-size:var(--font-size-xs); font-weight:700;
                    color:oklch(14.8% 0.004 228.8); flex-shrink:0;
                    box-shadow: 0 0 10px oklch(65% 0.16 188 / 0.25);
                ">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div style="min-width:0; flex:1;">
                    <p style="font-size:var(--font-size-sm); font-weight:600; color:var(--color-mist-50); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; letter-spacing:-0.01em;">{{ auth()->user()->name }}</p>
                    <p style="font-size:var(--font-size-xs); color:var(--color-mist-600); margin-top:0.05rem;">{{ ucfirst(auth()->user()->role) }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar-link" style="width:100%; background:none; border:none; cursor:pointer; text-align:left;">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main area ────────────────────────────────────────────────────────── --}}
    <div style="flex:1; display:flex; flex-direction:column; margin-left:var(--sidebar-width); min-height:100dvh;">

        {{-- Topbar --}}
        <header class="topbar" style="position:sticky; inset-block-start:0; inset-inline:0; margin-left:0;">
            <h1 style="font-size:var(--font-size-lg); font-weight:700; color:var(--color-mist-50); letter-spacing:-0.025em; flex:1;">
                @yield('header', 'Dashboard')
            </h1>
            <span style="
                font-size:var(--font-size-xs);
                color:var(--color-mist-600);
                background: oklch(21.8% 0.008 223.9 / 0.6);
                border: 1px solid oklch(100% 0 0 / 0.06);
                padding: 0.25rem 0.625rem;
                border-radius: var(--radius-full);
                font-weight:500;
            ">{{ now()->isoFormat('D MMM YYYY') }}</span>
        </header>

        {{-- Flash messages --}}
        @if(session('success') || session('error') || session('info'))
            <div style="padding: 1rem 1.5rem 0;">
                @if(session('success'))
                    <div class="alert alert-success animate-slide-in-top">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger animate-slide-in-top">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @if(session('info'))
                    <div class="alert alert-info animate-slide-in-top">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('info') }}</span>
                    </div>
                @endif
            </div>
        @endif

        {{-- Page content --}}
        <main class="main-content" style="margin-left:0; margin-top:0;">
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
