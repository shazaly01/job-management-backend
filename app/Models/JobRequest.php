<?php

namespace App\Models;

use Illuminate\Support\Str; // استيراد كلاس النصوص
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'RequestNo',
        'slug', // ✅ إضافة الحقل الجديد هنا
        'department_id',
        'RequiredDegreeLevel',
        'RequiredMajor',
        'RequiredYearsOfExperience',
        'Status',
        'JobDescription', // أضفته تحسباً لوجود وصف الوظيفة في شاشتك
    ];

    protected $casts = [
        'RequestNo' => 'decimal:0', // القاعدة الذهبية الخاصة بك (18,0)
    ];

    /**
     * منطق الأحداث (Events): توليد الـ Slug تلقائياً قبل الحفظ.
     */
 protected static function booted()
{
    static::creating(function ($jobRequest) {
        // 1. توليد رقم الطلب تلقائياً إذا لم يكن موجوداً
        // نستخدم الإدارة + التاريخ + رقم عشوائي (يتناسب مع قاعدتك DECIMAL 18,0)
        if (!$jobRequest->RequestNo) {
            $deptId = $jobRequest->department_id ?? rand(1, 9);
            $jobRequest->RequestNo = $deptId . now()->format('ymd') . rand(1000, 9999);
        }

        // 2. توليد الـ Slug (يعتمد على الرقم الذي ولدناه بالأعلى)
        if (empty($jobRequest->slug)) {
            // نستخدم Str::slug لتحويل النص لرابط
            // مثال: "مهندس-كهربائي-32603294512"
            $jobRequest->slug = \Illuminate\Support\Str::slug($jobRequest->RequiredMajor . '-' . $jobRequest->RequestNo);
        }
    });
}
    // --- العلاقات (Relationships) ---

    // طلب التوظيف يتبع لقسم معين
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    // طلب التوظيف يمتلك العديد من التقديمات
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    // علاقة إضافية: المقابلات المرتبطة بهذا الطلب (Master-Detail)
    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class);
    }
}
