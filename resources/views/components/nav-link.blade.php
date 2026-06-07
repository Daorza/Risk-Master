@props(['href', 'active' => false])

<a href="{{ $href }}" class="sidebar-link {{ $active ? 'sidebar-link-active' : '' }}">
    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
        {{ $icon }}
    </svg>
    <span>{{ $slot }}</span>
</a>
