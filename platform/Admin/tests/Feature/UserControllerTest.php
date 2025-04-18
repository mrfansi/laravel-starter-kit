<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Platform\Admin\Models\Admin;
use Platform\Admin\Models\Permission;
use Platform\Admin\Models\Role;
use Tests\TestCase;

uses(TestCase::class);
uses(RefreshDatabase::class);

test('user index page can be rendered', function () {
    // Create an admin user
    $admin = Admin::factory()->create();

    // Create a role with necessary permissions
    $role = Role::factory()->create(['name' => 'super-admin']);
    $permission = Permission::factory()->create(['name' => 'user.view']);
    $role->permissions()->attach($permission);
    $admin->roles()->attach($role);

    // Act as the admin user
    $this->actingAs($admin, 'admin');

    // Create some users
    Admin::factory()->count(5)->create();

    // Visit the users index page
    $response = $this->get(route('admin.users.index'));

    // Assert the page loads successfully
    $response->assertStatus(200);

    // Assert the view is the expected one
    $response->assertViewIs('admin::users.index');

    // Assert the view has the users data
    $response->assertViewHas('users');
});

test('user index page can filter by search', function () {

    // Create an admin user
    $admin = Admin::factory()->create();

    // Create a role with necessary permissions
    $role = Role::factory()->create(['name' => 'super-admin']);
    $permission = Permission::factory()->create(['name' => 'user.view']);
    $role->permissions()->attach($permission);
    $admin->roles()->attach($role);

    // Act as the admin user
    $this->actingAs($admin, 'admin');

    // Create a specific user for testing search
    Admin::factory()->create([
        'name' => 'Search Test User',
        'email' => 'searchtest@example.com',
    ]);

    // Create some other users
    Admin::factory()->count(5)->create();

    // Visit the users index page with search parameter
    $response = $this->get(route('admin.users.index', ['search' => 'Search Test']));

    // Assert the page loads successfully
    $response->assertStatus(200);

    // Assert the search parameter is passed to the view
    $response->assertViewHas('search', 'Search Test');

    // Get the users from the view
    $users = $response->viewData('users');

    // Assert that the search found our test user
    expect($users->contains('name', 'Search Test User'))->toBeTrue();
});

test('user index page can sort results', function () {

    // Create an admin user
    $admin = Admin::factory()->create();

    // Create a role with necessary permissions
    $role = Role::factory()->create(['name' => 'super-admin']);
    $permission = Permission::factory()->create(['name' => 'user.view']);
    $role->permissions()->attach($permission);
    $admin->roles()->attach($role);

    // Act as the admin user
    $this->actingAs($admin, 'admin');

    // Create some users
    Admin::factory()->count(5)->create();

    // Visit the users index page with sort parameters
    $response = $this->get(route('admin.users.index', [
        'sort_field' => 'name',
        'sort_direction' => 'asc',
    ]));

    // Assert the page loads successfully
    $response->assertStatus(200);

    // Assert the sort parameters are passed to the view
    $response->assertViewHas('sortField', 'name');
    $response->assertViewHas('sortDirection', 'asc');
});

test('user create page can be rendered', function () {

    // Create an admin user
    $admin = Admin::factory()->create();

    // Create a role with necessary permissions
    $role = Role::factory()->create(['name' => 'super-admin']);
    $permission = Permission::factory()->create(['name' => 'user.view']);
    $role->permissions()->attach($permission);
    $admin->roles()->attach($role);

    // Act as the admin user
    $this->actingAs($admin, 'admin');

    // Create some roles for the form
    Role::factory()->count(3)->create();

    // Visit the create user page
    $response = $this->get(route('admin.users.create'));

    // Assert the page loads successfully
    $response->assertStatus(200);

    // Assert the view is the expected one
    $response->assertViewIs('admin::users.create');

    // Assert the view has the roles data
    $response->assertViewHas('roles');
});

