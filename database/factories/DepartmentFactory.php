<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            // توليد رقم من 10 خانات ليتناسب مع DECIMAL(18,0)
            'DeptCode' => $this->faker->numerify('##########'),
            'Name' => $this->faker->jobTitle() . ' Department',
            'IsActive' => $this->faker->boolean(95),
        ];
    }
}
