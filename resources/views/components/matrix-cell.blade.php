@props(['value' => null, 'altId', 'critId'])

<td class="px-3 py-3 text-center">
    <input type="number"
        name="values[{{ $altId }}][{{ $critId }}]"
        value="{{ $value ?? '' }}"
        min="0" step="0.01" required
        class="w-20 text-center border border-gray-200 rounded-lg px-2 py-1.5 text-sm
            focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:border-transparent"
    />
</td>
