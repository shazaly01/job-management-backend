<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InterviewDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'interview_id',    // مفتاح الربط مع الرأس
        'application_id',  // مفتاح الربط مع حركة التقديم (المرشح)
        'InterviewTime',   // وقت المقابلة الدقيق (مثال: 09:30)
        'EvaluationScore', // الدرجة
        'Result',          // النتيجة (Pending, Accepted, Rejected)
        'Notes',           // ملاحظات المقيّم على هذا الشخص تحديداً
    ];

    protected $casts = [
        'EvaluationScore' => 'decimal:2',
        'InterviewTime' => 'datetime:H:i', // لضمان تنسيق الوقت بالساعات والدقائق
    ];

    /**
     * علاقة التفاصيل بالرأس (المقابلة الأساسية)
     */
    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class, 'interview_id');
    }

    /**
     * علاقة التفاصيل بحركة التقديم (ومن خلالها نصل لبيانات الشخص سواء كان موظف أو متقدم خارجي)
     */
    public function application(): BelongsTo
    {
        // ملاحظة: تأكد أن المودل الخاص بحركات التقديم اسمه Application لديك
        return $this->belongsTo(Application::class, 'application_id');
    }
}
