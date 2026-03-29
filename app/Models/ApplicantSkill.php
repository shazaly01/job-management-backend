<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ApplicantSkill extends Model
{
     use HasFactory, SoftDeletes;

    protected $fillable = [
        'applicant_id',
        'SkillName',
        'ProficiencyLevel',
    ];

    // علاقة: هذه المهارة تعود لمتقدم واحد
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Applicant::class);
    }
}
