<?php

use Platform\Admin\Models\Permission;
use Platform\Admin\Models\Role;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class);
uses(RefreshDatabase::class);

test('permission can be created', function () {
    $permission = Permission::factory()->create([
        'name' => 'create-user',
        'display_name' => 'Create User',
        'description' => 'Ability to create new users',
        'group' => 'users',
    ]);
    
    expect($permission)->toBeInstanceOf(Permission::class)
        ->and($permission->name)->toBe('create-user')
        ->and($permission->display_name)->toBe('Create User')
        ->and($permission->description)->toBe('Ability to create new users')
        ->and($permission->group)->toBe('users');
});

test('permission can be assigned to roles', function () {
    $permission = Permission::factory()->create(['name' => 'create-user']);
    $role1 = Role::factory()->create(['name' => 'admin']);
    $role2 = Role::factory()->create(['name' => 'manager']);
    
    $permission->roles()->attach([$role1->id, $role2->id]);
    
    expect($permission->roles)->toHaveCount(2)
        ->and($permission->roles->pluck('name')->toArray())->toContain('admin', 'manager');
});

test('permission factory creates valid instances', function () {
    $permission = Permission::factory()->create();
    
    expect($permission)->toBeInstanceOf(Permission::class)
        ->and($permission->name)->not->toBeEmpty()
        ->and($permission->display_name)->not->toBeEmpty()
        ->and($permission->description)->not->toBeEmpty()
        ->and($permission->group)->not->toBeEmpty();
});

test('permission factory can create for specific group', function () {
    $permission = Permission::factory()->forGroup('settings')->create();
    
    expect($permission->group)->toBe('settings');
});

test('permissions can be grouped', function () {
    // Create permissions in different groups
    Permission::factory()->count(3)->forGroup('users')->create();
    Permission::factory()->count(2)->forGroup('roles')->create();
    Permission::factory()->count(4)->forGroup('settings')->create();
    
    // Get permissions by group
    $userPermissions = Permission::where('group', 'users')->get();
    $rolePermissions = Permission::where('group', 'roles')->get();
    $settingsPermissions = Permission::where('group', 'settings')->get();
    
    expect($userPermissions)->toHaveCount(3)
        ->and($rolePermissions)->toHaveCount(2)
        ->and($settingsPermissions)->toHaveCount(4);
});
