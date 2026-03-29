<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('document.view');
    }

    public function view(User $user, Document $document): bool
    {
        return $user->hasPermissionTo('document.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('document.create');
    }

    public function update(User $user, Document $document): bool
    {
        return $user->hasPermissionTo('document.update');
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->hasPermissionTo('document.delete');
    }

    public function restore(User $user, Document $document): bool
    {
        return $user->hasPermissionTo('document.delete');
    }

    public function forceDelete(User $user, Document $document): bool
    {
        return $user->hasRole('Super Admin');
    }
}
