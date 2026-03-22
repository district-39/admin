<?php

use App\Models\Role;
use App\Models\User;

test('role can be created with name, slug, and description', function () {
    $role = Role::factory()->create([
        'name' => 'Test Role',
        'slug' => 'test-role',
        'description' => 'A test role',
    ]);

    expect($role->name)->toBe('Test Role')
        ->and($role->slug)->toBe('test-role')
        ->and($role->description)->toBe('A test role');
});

test('role has many users', function () {
    $role = Role::factory()->create();
    $users = User::factory()->count(3)->create();

    $role->users()->attach($users);

    expect($role->users)->toHaveCount(3);
});

test('user has many roles', function () {
    $user = User::factory()->create();
    $roles = Role::factory()->count(3)->create();

    $user->roles()->attach($roles);

    expect($user->roles)->toHaveCount(3);
});

test('user hasRole returns true when user has the role', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create(['slug' => 'treasurer']);

    $user->roles()->attach($role);
    $user->load('roles');

    expect($user->hasRole('treasurer'))->toBeTrue();
});

test('user hasRole returns false when user does not have the role', function () {
    $user = User::factory()->create();

    expect($user->hasRole('treasurer'))->toBeFalse();
});

test('user hasAnyRole returns true when user has at least one matching role', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create(['slug' => 'dsm']);

    $user->roles()->attach($role);
    $user->load('roles');

    expect($user->hasAnyRole(['dsm', 'adsm']))->toBeTrue();
});

test('user hasAnyRole returns false when user has no matching roles', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create(['slug' => 'secretary']);

    $user->roles()->attach($role);
    $user->load('roles');

    expect($user->hasAnyRole(['dsm', 'adsm']))->toBeFalse();
});

test('user isAdmin returns true for admin role', function () {
    $user = User::factory()->create();
    $role = Role::factory()->admin()->create();

    $user->roles()->attach($role);
    $user->load('roles');

    expect($user->isAdmin())->toBeTrue();
});

test('user isAdmin returns false without admin role', function () {
    $user = User::factory()->create();

    expect($user->isAdmin())->toBeFalse();
});

test('role slug is unique', function () {
    Role::factory()->create(['slug' => 'unique-slug']);

    expect(fn () => Role::factory()->create(['slug' => 'unique-slug']))
        ->toThrow(Exception::class);
});

test('role name is unique', function () {
    Role::factory()->create(['name' => 'Unique Name']);

    expect(fn () => Role::factory()->create(['name' => 'Unique Name']))
        ->toThrow(Exception::class);
});
