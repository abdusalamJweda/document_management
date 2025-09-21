<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Dom\Document;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DocumentType::create([
            'name' => 'Passport',
            'description' => 'A passport document',
            'expiry_duration_days' => 365,
        ]);
        DocumentType::create([
            'name' => 'Clearance Certificate',
            'description' => 'A Certificate of Clearance document',
            'expiry_duration_days' => 365,
        ]);
        DocumentType::create([
            'name' => 'Health certificate',
            'description' => 'A health certificate document',
            'expiry_duration_days' => 365,
        ]);
        DocumentType::create([
            'name' => 'Utility Bill',
            'description' => 'A utility bill document',
            'expiry_duration_days' => 365,
        ]);
        // DocumentType::factory(5)->create(); // Creates 5 dummy document types
    }
}
