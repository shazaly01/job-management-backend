<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;

class DepartmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('department.view');
    }

    public function view(User $user, Department $department): bool
    {
        return $user->hasPermissionTo('department.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('department.create');
    }

    public function update(User $user, Department $department): bool
    {
        return $user->hasPermissionTo('department.update');
    }

    public function delete(User $user, Department $department): bool
    {
        return $user->hasPermissionTo('department.delete');
    }

    public function restore(User $user, Department $department): bool
    {
        return $user->hasPermissionTo('department.delete');
    }

    public function forceDelete(User $user, Department $department): bool
    {
        return $user->hasRole('Super Admin');
    }
}
