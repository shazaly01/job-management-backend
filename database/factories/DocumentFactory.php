<?php

namespace Database\Factories;

use App\Models\Applicant;
use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . '.pdf',
            'file_path' => 'documents/test/' . $this->faker->uuid() . '.pdf',
            'DocumentType' => $this->faker->randomElement(['CV', 'National ID', 'Passport', 'Degree']),

            // إعداد افتراضي للربط بمتقدم
            'documentable_id' => Applicant::factory(),
            'documentable_type' => Applicant::class,
        ];
    }
}
