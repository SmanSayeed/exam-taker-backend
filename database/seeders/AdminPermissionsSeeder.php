<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions using 'admin-api' guard
        $permissions = [
            'view users',
            'edit users',
            'delete users',
            'create users',
            'view settings',
            'edit settings',
            'view posts',
            'edit posts',
            'create posts',
            'delete posts',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'admin-api']);
        }

        // Create roles using 'admin-api' guard
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin-api']);
        $subAdminRole = Role::firstOrCreate(['name' => 'sub_admin', 'guard_name' => 'admin-api']);
        $editorRole = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'admin-api']);

        // Assign permissions to roles
        $adminRole->givePermissionTo(Permission::all());

        $subAdminRole->givePermissionTo([
            'view users',
            'edit users',
            'create users',
            'view posts',
            'edit posts',
            'create posts',
        ]);

        $editorRole->givePermissionTo([
            'view posts',
            'edit posts',
            'create posts',
        ]);

        // Optional: Create an initial user and assign the admin role
        // Ensure you have a User model and it's using the HasRoles trait from Spatie
        // Uncomment and adjust the following lines based on your needs
        // $user = \App\Models\Admin::firstOrCreate([
        //     'name' => 'Admin User',
        //     'email' => 'admin@example.com',
        //     'password' => bcrypt('password')
        // ]);
        // $user->assignRole($adminRole);
    }
}
