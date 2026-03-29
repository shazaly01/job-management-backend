<?php

namespace Database\Factories;

use App\Models\Applicant;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicantFactory extends Factory
{
    protected $model = Applicant::class;

    public function definition(): array
    {
        return [
            // أرقام طويلة تتناسب مع متطلباتك (12-15 خانة)
            'ApplicantNo' => $this->faker->numerify('############'),
            'NationalID' => $this->faker->numerify('###############'),
            'ReferenceCode' => $this->faker->numerify('##########'),

            'FirstName' => $this->faker->firstName(),
            'LastName' => $this->faker->lastName(),
            'Email' => $this->faker->unique()->safeEmail(),
            'PhoneNumber' => $this->faker->phoneNumber(),

            // ربط تلقائي بمدينة (سيقوم بإنشاء مدينة جديدة إذا لم تكن موجودة)
            'city_id' => City::factory(),

            'ApplicationSource' => $this->faker->randomElement(['Online', 'Manual']),
            'IsActive' => $this->faker->boolean(100),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * حالة خاصة للمتقدمين غير النشطين
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'IsActive' => false,
        ]);
    }
}
