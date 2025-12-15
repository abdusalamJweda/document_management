<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        User::factory()->create([
            'name' => 'Hend Bengoula',
            'email' => 'h.bengoulla@tfgroup.ly',
            'password' => Hash::make('password'),
        ])->assignRole('admin');
        User::factory(10)->create();
    }
}
