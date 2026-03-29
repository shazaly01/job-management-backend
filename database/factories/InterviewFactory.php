<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\Interview;
use Illuminate\Database\Eloquent\Factories\Factory;

class InterviewFactory extends Factory
{
    protected $model = Interview::class;

    public function definition(): array
    {
        return [
            'application_id' => Application::factory(),
            // كود الموظف المقابل (أكثر من 9 أرقام كما طلبت)
            'EmpCode' => $this->faker->numerify('#########'),
            'InterviewDate' => $this->faker->dateTimeBetween('now', '+1 month'),
            'EvaluationScore' => $this->faker->numberBetween(50, 100),
            'Notes' => $this->faker->sentence(),
            'Result' => $this->faker->randomElement(['Passed', 'Failed', 'Pending']),
        ];
    }
}
