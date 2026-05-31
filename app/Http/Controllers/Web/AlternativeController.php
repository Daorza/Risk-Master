<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Alternative;
use Illuminate\Http\Request;

class AlternativeController extends Controller
{
    public function index(Request $request)
    {
        $alternatives = Alternative::with('creator:id,name')
            ->when($request->source, fn($q) => $q->where('source', $request->source))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('alternatives.index', compact('alternatives'));
    }

    public function create()
    {
        return view('alternatives.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
        ]);

        $user = $request->user();

        Alternative::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'source'      => $user->isAdmin() ? Alternative::SOURCE_ADMIN : Alternative::SOURCE_USER,
            'created_by'  => $user->id,
        ]);

        return redirect()
            ->route('alternatives.index')
            ->with('success', 'Alternatif berhasil ditambahkan.');
    }

    public function edit(Request $request, Alternative $alternative)
    {
        if (! $request->user()->isAdmin() && $alternative->created_by !== $request->user()->id) {
            abort(403);
        }

        return view('alternatives.edit', compact('alternative'));
    }

    public function update(Request $request, Alternative $alternative)
    {
        if (! $request->user()->isAdmin() && $alternative->created_by !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
        ]);

        $alternative->update($validated);

        return redirect()
            ->route('alternatives.index')
            ->with('success', 'Alternatif berhasil diperbarui.');
    }

    public function destroy(Request $request, Alternative $alternative)
    {
        if (! $request->user()->isAdmin() && $alternative->created_by !== $request->user()->id) {
            abort(403);
        }

        $alternative->delete();

        return redirect()
            ->route('alternatives.index')
            ->with('success', 'Alternatif berhasil dihapus.');
    }
}
