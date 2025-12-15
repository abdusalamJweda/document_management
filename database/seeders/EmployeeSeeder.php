<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Employee::factory()->count(30)->create();
        Employee::create([
            'name' => 'Hend Bengoula',
            'email' => 'h.bengoulla@tfgroup.ly',
            'type' => 'admin',
            'job_title' => 'Admin',
            'employee_id' => '3211',
            'department_id' => 1,
        ]);
        Employee::create([
            'name' => 'Abdusalam Jweda',
            'email' => 'a.jweda@tfgroup.ly',
            'type' => 'admin',
            'job_title' => 'Admin',
            'employee_id' => '3221',
            'department_id' => 3,
        ]);
        Employee::create([
            'name' => 'Muhaned Zbeda',
            'email' => 'm.zbeda@tfgroup.ly',
            // 'type' => 'admin',
            'job_title' => 'Head',
            'employee_id' => '3121',
            'department_id' => 3,
        ]);
        Employee::create([
            'name' => 'Aziz Badi',
            'email' => 'a.badi@tfgroup.ly',
            // 'type' => 'admin',
            'job_title' => 'Machine operator',
            'employee_id' => '1121',
            'department_id' => 5,
        ]);
    }
}