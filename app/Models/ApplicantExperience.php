<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicantExperience extends Model
{
     use HasFactory, SoftDeletes;

    protected $fillable = [
        'applicant_id',
        'JobTitle',
        'CompanyName',
        'StartDate',
        'EndDate',
        'JobDescription',
    ];

    // تحويل تواريخ البداية والنهاية لتسهيل التعامل معها ككائنات Carbon في Laravel
    protected $casts = [
        'StartDate' => 'date',
        'EndDate' => 'date',
    ];

    // علاقة: هذه الخبرة تعود لمتقدم واحد
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
