<?php

namespace App\Policies;

use App\Models\Applicant;
use App\Models\User;

class ApplicantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('applicant.view');
    }

    public function view(User $user, Applicant $applicant): bool
    {
        return $user->hasPermissionTo('applicant.view');
    }

   public function create(?User $user): bool
{
    // إذا كان هناك مستخدم مسجل، نتحقق من صلاحياته
    if ($user) {
        return $user->hasPermissionTo('applicant.create');
    }

    // إذا كان زائراً مجهولاً (null)، نسمح له بالتقديم عبر البوابة الخارجية
    return true;
}

    public function update(User $user, Applicant $applicant): bool
    {
        return $user->hasPermissionTo('applicant.update');
    }

    public function delete(User $user, Applicant $applicant): bool
    {
        return $user->hasPermissionTo('applicant.delete');
    }

    public function restore(User $user, Applicant $applicant): bool
    {
        return $user->hasPermissionTo('applicant.delete');
    }

    public function forceDelete(User $user, Applicant $applicant): bool
    {
        // حماية إضافية: الحذف النهائي لملف متقدم لا يتم إلا من المدير العام
        return $user->hasRole('Super Admin');
    }
}
