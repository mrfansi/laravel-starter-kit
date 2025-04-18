<?php

namespace Platform\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Platform\Admin\Models\Admin;
use Platform\Admin\Models\Permission;
use Platform\Admin\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin user
        $admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Create roles
        $superAdminRole = Role::create([
            'name' => 'super-admin',
            'display_name' => 'Super Admin',
            'description' => 'Role with all permissions',
        ]);

        $editorRole = Role::create([
            'name' => 'editor',
            'display_name' => 'Editor',
            'description' => 'Role with content management permissions',
        ]);

        $moderatorRole = Role::create([
            'name' => 'moderator',
            'display_name' => 'Moderator',
            'description' => 'Role with moderation permissions',
        ]);

        // Create permissions
        $permissionGroups = [
            'admin' => [
                'admin.access' => 'Access admin panel',
                'admin.settings' => 'Manage admin settings',
            ],
            'user' => [
                'user.view' => 'View users',
                'user.create' => 'Create users',
                'user.edit' => 'Edit users',
                'user.delete' => 'Delete users',
            ],
            'role' => [
                'role.view' => 'View roles',
                'role.create' => 'Create roles',
                'role.edit' => 'Edit roles',
                'role.delete' => 'Delete roles',
            ],
            'content' => [
                'content.view' => 'View content',
                'content.create' => 'Create content',
                'content.edit' => 'Edit content',
                'content.delete' => 'Delete content',
                'content.publish' => 'Publish content',
            ],
        ];

        $allPermissions = [];

        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $name => $displayName) {
                $permission = Permission::create([
                    'name' => $name,
                    'display_name' => $displayName,
                    'group' => $group,
                ]);
                
                $allPermissions[] = $permission->id;

                // Assign content permissions to editor role
                if ($group === 'content') {
                    $editorRole->permissions()->attach($permission->id);
                }

                // Assign user view permission to moderator role
                if ($name === 'user.view' || $group === 'content') {
                    $moderatorRole->permissions()->attach($permission->id);
                }
            }
        }

        // Assign all permissions to super-admin role
        $superAdminRole->permissions()->attach($allPermissions);

        // Assign super-admin role to the admin user
        $admin->roles()->attach($superAdminRole->id);
    }
}
