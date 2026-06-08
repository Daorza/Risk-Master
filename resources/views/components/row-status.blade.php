@props(['filled', 'total'])

@php $complete = (int)$filled >= (int)$total && (int)$total > 0; @endphp

<td style="text-align:center; padding:0.75rem 1rem;">
    <span class="badge {{ $complete ? 'badge-success' : 'badge-warning' }}">
        {{ $filled }}/{{ $total }}
    </span>
</td>
