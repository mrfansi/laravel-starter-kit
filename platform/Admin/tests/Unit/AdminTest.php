<?php

use Platform\Admin\Models\Admin;
use Platform\Admin\Models\Role;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class);
uses(RefreshDatabase::class);

test('admin can be created', function () {
    $admin = Admin::factory()->create([
        'name' => 'Test Admin',
        'email' => 'test@example.com',
    ]);
    
    expect($admin)->toBeInstanceOf(Admin::class)
        ->and($admin->name)->toBe('Test Admin')
        ->and($admin->email)->toBe('test@example.com')
        ->and($admin->is_active)->toBeTrue();
});

test('admin can be assigned roles', function () {
    $admin = Admin::factory()->create();
    $role = Role::factory()->create(['name' => 'editor']);
    
    $admin->roles()->attach($role->id);
    
    expect($admin->roles)->toHaveCount(1)
        ->and($admin->roles->first()->name)->toBe('editor');
});

test('admin can check for roles', function () {
    $admin = Admin::factory()->create();
    $editorRole = Role::factory()->create(['name' => 'editor']);
    $managerRole = Role::factory()->create(['name' => 'manager']);
    $adminRole = Role::factory()->create(['name' => 'admin']);
    
    // Assign editor and manager roles
    $admin->roles()->attach([$editorRole->id, $managerRole->id]);
    
    // Test hasRole
    expect($admin->hasRole('editor'))->toBeTrue()
        ->and($admin->hasRole('manager'))->toBeTrue()
        ->and($admin->hasRole('admin'))->toBeFalse();
    
    // Test hasAnyRole
    expect($admin->hasAnyRole(['editor', 'admin']))->toBeTrue()
        ->and($admin->hasAnyRole(['admin', 'super-admin']))->toBeFalse();
    
    // Test hasAllRoles
    expect($admin->hasAllRoles(['editor', 'manager']))->toBeTrue()
        ->and($admin->hasAllRoles(['editor', 'manager', 'admin']))->toBeFalse();
});

test('admin factory creates active admins by default', function () {
    $admin = Admin::factory()->create();
    expect($admin->is_active)->toBeTrue();
});

test('admin factory can create inactive admins', function () {
    $admin = Admin::factory()->inactive()->create();
    expect($admin->is_active)->toBeFalse();
});
