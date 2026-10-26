<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Palette;

class PalettePolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(?User $user, Palette $palette): bool
    {
        return $palette->is_public || $user?->id === $palette->user_id;
    }

    public function delete(User $user, Palette $palette): bool
    {
        return $user->id === $palette->user_id;
    }

    public function update(User $user, Palette $palette): bool
    {
        return $user->id === $palette->user_id;
    }

    public function canAddToFavorite($user): bool
    {
        return $user;
    }
}
