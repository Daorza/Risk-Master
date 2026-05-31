@props(['criteria'])

<th class="text-center px-3 py-3 font-semibold text-gray-700 whitespace-nowrap min-w-[120px]">
    <div class="text-sm">{{ $criteria->name }}</div>
    <div class="font-normal text-xs {{ $criteria->isBenefit() ? 'text-blue-500' : 'text-orange-500' }}">
        {{ strtoupper($criteria->type) }} · {{ $criteria->weight_percent }}
    </div>
</th>
