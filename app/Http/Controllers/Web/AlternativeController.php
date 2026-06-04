<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Alternative;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AlternativeController extends Controller
{
    public function index(Request $request)
    {
        $query = Alternative::with('creator:id,name')
            ->when($request->source, fn($q) => $q->where('source', $request->source));

        if ($request->filled('search')) {
            $keyword = mb_Strtolower(trim($request->search));

            $alternatives = $query->get()
                ->filter(fn($alt) => str_contains(mb_strtolower($alt->name), $keyword))
                ->sortBy('name');

            $page = $request->get('page', 1);
            $perPage = 15;
            $total = $alternatives->count();
            $items = $alternatives->slice(($page - 1) * $perPage, $perPage)->values();

            $alternatives = new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()],
            );
        } else {
            $alternatives = $query
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString();
        }

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
