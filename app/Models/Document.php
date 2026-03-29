<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'file_path',
        'DocumentType',
        'documentable_id',
        'documentable_type',
    ];

    // نخبر لارافيل بإرجاع حقل url دائماً مع البيانات
    protected $appends = ['url'];

    /**
     * دالة بناء الرابط الآمن (Accessor)
     */
    public function getUrlAttribute(): ?string
    {
        if ($this->file_path) {
            // توليد رابط موقع صالح لمدة 60 دقيقة
            return URL::signedRoute(
                'documents.download',
                ['document' => $this->id],
                now()->addMinutes(60)
            );
        }

        return null;
    }

    /**
     * العلاقة متعددة الأشكال
     */
    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }
}
