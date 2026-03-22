<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware(['web', 'role:admin'])->get('/test-admin', fn () => 'ok')->name('test.admin');
    Route::middleware(['web', 'role:admin,dsm'])->get('/test-multi', fn () => 'ok')->name('test.multi');
});

test('user with required role can access the route', function () {
    $user = User::factory()->create();
    $role = Role::factory()->admin()->create();
    $user->roles()->attach($role);

    $this->actingAs($user)
        ->get('/test-admin')
        ->assertSuccessful();
});

test('user without required role gets 403', function () {
    $user = User::factory()->create();
    Role::factory()->admin()->create();

    $this->actingAs($user)
        ->get('/test-admin')
        ->assertForbidden();
});

test('unauthenticated user gets redirected', function () {
    $this->get('/test-admin')
        ->assertForbidden();
});

test('middleware accepts multiple roles with OR logic', function () {
    $user = User::factory()->create();
    $dsmRole = Role::factory()->create(['slug' => 'dsm', 'name' => 'DSM']);
    $user->roles()->attach($dsmRole);

    $this->actingAs($user)
        ->get('/test-multi')
        ->assertSuccessful();
});

test('user with none of multiple required roles gets 403', function () {
    $user = User::factory()->create();
    Role::factory()->create(['slug' => 'secretary', 'name' => 'Secretary']);
    $user->roles()->attach(Role::where('slug', 'secretary')->first());

    $this->actingAs($user)
        ->get('/test-multi')
        ->assertForbidden();
});
