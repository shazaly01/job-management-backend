<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Interview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_request_id', // ✅ تم التعديل ليرتبط بطلب الاحتياج الوظيفي
        'EmpCode',        // كود الموظف المُقيّم
        'InterviewDate',  // تاريخ الجلسة
        'Location',       // المكان أو الرابط
        'Status',         // حالة المقابلة (مجدولة، مكتملة، ملغاة)
        'Notes',          // ملاحظات عامة
    ];

    protected $casts = [
        'EmpCode' => 'decimal:0', // ✅ تطبيق قاعدتك الثابتة لكود الموظف
        'InterviewDate' => 'date',
    ];

    /**
     * علاقة الرأس بطلب التوظيف المستهدف
     */
    public function jobRequest(): BelongsTo
    {
        return $this->belongsTo(JobRequest::class); // ✅ تم التعديل
    }

    /**
     * علاقة الرأس بالتفاصيل (قائمة المرشحين المجدولين لهذه المقابلة)
     */
    public function details(): HasMany
    {
        return $this->hasMany(InterviewDetail::class, 'interview_id');
    }
}
