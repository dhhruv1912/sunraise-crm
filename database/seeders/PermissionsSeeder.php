<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionsSeeder extends Seeder
{
    public function run()
    {
        // define permissions
        $perms = [
            'view dashboard',
            'view users',
            'manage users',
            'view settings',
            'manage settings',
            // add other permissions as needed
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo(Permission::all());

        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $manager->givePermissionTo(['view dashboard', 'view users', 'view settings']);

        $staff = Role::firstOrCreate(['name' => 'Staff']);
        $staff->givePermissionTo(['view dashboard']);

        // optionally assign admin role to first user (adjust id or find by email)
        $user = User::first();
        if ($user) {
            $user->assignRole('Admin');
        }
    }
}
