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
        Employee::factory()->count(30)->create();
        Employee::create([
            'first_name' => 'Hend',
            'last_name' => 'Bengoula',
            'email' => 'h.bengoulla@tfgroup.ly',
            'type' => 'admin',
            'job_title' => 'Admin',
            'employee_id' => '3211',
        ]);
        Employee::create([
            'first_name' => 'Abdusalam',
            'last_name' => 'Jwedaa',
            'email' => 'a.jweda@tfgroup.ly',
            'type' => 'admin',
            'job_title' => 'Admin',
            'employee_id' => '3221',
        ]);
    }
}