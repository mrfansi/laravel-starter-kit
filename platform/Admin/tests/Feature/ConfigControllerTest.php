<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class);
uses(RefreshDatabase::class);

test('config routes are protected', function () {
    // Visit the configurations index page without authentication
    $response = $this->get(route('admin.config.index'));
    
    // Should redirect to login page if not authenticated
    $response->assertStatus(302);
    $response->assertRedirect(route('admin.login'));
});

test('config create route is protected', function () {
    // Visit the create configuration page without authentication
    $response = $this->get(route('admin.config.create'));
    
    // Should redirect to login page if not authenticated
    $response->assertStatus(302);
    $response->assertRedirect(route('admin.login'));
});

test('config store route is protected', function () {
    // Submit the form to create a new configuration without authentication
    $response = $this->post(route('admin.config.store'), [
        'key' => 'new_test_config',
        'value' => 'test_value',
        'type' => 'string',
        'group' => 'testing',
        'description' => 'This is a test configuration',
    ]);
    
    // Should redirect to login page if not authenticated
    $response->assertStatus(302);
    $response->assertRedirect(route('admin.login'));
});
