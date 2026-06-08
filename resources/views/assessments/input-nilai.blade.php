@extends('layouts.app')
@section('title', 'Input Nilai Matrix')
@section('header', 'Input Nilai — ' . $assessment->title)

@section('content')
<div class="space-y-5 max-w-3xl mx-auto">

    {{-- Panduan --}}
    <h3 style="font-weight:600; font-size:var(--font-size-sm); margin-bottom:0.5rem; color:inherit;">Panduan Penilaian</h3>
    <div class="alert alert-info" style="border-radius:var(--radius-xl); padding:1rem 1.25rem;">
        <div class="grid grid-cols-2 gap-3">
            <div style="background:var(--color-bg-subtle); border-radius:var(--radius-lg); padding:0.5rem 0.75rem; border:1px solid var(--color-border);">
                <p style="font-size:var(--font-size-xs); font-weight:700; color:var(--color-primary-text); margin-bottom:0.25rem;">🔵 BENEFIT</p>
                <p style="font-size:var(--font-size-xs); color:var(--color-text-muted);">
                    Nilai <strong>semakin tinggi = semakin baik</strong>.<br>
                    Contoh: Efektivitas tinggi → nilai 10
                </p>
            </div>
            <div style="background:var(--color-bg-subtle); border-radius:var(--radius-lg); padding:0.5rem 0.75rem; border:1px solid var(--color-border);">
                <p style="font-size:var(--font-size-xs); font-weight:700; color:var(--color-warning-text); margin-bottom:0.25rem;">🟠 COST</p>
                <p style="font-size:var(--font-size-xs); color:var(--color-text-muted);">
                    Nilai <strong>semakin tinggi = semakin buruk</strong>.<br>
                    Contoh: Biaya mahal → nilai 10 (= sangat buruk)
                </p>
            </div>
            <p style="font-size:var(--font-size-xs); color:inherit; opacity:0.8;" class=" col-span-2">
                Input: <strong>1–10</strong> →
                Sistem konversi ke <strong>0.1–1.0</strong> untuk kalkulasi EDAS.
            </p>
        </div>
    </div>

    @if($alternatives->isEmpty())
        <div class="card" style="padding:3rem 1.5rem; text-align:center;">
            <p style="color:var(--color-text-subtle); font-size:var(--font-size-sm);">Belum ada alternatif.</p>
            <a href="{{ route('assessments.edit', $assessment) }}" class="btn btn-ghost btn-sm" style="margin-top:0.5rem;">Edit assessment →</a>
        </div>
    @else

        {{-- Navigasi wizard --}}
        <div class="card" style="padding:1.25rem;">

            {{-- Progress bar + label --}}
            <div style="margin-bottom:0.75rem;">
                <div class="flex items-center justify-between mb-1.5 tracking-wider">
                    <span style="font-size:var(--font-size-xs); color:var(--color-text-subtle);">
                        Alternatif <strong id="current-label" style="color:var(--color-text);">1</strong> dari <strong style="color:var(--color-text);">{{ $alternatives->count() }}</strong>
                    </span>
                    <span style="font-size:var(--font-size-xs); color:var(--color-text-muted);" id="step-filled-label">0/{{ $criteria->count() }} kriteria diisi</span>
                </div>
                {{-- Track dots --}}
                <div class="flex gap-1.5 items-center mb-2">
                    @foreach($alternatives as $i => $alt)
                        <div id="dot-{{ $i }}"
                             class="h-2 rounded-full transition-all duration-300 {{ $i === 0 ? 'flex-[2]' : 'flex-1' }}"
                             style="background: {{ $i === 0 ? 'var(--color-primary)' : 'var(--color-border)' }}">
                        </div>
                    @endforeach
                </div>
                {{-- Progress bar pengisian --}}
                <div style="width:100%; background:var(--glass-bg); border-radius:9999px; height:0.375rem; overflow:hidden;">
                    <div id="fill-bar" style="height:100%; border-radius:9999px; background:var(--color-success-text); transition:width 0.3s ease; width:0%"></div>
                </div>
            </div>

            {{-- Nama alternatif aktif --}}
            @foreach($alternatives as $i => $alt)
                <div id="alt-name-{{ $i }}" class="{{ $i !== 0 ? 'hidden' : '' }}">
                    <h3 style="font-weight:700; color:var(--color-text); font-size:var(--font-size-base);">{{ $alt->name }}</h3>
                    @if($alt->description)
                        <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); margin-top:0.125rem;">{{ Str::limit($alt->description, 100) }}</p>
                    @endif
                </div>
            @endforeach

            {{-- Navigasi prev/next --}}
            <div class="flex items-center justify-between mt-4">
                <button type="button" id="btn-prev" onclick="navigate(-1)" class="btn btn-secondary btn-sm disabled:opacity-40" disabled>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Sebelumnya
                </button>

                <span style="font-size:var(--font-size-sm); color:var(--color-text-subtle);">
                    <span id="current-num">1</span> / {{ $alternatives->count() }}
                </span>

                <button type="button" id="btn-next" onclick="navigate(1)" class="btn btn-primary btn-sm">
                    Selanjutnya
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('assessments.values.store', $assessment) }}" id="form-nilai">
            @csrf

            {{-- Satu card per alternatif --}}
            @foreach($alternatives as $i => $alt)
                <div id="step-{{ $i }}" class="{{ $i !== 0 ? 'hidden' : '' }} space-y-0 card overflow-hidden">

                    <div style="display:flex; flex-direction:column;">
                        @foreach($criteria as $c)
                            @php
                                $savedDecimal = $valueMap[$alt->id][$c->id] ?? null;
                                $savedSlider  = $savedDecimal !== null
                                    ? (int) round($savedDecimal * 10)
                                    : null;
                            @endphp

                            <div style="padding:1rem 1.25rem; border-bottom:1px solid var(--color-border);">
                                {{-- Baris atas: badge + nama + display nilai --}}
                                <div class="flex items-center justify-between gap-4 mb-3">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="badge {{ $c->isBenefit() ? 'badge-primary' : 'badge-warning' }}" style="padding:0.125rem 0.375rem; font-size:0.65rem;">
                                            {{ strtoupper($c->type) }}
                                        </span>
                                        <span style="font-size:var(--font-size-sm); font-weight:500; color:var(--color-text);" class="truncate">{{ $c->name }}</span>
                                        <span style="font-size:var(--font-size-xs); color:var(--color-text-subtle); flex-shrink:0;">{{ $c->weight_percent }}</span>
                                    </div>

                                    <div class="text-right shrink-0">
                                        <span style="font-size:var(--font-size-xl); font-weight:700; color:var(--color-text);" id="display-{{ $alt->id }}-{{ $c->id }}">
                                            {{ $savedSlider ?? '—' }}
                                        </span>
                                        <span style="font-size:var(--font-size-xs); color:var(--color-text-muted);">/10</span>
                                        <div style="font-size:var(--font-size-xs); color:var(--color-text-muted); margin-top:0.125rem;">
                                            = <span id="decimal-{{ $alt->id }}-{{ $c->id }}">
                                                {{ $savedSlider !== null ? number_format($savedSlider / 10, 1) : '—' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Slider --}}
                                <div class="flex items-center gap-3">
                                    <span style="font-size:var(--font-size-xs); color:var(--color-text-muted); width:0.75rem; text-align:center;">1</span>
                                    <input type="range"
                                           min="1" max="10" step="1"
                                           value="{{ $savedSlider ?? 1 }}"
                                           data-alt="{{ $alt->id }}"
                                           data-crit="{{ $c->id }}"
                                           data-step="{{ $i }}"
                                           class="slider-input flex-1 h-2 rounded-full appearance-none cursor-pointer"
                                           style="accent-color: {{ $c->isBenefit() ? 'var(--color-primary)' : 'var(--color-warning)' }}"
                                           oninput="onSliderChange(this)">
                                    <span style="font-size:var(--font-size-xs); color:var(--color-text-muted); width:0.75rem; text-align:center;">10</span>

                                    <input type="hidden"
                                           name="values[{{ $alt->id }}][{{ $c->id }}]"
                                           id="hidden-{{ $alt->id }}-{{ $c->id }}"
                                           value="{{ $savedDecimal ?? '' }}">
                                </div>

                                {{-- Label kontekstual --}}
                                <div class="flex justify-between mt-1 px-6" style="font-size:0.65rem; color:var(--color-text-muted);">
                                    @if($c->isBenefit())
                                        <span>Sangat Buruk</span><span>Sangat Baik</span>
                                    @else
                                        <span>Sangat Murah/Mudah</span><span>Sangat Mahal/Sulit</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- Footer: total + tombol simpan --}}
            <div class="mt-4 card" style="padding:1rem 1.5rem; display:flex; align-items:center; justify-content:space-between;">
                <div class="flex items-center gap-4">
                    <a href="{{ route('assessments.show', $assessment) }}" class="btn btn-ghost btn-sm">← Kembali</a>
                    <span style="font-size:var(--font-size-xs); color:var(--color-text-subtle);">
                        Total: <strong id="total-filled" style="color:var(--color-text);">0</strong>/{{ $alternatives->count() * $criteria->count() }} sel diisi
                    </span>
                </div>
                <button type="submit" id="btn-simpan" class="btn btn-primary disabled:opacity-50 disabled:cursor-not-allowed">
                    Simpan Semua Nilai
                </button>
            </div>
        </form>

    @endif
