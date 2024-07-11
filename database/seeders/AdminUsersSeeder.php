<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUsersSeeder extends Seeder
{
    public function run()
    {
        // Ensure roles are created
        $adminRole = Role::findByName('admin', 'admin-api');
        $subAdminRole = Role::findByName('sub_admin', 'admin-api');
        $editorRole = Role::findByName('editor', 'admin-api');

        // Create admin users and assign roles to them
        $adminUser = Admin::firstOrCreate([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
        ]);
        $adminUser->assignRole($adminRole);

        $subAdminUser = Admin::firstOrCreate([
            'name' => 'Sub Admin',
            'email' => 'subadmin@example.com',
            'password' => Hash::make('password'),
        ]);
        $subAdminUser->assignRole($subAdminRole);

        $editorUser = Admin::firstOrCreate([
            'name' => 'Editor',
            'email' => 'editor@example.com',
            'password' => Hash::make('password'),
        ]);
        $editorUser->assignRole($editorRole);
    }
}
