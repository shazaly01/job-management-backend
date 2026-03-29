<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- استيراد الـ Trait

class Application extends Model
{
    use HasFactory, SoftDeletes; // <-- إضافة HasFactory

    protected $fillable = [
        'TransactionNo',
        'applicant_id',
        'job_request_id',
        'ApplicationStatus',
    ];

    protected $casts = [
        'TransactionNo' => 'decimal:0', // مهم جداً للأرقام الطويلة 18,0
    ];

    // علاقة: حركة التقديم تخص متقدم واحد
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

    // علاقة: حركة التقديم ترتبط بطلب توظيف واحد
    public function jobRequest(): BelongsTo
    {
        return $this->belongsTo(JobRequest::class);
    }

    // علاقة: حركة التقديم قد تحتوي على عدة مقابلات
    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }
}