</div>

@push('scripts')
<script>
    const TOTAL_STEPS = {{ $alternatives->count() }};
    const TOTAL_CRIT  = {{ $criteria->count() }};
    const TOTAL_SLOTS = TOTAL_STEPS * TOTAL_CRIT;

    let currentStep = 0;

    // filledMap[altId] = Set of critIds yang sudah diisi
    const filledMap = {};

    // Inisialisasi dari nilai tersimpan di DB
    @foreach($alternatives as $alt)
        filledMap[{{ $alt->id }}] = new Set();
        @foreach($criteria as $c)
            @if(isset($valueMap[$alt->id][$c->id]))
                filledMap[{{ $alt->id }}].add({{ $c->id }});
            @endif
        @endforeach
    @endforeach

    // Data alternatif untuk keperluan JS
    const altIds = [
        @foreach($alternatives as $alt)
            {{ $alt->id }},
        @endforeach
    ];

    // ── Navigasi antar step ───────────────────────────────────────────────────

    function navigate(direction) {
        const next = currentStep + direction;
        if (next < 0 || next >= TOTAL_STEPS) return;

        // Sembunyikan step lama
        document.getElementById(`step-${currentStep}`).classList.add('hidden');
        document.getElementById(`alt-name-${currentStep}`).classList.add('hidden');

        currentStep = next;

        // Tampilkan step baru
        document.getElementById(`step-${currentStep}`).classList.remove('hidden');
        document.getElementById(`alt-name-${currentStep}`).classList.remove('hidden');

        updateNav();
        updateStepFilled();
    }

    function updateNav() {
        const btnPrev = document.getElementById('btn-prev');
        const btnNext = document.getElementById('btn-next');

        // Prev
        btnPrev.disabled = currentStep === 0;

        // Next: di step terakhir, sembunyikan next (user tinggal klik simpan)
        if (currentStep === TOTAL_STEPS - 1) {
            btnNext.textContent = '';
            btnNext.innerHTML   = `
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Selesai`;
            btnNext.onclick = null;
            btnNext.disabled = true; // aktifkan hanya jika step terakhir lengkap
            const altId  = altIds[currentStep];
            const filled = filledMap[altId]?.size ?? 0;
            btnNext.disabled = filled < TOTAL_CRIT;
        } else {
            btnNext.innerHTML = `Selanjutnya
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>`;
            btnNext.onclick = () => navigate(1);
            btnNext.disabled = false;
        }

        // Label
        document.getElementById('current-label').textContent = currentStep + 1;
        document.getElementById('current-num').textContent   = currentStep + 1;

        // Dots
        for (let i = 0; i < TOTAL_STEPS; i++) {
            const dot = document.getElementById(`dot-${i}`);
            dot.className = 'h-2 rounded-full transition-all duration-300 ';
            if (i < currentStep) {
                dot.style.background = 'var(--color-success-text)';
                dot.className += 'flex-1';
            } else if (i === currentStep) {
                dot.style.background = 'var(--color-primary)';
                dot.className += 'flex-[2]';
            } else {
                dot.style.background = 'var(--color-border)';
                dot.className += 'flex-1';
            }
        }
    }

    // ── Update info pengisian per step ────────────────────────────────────────

    function updateStepFilled() {
        const altId  = altIds[currentStep];
        const filled = filledMap[altId]?.size ?? 0;
        document.getElementById('step-filled-label').textContent =
            `${filled}/${TOTAL_CRIT} kriteria diisi`;

        // Update fill bar berdasarkan step aktif
        const pct = TOTAL_CRIT > 0 ? (filled / TOTAL_CRIT * 100) : 0;
        document.getElementById('fill-bar').style.width = pct + '%';
    }

    function updateTotalFilled() {
        let total = 0;
        for (const altId in filledMap) {
            total += filledMap[altId].size;
        }
        document.getElementById('total-filled').textContent = total;

        // Aktifkan tombol simpan hanya jika semua slot terisi
        document.getElementById('btn-simpan').disabled = total < TOTAL_SLOTS;
    }

    // ── Slider change handler ─────────────────────────────────────────────────

    function onSliderChange(slider) {
        const altId  = parseInt(slider.dataset.alt);
        const critId = parseInt(slider.dataset.crit);
        const val    = parseInt(slider.value);

        // Update tampilan nilai
        document.getElementById(`display-${altId}-${critId}`).textContent = val;
        document.getElementById(`decimal-${altId}-${critId}`).textContent  = (val / 10).toFixed(1);

        // Simpan ke hidden input (konversi 1–10 → 0.1–1.0)
        document.getElementById(`hidden-${altId}-${critId}`).value = (val / 10).toFixed(4);

        // Tandai sel terisi
        if (!filledMap[altId]) filledMap[altId] = new Set();
        filledMap[altId].add(critId);

        updateStepFilled();
        updateTotalFilled();

        // Cek apakah step terakhir dan semua terisi → aktifkan "Selesai"
        if (currentStep === TOTAL_STEPS - 1) {
            const filled = filledMap[altIds[currentStep]]?.size ?? 0;
            document.getElementById('btn-next').disabled = filled < TOTAL_CRIT;
        }
    }

    // ── Init ──────────────────────────────────────────────────────────────────

    document.addEventListener('DOMContentLoaded', () => {
        // Inisialisasi display dari nilai tersimpan
        document.querySelectorAll('.slider-input').forEach(slider => {
            const altId  = parseInt(slider.dataset.alt);
            const critId = parseInt(slider.dataset.crit);
            const val    = parseInt(slider.value);

            if (val >= 1) {
                const hiddenInput = document.getElementById(`hidden-${altId}-${critId}`);

                // Jika hidden input belum ada nilainya (belum pernah disimpan),
                // set dari slider default (nilai 1)
                if (!hiddenInput.value) {
                    // Jangan auto-fill — biarkan user yang pilih agar tidak ada bias
                    slider.value = 1;
                } else {
                    // Sudah tersimpan — tampilkan nilai tersimpan
                    document.getElementById(`display-${altId}-${critId}`).textContent = val;
                    document.getElementById(`decimal-${altId}-${critId}`).textContent  = (val / 10).toFixed(1);
                }
            }
        });

        updateNav();
        updateStepFilled();
        updateTotalFilled();
    });
</script>

<style>
    input[type=range] {
        -webkit-appearance: none;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 9999px;
        height: 8px;
        outline: none;
    }
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: currentColor;
        cursor: pointer;
        border: 2px solid oklch(98.7% 0.002 197.1);
        box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }
    input[type=range]::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: currentColor;
        cursor: pointer;
        border: 2px solid oklch(98.7% 0.002 197.1);
    }
</style>
@endpush

@endsection
