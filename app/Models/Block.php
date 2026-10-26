<?php

namespace App\Models;

use Database\Factories\BlockFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $identifier
 * @property string $name
 * @property string|null $texture
 * @property string|null $color
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['identifier', 'name', 'texture', 'color'])]
class Block extends Model
{
    /** @use HasFactory<BlockFactory> */
    use HasFactory;

    /**
     * Get the palettes that use this block.
     *
     * @return BelongsToMany<Palette, $this>
     */
    public function palettes(): BelongsToMany
    {
        return $this->belongsToMany(Palette::class)->withPivot('position');
    }
}
