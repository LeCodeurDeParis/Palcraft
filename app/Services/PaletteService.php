<?php

namespace App\Services;

use App\Models\User;
use App\Models\Palette;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class PaletteService
{
    public function create(User $user, string $name, bool $isPublic, array $blockIds): Palette
    {
        return DB::transaction(function () use ($user, $name, $isPublic,$blockIds) {
            $palette = $user->palettes()->create([
                'name' => $name,
                'is_public' => $isPublic,
            ]); 
              
            foreach (array_values($blockIds) as $i => $blockId) {
                $palette->blocks()->attach($blockId, ['position' => $i + 1]);
            }   
              
            return $palette;
        });
    }

    /**
    * Get the shared palettes, most recent first.
    *
    * @return LengthAwarePaginator<int, Palette>
    */
    public function showPublic(int $perPage = 24): LengthAwarePaginator
    {
        return Palette::public()
            ->with(['user:id,name', 'blocks'])
            ->withCount('favoritedBy')
            ->latest()
            ->paginate($perPage);
    }

    public function showById(int $id): Palette
    {
        return Palette::byId($id)
            ->with(['user:id,name', 'blocks'])
            ->firstOrFail();
    }


    public function deleteById(int $id): void
    {
        Palette::findOrFail($id)->delete();
    }

    public function update(Palette $palette, ?string $name = null, ?bool $isPublic = null, ?array $blockIds = null): Palette
    {
        return DB::transaction(function () use ($palette, $name, $isPublic, $blockIds) {
            $palette->update(array_filter([
                'name' => $name,
                'is_public' => $isPublic,
            ], fn ($value) => $value !== null));

            if ($blockIds !== null) {
                $palette->blocks()->detach();

                foreach (array_values($blockIds) as $i => $blockId) {
                    $palette->blocks()->attach($blockId, ['position' => $i + 1]);
                }
            }

            return $palette->load('blocks');
        });
    }

    public function showByUser(User $user): Collection
    {
        return $user->palettes()
            ->with('blocks')
            ->latest()
            ->get();
    }

    public function toggleFavorite(User $user, Palette $palette): bool
    {
        $result = $user->favoritePalettes()->toggle($palette);

        return count($result['attached']) > 0;
    }

}  