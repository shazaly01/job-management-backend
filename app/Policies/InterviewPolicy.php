<?php

namespace App\Policies;

use App\Models\Interview;
use App\Models\User;

class InterviewPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('interview.view');
    }

    public function view(User $user, Interview $interview): bool
    {
        return $user->hasPermissionTo('interview.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('interview.create');
    }

    public function update(User $user, Interview $interview): bool
    {
        return $user->hasPermissionTo('interview.update');
    }

    public function delete(User $user, Interview $interview): bool
    {
        return $user->hasPermissionTo('interview.delete');
    }

    public function restore(User $user, Interview $interview): bool
    {
        return $user->hasPermissionTo('interview.delete');
    }

    public function forceDelete(User $user, Interview $interview): bool
    {
        return $user->hasRole('Super Admin');
    }
}
