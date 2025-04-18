<?php

use Tests\TestCase;

uses(TestCase::class);

test('dashboard route exists', function () {
    $response = $this->get(route('admin.dashboard'));
    
    // We're not checking authentication here, just that the route exists
    $response->assertStatus(302); // Redirect to login if not authenticated
});

test('login route exists', function () {
    $response = $this->get(route('admin.login'));
    
    $response->assertStatus(200);
});
