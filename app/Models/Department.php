<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- Add this import

class Department extends Model
{
    use HasFactory, SoftDeletes; // <-- Add HasFactory here

    protected $fillable = [
        'DeptCode',
        'Name',
        'IsActive',
    ];

    protected $casts = [
        'DeptCode' => 'decimal:0', // Important for the 18,0 rule
    ];

    // Relationship: A department can have many job requests
    public function jobRequests()
    {
        return $this->hasMany(JobRequest::class);
    }
}
