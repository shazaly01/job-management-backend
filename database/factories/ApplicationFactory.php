<?php

namespace Database\Factories;

use App\Models\Applicant;
use App\Models\Application;
use App\Models\JobRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    // هنا كان الخطأ، أضفنا ::class
    protected $model = Application::class;

    public function definition(): array
    {
        return [
            // رقم معاملة طويل جداً (15 خانة)
            'TransactionNo' => $this->faker->numerify('###############'),
            'applicant_id' => Applicant::factory(),
            'job_request_id' => JobRequest::factory(),
            'ApplicationStatus' => $this->faker->randomElement(['Pending', 'Shortlisted', 'Interview', 'Accepted', 'Rejected']),
        ];
    }
}
