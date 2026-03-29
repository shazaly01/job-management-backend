<?php

namespace Database\Factories;

use App\Models\Applicant;
use App\Models\ApplicantExperience;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicantExperienceFactory extends Factory
{
    protected $model = ApplicantExperience::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-10 years', '-1 year');
        // تاريخ النهاية يكون بعد تاريخ البداية بمدة عشوائية أو يبقى null (لا يزال يعمل)
        $endDate = $this->faker->boolean(80)
            ? $this->faker->dateTimeBetween($startDate, 'now')
            : null;

        return [
            'applicant_id' => Applicant::factory(),
            'JobTitle' => $this->faker->jobTitle(),
            'CompanyName' => $this->faker->company(),
            'StartDate' => $startDate,
            'EndDate' => $endDate,
            'JobDescription' => $this->faker->paragraph(),
        ];
    }
}
