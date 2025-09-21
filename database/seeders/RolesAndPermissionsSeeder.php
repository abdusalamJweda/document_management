<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();


        Role::create(['name' => 'admin']);


        Role::create(['name' => 'user']);


        $role3 = Role::create(['name' => 'super-admin']);
        // super-admin gets all permissions via a wildcard
    }
}