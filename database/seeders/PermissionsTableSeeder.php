<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        // Membuat permissions
        $permissions = [
            'create posts',
            'edit posts',
            'show posts',
            'delete posts',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Membuat roles
        $adminRole = Role::create(['name' => 'admin']);
        $editorRole = Role::create(['name' => 'editor']);
        $viewerRole = Role::create(['name' => 'viewer']);

        // Menambahkan permissions ke role
        $adminRole->givePermissionTo($permissions);  // Memberikan semua permissions ke role 'admin'
        $editorRole->givePermissionTo(['create posts', 'edit posts', 'show posts']);  // Untuk role 'editor'
        $viewerRole->givePermissionTo(['show posts']);  // Untuk role 'viewer'
    }
}
