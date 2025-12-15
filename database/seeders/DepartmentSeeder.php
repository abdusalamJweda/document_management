<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Employee::factory()->count(30)->create();
        Department::create([
            'name' => 'Human Resources',
        ]);
        Department::create([
            'name' => 'Information Technology',
        ]);
        Department::create([
            'name' => 'Tadawul Digital Solutions',
        ]);
        Department::create([
            'name' => 'Tadawul Banking Solutions',
        ]);
        Department::create([
            'name' => 'Tadawul Cheques & cards',
        ]);
        Department::create([
            'name' => 'Security',
        ]);
    }
}