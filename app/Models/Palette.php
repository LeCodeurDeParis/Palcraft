<?php

namespace App\Models;

use Database\Factories\PaletteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property bool $is_public
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'is_public'])]
class Palette extends Model
{
    /** @use HasFactory<PaletteFactory> */
    use HasFactory;

    /**
     * Get the user that owns the palette.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the palette's blocks, ordered by position.
     *
     * @return BelongsToMany<Block, $this>
     */
    public function blocks(): BelongsToMany
    {
        return $this->belongsToMany(Block::class)
            ->withPivot('position')
            ->orderByPivot('position');
    }

    /**
     * Get the users who favorited the palette.
     *
     * @return BelongsToMany<User, $this>
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    /**
     * Scope the query to shared palettes.
     *
     * @param  Builder<Palette>  $query
     */
    #[Scope]
    protected function public(Builder $query): void
    {
        $query->where('is_public', true);
    }

    #[Scope]
    protected function byId(Builder $query, int $id): void
    {
        $query->where('id', $id);
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }
}
