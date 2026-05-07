<?php

namespace App\Policies;

use App\Models\InventoryItem;
use App\Models\User;

class InventoryItemPolicy
{
    public function before(User $user): ?bool
    {
        return $user->is_admin ? true : null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, InventoryItem $inventoryItem): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, InventoryItem $inventoryItem): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, InventoryItem $inventoryItem): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, InventoryItem $inventoryItem): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, InventoryItem $inventoryItem): bool
    {
        return $user->is_admin;
    }
}
