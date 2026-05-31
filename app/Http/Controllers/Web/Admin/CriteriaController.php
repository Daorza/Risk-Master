<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Criteria;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CriteriaController extends Controller
{
    public function index()
    {
        $criteria    = Criteria::orderBy('id')->get();
        $totalWeight = $criteria->sum('weight');

        return view('admin.criteria.index', compact('criteria', 'totalWeight'));
    }

    public function create()
    {
        return view('admin.criteria.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:criteria'],
            'description' => ['nullable', 'string'],
            'type'        => ['required', Rule::in(['benefit', 'cost'])],
            'weight'      => ['required', 'numeric', 'min:0.0001', 'max:1'],
        ]);

        Criteria::create($validated);

        return redirect()
            ->route('admin.criteria.index')
            ->with('success', 'Kriteria berhasil ditambahkan.');
    }

    public function edit(Criteria $criterium)
    {
        return view('admin.criteria.edit', ['criteria' => $criterium]);
    }

    public function update(Request $request, Criteria $criterium)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', Rule::unique('criteria')->ignore($criterium->id)],
            'description' => ['nullable', 'string'],
            'type'        => ['required', Rule::in(['benefit', 'cost'])],
            'weight'      => ['required', 'numeric', 'min:0.0001', 'max:1'],
        ]);

        $criterium->update($validated);

        return redirect()
            ->route('admin.criteria.index')
            ->with('success', 'Kriteria berhasil diperbarui.');
    }

    public function destroy(Criteria $criterium)
    {
        $criterium->delete();

        return redirect()
            ->route('admin.criteria.index')
            ->with('success', 'Kriteria berhasil dihapus.');
    }
}
