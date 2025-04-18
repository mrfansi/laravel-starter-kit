<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class);
uses(RefreshDatabase::class);

test('role routes are protected', function () {
    // Visit the roles index page without authentication
    $response = $this->get(route('admin.roles.index'));
    
    // Should redirect to login page if not authenticated
    $response->assertStatus(302);
    $response->assertRedirect(route('admin.login'));
});

test('role create route is protected', function () {
    // Visit the create role page without authentication
    $response = $this->get(route('admin.roles.create'));
    
    // Should redirect to login page if not authenticated
    $response->assertStatus(302);
    $response->assertRedirect(route('admin.login'));
});

test('role store route is protected', function () {
    // Submit the form to create a new role without authentication
    $response = $this->post(route('admin.roles.store'), [
        'name' => 'editor',
        'display_name' => 'Content Editor',
        'description' => 'Can edit content',
    ]);
    
    // Should redirect to login page if not authenticated
    $response->assertStatus(302);
    $response->assertRedirect(route('admin.login'));
});
