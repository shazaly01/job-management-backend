<?php

namespace Database\Factories;

use App\Models\Applicant;
use App\Models\ApplicantQualification;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicantQualificationFactory extends Factory
{
    protected $model = ApplicantQualification::class;

    public function definition(): array
    {
        return [
            // ربط تلقائي بمتقدم
            'applicant_id' => Applicant::factory(),

            'DegreeLevel' => $this->faker->randomElement(['Bachelor', 'Master', 'PhD', 'Diploma']),
            'Major' => $this->faker->randomElement(['Computer Science', 'Accounting', 'Business Administration', 'Engineering']),
            'GraduationYear' => $this->faker->year(),
            'UniversityOrInstitute' => $this->faker->company() . ' University',
            'GPA_or_Grade' => $this->faker->randomElement(['Excellent', 'Very Good', '3.5/4.0', '90%']),
        ];
    }
}
