@props(['criteria'])

<div class="bg-white rounded-lg border border-gray-200 p-3">
    <div class="flex items-center gap-2 mb-1">
        <span class="text-xs font-bold px-1.5 py-0.5 rounded
            {{ $criteria->isBenefit() ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
            {{ strtoupper($criteria->type) }}
        </span>
        <span class="text-sm font-semibold text-gray-800">{{ $criteria->name }}</span>
        <span class="text-xs text-gray-400 ml-auto">{{ $criteria->weight_percent }}</span>
    </div>
    @if($criteria->description)
        <p class="text-xs text-gray-500 leading-relaxed">{{ $criteria->description }}</p>
    @endif
</div>
