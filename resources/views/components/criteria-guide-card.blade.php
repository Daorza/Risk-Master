@props(['criteria'])

<div class="card" style="padding:0.75rem;">
    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.25rem;">
        <span class="badge {{ $criteria->isBenefit() ? 'badge-primary' : 'badge-warning' }}" style="padding:0.125rem 0.375rem; font-size:var(--font-size-xs);">
            {{ strtoupper($criteria->type) }}
        </span>
        <span style="font-size:var(--font-size-sm); font-weight:600; color:var(--color-text);">{{ $criteria->name }}</span>
        <span style="font-size:var(--font-size-xs); color:var(--color-text-muted); margin-left:auto;">{{ $criteria->weight_percent }}</span>
    </div>
    @if($criteria->description)
        <p style="font-size:var(--font-size-xs); color:var(--color-text-subtle); line-height:1.6;">{{ $criteria->description }}</p>
    @endif
</div>
