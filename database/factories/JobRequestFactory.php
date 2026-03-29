<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\JobRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobRequestFactory extends Factory
{
    protected $model = JobRequest::class;

    public function definition(): array
    {
        return [
            // توليد رقم طلب طويل (مثال: 12 خانة)
            'RequestNo' => $this->faker->numerify('############'),
            'department_id' => Department::factory(),
            'RequiredDegreeLevel' => $this->faker->randomElement(['Bachelor', 'Master', 'PhD']),
            'RequiredMajor' => $this->faker->randomElement(['IT', 'HR', 'Finance', 'Legal']),
            'RequiredYearsOfExperience' => $this->faker->numberBetween(1, 15),
            'Status' => $this->faker->randomElement(['Open', 'Closed', 'Fulfilled']),
        ];
    }
}
