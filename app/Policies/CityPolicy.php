<?php

namespace App\Policies;

use App\Models\City;
use App\Models\User;

class CityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('city.view');
    }

    public function view(User $user, City $city): bool
    {
        return $user->hasPermissionTo('city.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('city.create');
    }

    public function update(User $user, City $city): bool
    {
        return $user->hasPermissionTo('city.update');
    }

    public function delete(User $user, City $city): bool
    {
        return $user->hasPermissionTo('city.delete');
    }

    // دوال الـ SoftDeletes
    public function restore(User $user, City $city): bool
    {
        return $user->hasPermissionTo('city.delete');
    }

    public function forceDelete(User $user, City $city): bool
    {
        return $user->hasRole('Super Admin'); // يُفضل أن يكون الحذف النهائي للمدير العام فقط
    }
}
