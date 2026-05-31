@props(['value' => null, 'filled', 'total'])

@php
    $complete = $filled >= $total && $total > 0;
@endphp

<td class="px-3 py-3 text-center">
    <span class="text-xs px-2 py-1 rounded-full font-medium
        {{ $complete
            ? 'bg-green-100 text-green-700'
            : 'bg-gray-100 text-gray-700' }}">
        {{ $filled }}/{{ $total }}
    </span>
</td>
