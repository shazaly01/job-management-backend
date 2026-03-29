<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('application.view');
    }

    public function view(User $user, Application $application): bool
    {
        return $user->hasPermissionTo('application.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('application.create');
    }

    public function update(User $user, Application $application): bool
    {
        return $user->hasPermissionTo('application.update');
    }

    public function delete(User $user, Application $application): bool
    {
        return $user->hasPermissionTo('application.delete');
    }

    public function restore(User $user, Application $application): bool
    {
        return $user->hasPermissionTo('application.delete');
    }

    public function forceDelete(User $user, Application $application): bool
    {
        return $user->hasRole('Super Admin');
    }
}
