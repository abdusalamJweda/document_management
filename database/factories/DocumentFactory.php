<?php

namespace Database\Factories;

use App\Models\DocumentType;
use App\Models\Employee;
use Dom\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $issuedDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $submissionDate = $this->faker->dateTimeBetween($issuedDate, 'now');

        return [
            'employee_id' => Employee::inRandomOrder()->first()->id,
            'document_type_id' => DocumentType::inRandomOrder()->first()->id,
            'issued_date' => $issuedDate,
            'submission_date' => $submissionDate,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($document) {
            // Using a fake image URL from Faker
            $document->addMediaFromUrl("https://s2.q4cdn.com/175719177/files/doc_presentations/Placeholder-PDF.pdf")
                ->toMediaCollection('files');
        });
    }
}