<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaletteRequest;
use App\Http\Requests\UpdatePaletteRequest;
use App\Models\Palette;
use App\Services\PaletteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class PaletteController extends Controller
{
    public function __construct(private PaletteService $palettes) {}

    /**
     * Show the shared palettes.
     */
    public function index(): Response
    {
        return Inertia::render('palettes/Index', [
            'palettes' => $this->palettes->showPublic(),
        ]);
    }

    /**
     * Show the authenticated user's palettes.
     */
    public function mine(Request $request): Response
    {
        return Inertia::render('palettes/Mine', [
            'palettes' => $this->palettes->showByUser($request->user()),
        ]);
    }

    /**
     * Show a single palette.
     */
    public function show(int $id): Response
    {
        $palette = $this->palettes->showById($id);

        Gate::authorize('view', $palette);

        return Inertia::render('palettes/Show', [
            'palette' => $palette,
        ]);
    }

    /**
     * Store a new palette.
     */
    public function store(StorePaletteRequest $request): RedirectResponse
    {
        $palette = $this->palettes->create(
            $request->user(),
            $request->validated('name'),
            $request->boolean('is_public'),
            $request->validated('blocks'),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Palette created.')]);

        return to_route('palettes.show', $palette);
    }

    /**
     * Update a palette.
     */
    public function update(UpdatePaletteRequest $request, Palette $palette): RedirectResponse
    {
        $this->palettes->update(
            $palette,
            $request->validated('name'),
            $request->has('is_public') ? $request->boolean('is_public') : null,
            $request->validated('blocks'),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Palette updated.')]);

        return to_route('palettes.show', $palette);
    }

    /**
     * Delete a palette.
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->palettes->deleteById($id);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Palette deleted.')]);

        return to_route('palettes.mine');
    }

    /**
     * Add or remove a palette from the user's favorites.
     */
    public function toggleFavorite(Request $request, Palette $palette): RedirectResponse
    {
        $added = $this->palettes->toggleFavorite($request->user(), $palette);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => $added ? __('Added to favorites.') : __('Removed from favorites.'),
        ]);

        return back();
    }
}
