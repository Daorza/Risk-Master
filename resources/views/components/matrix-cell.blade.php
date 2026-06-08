@props(['value' => null, 'altId', 'critId'])

<td style="text-align:center; padding:0.625rem 0.75rem;">
    <input type="number"
        name="values[{{ $altId }}][{{ $critId }}]"
        value="{{ $value ?? '' }}"
        min="0" step="0.01" required
        class="form-input"
        style="width:5rem; text-align:center; padding:0.375rem 0.5rem; font-family:var(--font-mono);">
</td>