test('user can be created', function () {

    // Create an admin user
    $admin = Admin::factory()->create();

    // Create a role with necessary permissions
    $role = Role::factory()->create(['name' => 'super-admin']);
    $permission = Permission::factory()->create(['name' => 'user.view']);
    $role->permissions()->attach($permission);
    $admin->roles()->attach($role);

    // Act as the admin user
    $this->actingAs($admin, 'admin');

    // Create roles to assign
    $roles = Role::factory()->count(2)->create();

    // Submit the form to create a new user
    $response = $this->post(route('admin.users.store'), [
        'name' => 'New Test User',
        'email' => 'newuser@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'roles' => $roles->pluck('id')->toArray(),
    ]);

    // Assert the user is redirected to the index page
    $response->assertRedirect(route('admin.users.index'));

    // Assert the user was created in the database
    $this->assertDatabaseHas('admins', [
        'name' => 'New Test User',
        'email' => 'newuser@example.com',
    ]);

    // Get the created user
    $user = Admin::where('email', 'newuser@example.com')->first();

    // Assert the user has the correct roles
    expect($user->roles)->toHaveCount(2);

    // Assert each role is assigned to the user
    foreach ($roles as $role) {
        expect($user->roles->pluck('id')->toArray())->toContain($role->id);
    }

    // Assert the password was hashed
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

test('user show page can be rendered', function () {

    // Create an admin user
    $admin = Admin::factory()->create();

    // Create a role with necessary permissions
    $role = Role::factory()->create(['name' => 'super-admin']);
    $permission = Permission::factory()->create(['name' => 'user.view']);
    $role->permissions()->attach($permission);
    $admin->roles()->attach($role);

    // Act as the admin user
    $this->actingAs($admin, 'admin');

    // Create a user to show
    $user = Admin::factory()->create();

    // Visit the show user page
    $response = $this->get(route('admin.users.show', $user));

    // Assert the page loads successfully
    $response->assertStatus(200);

    // Assert the view is the expected one
    $response->assertViewIs('admin::users.show');

    // Assert the view has the user data
    $response->assertViewHas('user');
});

test('user edit page can be rendered', function () {

    // Create an admin user
    $admin = Admin::factory()->create();

    // Create a role with necessary permissions
    $role = Role::factory()->create(['name' => 'super-admin']);
    $permission = Permission::factory()->create(['name' => 'user.view']);
    $role->permissions()->attach($permission);
    $admin->roles()->attach($role);

    // Act as the admin user
    $this->actingAs($admin, 'admin');

    // Create a user to edit
    $user = Admin::factory()->create();

    // Create some roles for the form
    Role::factory()->count(3)->create();

    // Visit the edit user page
    $response = $this->get(route('admin.users.edit', $user));

    // Assert the page loads successfully
    $response->assertStatus(200);

    // Assert the view is the expected one
    $response->assertViewIs('admin::users.edit');

    // Assert the view has the necessary data
    $response->assertViewHas('user');
    $response->assertViewHas('roles');
    $response->assertViewHas('selectedRoles');
});

test('user can be updated', function () {

    // Create an admin user
    $admin = Admin::factory()->create();

    // Create a role with necessary permissions
    $role = Role::factory()->create(['name' => 'super-admin']);
    $permission = Permission::factory()->create(['name' => 'user.view']);
    $role->permissions()->attach($permission);
    $admin->roles()->attach($role);

    // Act as the admin user
    $this->actingAs($admin, 'admin');

    // Create a user to update
    $user = Admin::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);

    // Create roles to assign
    $roles = Role::factory()->count(2)->create();

    // Submit the form to update the user
    $response = $this->put(route('admin.users.update', $user), [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'roles' => $roles->pluck('id')->toArray(),
    ]);

    // Assert the user is redirected to the index page
    $response->assertRedirect(route('admin.users.index'));

    // Assert the user was updated in the database
    $this->assertDatabaseHas('admins', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);

    // Refresh the user from the database
    $user->refresh();

    // Assert the user has the correct roles
    expect($user->roles)->toHaveCount(2);

    // Assert each role is assigned to the user
    foreach ($roles as $role) {
        expect($user->roles->pluck('id')->toArray())->toContain($role->id);
    }
});

test('user password can be updated', function () {

    // Create an admin user
    $admin = Admin::factory()->create();

    // Create a role with necessary permissions
    $role = Role::factory()->create(['name' => 'super-admin']);
    $permission = Permission::factory()->create(['name' => 'user.view']);
    $role->permissions()->attach($permission);
    $admin->roles()->attach($role);

    // Act as the admin user
    $this->actingAs($admin, 'admin');

    // Create a user to update
    $user = Admin::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    // Submit the form to update the user's password
    $response = $this->put(route('admin.users.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    // Assert the user is redirected to the index page
    $response->assertRedirect(route('admin.users.index'));

    // Refresh the user from the database
    $user->refresh();

    // Assert the password was updated
    expect(Hash::check('new-password', $user->password))->toBeTrue();
    expect(Hash::check('old-password', $user->password))->toBeFalse();
});

test('user can be deleted', function () {

    // Create an admin user
    $admin = Admin::factory()->create();

    // Create a role with necessary permissions
    $role = Role::factory()->create(['name' => 'super-admin']);
    $permission = Permission::factory()->create(['name' => 'user.view']);
    $role->permissions()->attach($permission);
    $admin->roles()->attach($role);

    // Act as the admin user
    $this->actingAs($admin, 'admin');

    // Create a user to delete
    $user = Admin::factory()->create();

    // Submit the request to delete the user
    $response = $this->delete(route('admin.users.destroy', $user));

    // Assert the user is redirected to the index page
    $response->assertRedirect(route('admin.users.index'));

    // Assert the user was deleted from the database
    $this->assertDatabaseMissing('admins', [
        'id' => $user->id,
    ]);
});
