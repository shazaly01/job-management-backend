<?php

namespace App\Policies;

use App\Models\JobRequest;
use App\Models\User;

class JobRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('job_request.view');
    }

    public function view(User $user, JobRequest $jobRequest): bool
    {
        // ملاحظة: يمكنك لاحقاً إضافة شرط هنا ليسمح لمدير الإدارة برؤية طلبات إدارته فقط
        // return $user->hasPermissionTo('job_request.view') && $user->department_id === $jobRequest->department_id;

        return $user->hasPermissionTo('job_request.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('job_request.create');
    }

    public function update(User $user, JobRequest $jobRequest): bool
    {
        return $user->hasPermissionTo('job_request.update');
    }

    public function delete(User $user, JobRequest $jobRequest): bool
    {
        return $user->hasPermissionTo('job_request.delete');
    }

    public function restore(User $user, JobRequest $jobRequest): bool
    {
        return $user->hasPermissionTo('job_request.delete');
    }

    public function forceDelete(User $user, JobRequest $jobRequest): bool
    {
        return $user->hasRole('Super Admin');
    }
}
