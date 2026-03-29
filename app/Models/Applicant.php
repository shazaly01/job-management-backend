<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- استيراد الـ Trait
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Applicant extends Model
{
    use HasFactory, SoftDeletes; // <-- إضافة HasFactory هنا

    protected $fillable = [
        'ApplicantNo',
        'NationalID',
        'ReferenceCode',
        'FirstName',
        'LastName',
        'Email',
        'PhoneNumber',
        'city_id',
        'ApplicationSource',
        'IsActive',
        'resume_token',
    ];

    protected $casts = [
        'ApplicantNo' => 'decimal:0',
        'NationalID' => 'decimal:0',
        'ReferenceCode' => 'decimal:0',
    ];

    // ================= العلاقات ================= //

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function qualifications(): HasMany
    {
        return $this->hasMany(ApplicantQualification::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(ApplicantExperience::class);
    }

    public function skills(): HasMany
    {
        return $this->hasMany(ApplicantSkill::class);
    }

   public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    // ================= Scopes (فلترة مخصصة) ================= //

    /**
     * نطاق التوظيف المجهول (Blind Recruitment Scope).
     * يقوم بإخفاء البيانات الشخصية (الاسم، الرقم الوطني، التواصل)
     * وجلب البيانات التي تهم المدراء فقط للفرز.
     */
    public function scopeBlind(Builder $query): Builder
    {
        return $query->select([
            'id',
            'ApplicantNo',
            'city_id',
            'ApplicationSource'
        ]);
    }
}
