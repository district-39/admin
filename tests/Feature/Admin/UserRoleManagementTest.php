<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Vite;
use Illuminate\Support\HtmlString;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    app()->instance(Vite::class, new class extends Vite
    {
        public function __invoke($entrypoints, $buildDirectory = null): HtmlString
        {
            return new HtmlString('');
        }
    });
});

function createAdminUser(): User
{
    $user = User::factory()->create();
    $role = Role::factory()->admin()->create();
    $user->roles()->attach($role);

    return $user;
}

test('admin can view users index', function () {
    $admin = createAdminUser();

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/users/Index')
            ->has('users')
            ->has('roles')
        );
});

test('non-admin cannot view users index', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

test('admin can assign roles to a user', function () {
    $admin = createAdminUser();
    $user = User::factory()->create();
    $roles = Role::factory()->count(2)->create();

    $this->actingAs($admin)
        ->put(route('admin.users.roles.update', $user), [
            'role_ids' => $roles->pluck('id')->toArray(),
        ])
        ->assertRedirect(route('admin.users.index'));

    $user->refresh();
    expect($user->roles)->toHaveCount(2);
});

test('admin can remove all roles from a user', function () {
    $admin = createAdminUser();
    $user = User::factory()->create();
    $roles = Role::factory()->count(2)->create();
    $user->roles()->attach($roles);

    $this->actingAs($admin)
        ->put(route('admin.users.roles.update', $user), [
            'role_ids' => [],
        ])
        ->assertRedirect(route('admin.users.index'));

    $user->refresh();
    expect($user->roles)->toHaveCount(0);
});

test('admin can change a users roles', function () {
    $admin = createAdminUser();
    $user = User::factory()->create();
    $oldRole = Role::factory()->create(['slug' => 'old-role', 'name' => 'Old Role']);
    $newRole = Role::factory()->create(['slug' => 'new-role', 'name' => 'New Role']);
    $user->roles()->attach($oldRole);

    $this->actingAs($admin)
        ->put(route('admin.users.roles.update', $user), [
            'role_ids' => [$newRole->id],
        ])
        ->assertRedirect(route('admin.users.index'));

    $user->refresh();
    expect($user->roles)->toHaveCount(1)
        ->and($user->roles->first()->slug)->toBe('new-role');
});

test('non-admin cannot assign roles', function () {
    $user = User::factory()->create();
    $target = User::factory()->create();
    $role = Role::factory()->create();

    $this->actingAs($user)
        ->put(route('admin.users.roles.update', $target), [
            'role_ids' => [$role->id],
        ])
        ->assertForbidden();
});

test('role_ids must be valid role ids', function () {
    $admin = createAdminUser();
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->put(route('admin.users.roles.update', $user), [
            'role_ids' => [99999],
        ])
        ->assertSessionHasErrors('role_ids.0');
});

test('role_ids must be an array', function () {
    $admin = createAdminUser();
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->put(route('admin.users.roles.update', $user), [
            'role_ids' => 'not-an-array',
        ])
        ->assertSessionHasErrors('role_ids');
});
