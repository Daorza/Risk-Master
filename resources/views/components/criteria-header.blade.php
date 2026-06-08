@props(['criteria'])

<th style="text-align:center; min-width:120px; white-space:nowrap;">
    <div style="font-size:var(--font-size-sm); color:var(--color-text);">{{ $criteria->name }}</div>
    <div style="font-size:var(--font-size-xs); font-weight:500; margin-top:0.125rem; color: {{ $criteria->isBenefit() ? 'var(--color-primary-text)' : 'var(--color-warning-text)' }};">
        {{ strtoupper($criteria->type) }} · {{ $criteria->weight_percent }}
    </div>
</th>
