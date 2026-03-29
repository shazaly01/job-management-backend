<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- استيراد الـ Trait

class City extends Model
{
    use HasFactory, SoftDeletes; // <-- إضافة HasFactory هنا

    protected $fillable = [
        'Name',
        'IsActive',
    ];

    // علاقة: المدينة الواحدة تحتوي على عدة متقدمين
    public function applicants(): HasMany
    {
        return $this->hasMany(Applicant::class);
    }
}
