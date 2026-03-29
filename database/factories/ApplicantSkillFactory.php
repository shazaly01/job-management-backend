<?php

namespace Database\Factories;

use App\Models\Applicant;
use App\Models\ApplicantSkill;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicantSkillFactory extends Factory
{
    protected $model = ApplicantSkill::class;

    public function definition(): array
    {
        return [
            'applicant_id' => Applicant::factory(),
            'SkillName' => $this->faker->randomElement([
                'Laravel', 'PHP', 'SQL Server', 'Project Management',
                'Public Relations', 'Accounting', 'English Language'
            ]),
            'ProficiencyLevel' => $this->faker->randomElement(['Beginner', 'Intermediate', 'Advanced', 'Expert']),
        ];
    }
}
