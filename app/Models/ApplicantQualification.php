<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicantQualification extends Model
{
     use HasFactory, SoftDeletes;

    protected $fillable = [
        'applicant_id',
        'DegreeLevel',
        'Major',
        'GraduationYear',
        'UniversityOrInstitute',
        'GPA_or_Grade',
    ];

    // علاقة: هذا المؤهل يعود لمتقدم واحد
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }

}
