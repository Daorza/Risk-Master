@props(['href', 'active' => false])

<a href="{{ $href }}"
   class="{{ $active
       ? 'bg-blue-600 text-white'
       : 'text-slate-300 hover:bg-slate-800 hover:text-white'
   }} flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        {{ $icon }}
    </svg>
    {{ $slot }}
</a>
