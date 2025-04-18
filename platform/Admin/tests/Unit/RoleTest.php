<?php

use Platform\Admin\Models\Role;
use Platform\Admin\Models\Admin;
use Platform\Admin\Models\Permission;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class);
uses(RefreshDatabase::class);

test('role can be created', function () {
    $role = Role::factory()->create([
        'name' => 'editor',
        'display_name' => 'Content Editor',
        'description' => 'Can edit content',
    ]);
    
    expect($role)->toBeInstanceOf(Role::class)
        ->and($role->name)->toBe('editor')
        ->and($role->display_name)->toBe('Content Editor')
        ->and($role->description)->toBe('Can edit content');
});

test('role can be assigned to admins', function () {
    $role = Role::factory()->create(['name' => 'editor']);
    $admin1 = Admin::factory()->create();
    $admin2 = Admin::factory()->create();
    
    $role->admins()->attach([$admin1->id, $admin2->id]);
    
    expect($role->admins)->toHaveCount(2)
        ->and($role->admins->pluck('id')->toArray())->toContain($admin1->id, $admin2->id);
});

test('role can be assigned permissions', function () {
    $role = Role::factory()->create(['name' => 'editor']);
    $permission1 = Permission::factory()->create(['name' => 'create-post']);
    $permission2 = Permission::factory()->create(['name' => 'edit-post']);
    
    $role->permissions()->attach([$permission1->id, $permission2->id]);
    
    expect($role->permissions)->toHaveCount(2)
        ->and($role->permissions->pluck('name')->toArray())->toContain('create-post', 'edit-post');
});

test('role can check for permissions', function () {
    $role = Role::factory()->create(['name' => 'editor']);
    $permission1 = Permission::factory()->create(['name' => 'create-post']);
    $permission2 = Permission::factory()->create(['name' => 'edit-post']);
    
    $role->permissions()->attach([$permission1->id]);
    
    expect($role->hasPermission('create-post'))->toBeTrue()
        ->and($role->hasPermission('edit-post'))->toBeFalse();
});

test('role can assign permissions', function () {
    $role = Role::factory()->create(['name' => 'editor']);
    $permission1 = Permission::factory()->create(['name' => 'create-post']);
    $permission2 = Permission::factory()->create(['name' => 'edit-post']);
    $permission3 = Permission::factory()->create(['name' => 'delete-post']);
    
    // Assign initial permissions
    $role->assignPermissions([$permission1->id, $permission2->id]);
    
    expect($role->permissions)->toHaveCount(2)
        ->and($role->permissions->pluck('id')->toArray())->toContain($permission1->id, $permission2->id);
    
    // Change permissions
    $role->assignPermissions([$permission2->id, $permission3->id]);
    $role->refresh();
    
    expect($role->permissions)->toHaveCount(2)
        ->and($role->permissions->pluck('id')->toArray())->toContain($permission2->id, $permission3->id)
        ->and($role->permissions->pluck('id')->toArray())->not->toContain($permission1->id);
});

test('role factory creates valid instances', function () {
    $role = Role::factory()->create();
    
    expect($role)->toBeInstanceOf(Role::class)
        ->and($role->name)->not->toBeEmpty()
        ->and($role->display_name)->not->toBeEmpty()
        ->and($role->description)->not->toBeEmpty();
});

test('role factory can create admin role', function () {
    $role = Role::factory()->admin()->create();
    
    expect($role->name)->toBe('admin')
        ->and($role->display_name)->toBe('Administrator')
        ->and($role->description)->toBe('Administrator with full access');
});
