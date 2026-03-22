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

function createAdmin(): User
{
    $user = User::factory()->create();
    $role = Role::factory()->admin()->create();
    $user->roles()->attach($role);

    return $user;
}

function createNonAdmin(): User
{
    return User::factory()->create();
}

test('admin can view roles index', function () {
    $admin = createAdmin();
    Role::factory()->count(3)->create();

    $this->actingAs($admin)
        ->get(route('admin.roles.index'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/roles/Index')
            ->has('roles', 4) // 3 + admin
        );
});

test('non-admin cannot view roles index', function () {
    $user = createNonAdmin();

    $this->actingAs($user)
        ->get(route('admin.roles.index'))
        ->assertForbidden();
});

test('guest cannot view roles index', function () {
    $this->get(route('admin.roles.index'))
        ->assertRedirect(route('login'));
});

test('admin can view create role form', function () {
    $admin = createAdmin();

    $this->actingAs($admin)
        ->get(route('admin.roles.create'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/roles/Create')
        );
});

test('admin can create a new role', function () {
    $admin = createAdmin();

    $this->actingAs($admin)
        ->post(route('admin.roles.store'), [
            'name' => 'New Role',
            'description' => 'A brand new role',
        ])
        ->assertRedirect(route('admin.roles.index'));

    expect(Role::where('slug', 'new-role')->exists())->toBeTrue();
});

test('creating a role generates a slug from the name', function () {
    $admin = createAdmin();

    $this->actingAs($admin)
        ->post(route('admin.roles.store'), [
            'name' => 'Grapevine Rep',
        ]);

    $role = Role::where('slug', 'grapevine-rep')->first();
    expect($role)->not->toBeNull()
        ->and($role->name)->toBe('Grapevine Rep');
});

test('creating a role requires a name', function () {
    $admin = createAdmin();

    $this->actingAs($admin)
        ->post(route('admin.roles.store'), [
            'name' => '',
        ])
        ->assertSessionHasErrors('name');
});

test('creating a role requires a unique name', function () {
    $admin = createAdmin();
    Role::factory()->create(['name' => 'Existing']);

    $this->actingAs($admin)
        ->post(route('admin.roles.store'), [
            'name' => 'Existing',
        ])
        ->assertSessionHasErrors('name');
});

test('admin can view edit role form', function () {
    $admin = createAdmin();
    $role = Role::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.roles.edit', $role))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/roles/Edit')
            ->has('role')
        );
});

test('admin can update a role', function () {
    $admin = createAdmin();
    $role = Role::factory()->create(['name' => 'Old Name']);

    $this->actingAs($admin)
        ->put(route('admin.roles.update', $role), [
            'name' => 'New Name',
            'description' => 'Updated description',
        ])
        ->assertRedirect(route('admin.roles.index'));

    $role->refresh();
    expect($role->name)->toBe('New Name')
        ->and($role->description)->toBe('Updated description');
});

test('updating a role does not change its slug', function () {
    $admin = createAdmin();
    $role = Role::factory()->create(['name' => 'Original', 'slug' => 'original']);

    $this->actingAs($admin)
        ->put(route('admin.roles.update', $role), [
            'name' => 'Renamed',
        ]);

    $role->refresh();
    expect($role->slug)->toBe('original');
});

test('admin can delete a role', function () {
    $admin = createAdmin();
    $role = Role::factory()->create();

    $this->actingAs($admin)
        ->delete(route('admin.roles.destroy', $role))
        ->assertRedirect(route('admin.roles.index'));

    expect(Role::find($role->id))->toBeNull();
});

test('admin cannot delete the admin role', function () {
    $admin = createAdmin();
    $adminRole = Role::where('slug', 'admin')->first();

    $this->actingAs($admin)
        ->delete(route('admin.roles.destroy', $adminRole))
        ->assertRedirect(route('admin.roles.index'));

    expect(Role::where('slug', 'admin')->exists())->toBeTrue();
});

test('non-admin cannot create a role', function () {
    $user = createNonAdmin();

    $this->actingAs($user)
        ->post(route('admin.roles.store'), [
            'name' => 'Hacker Role',
        ])
        ->assertForbidden();
});

test('non-admin cannot update a role', function () {
    $user = createNonAdmin();
    $role = Role::factory()->create();

    $this->actingAs($user)
        ->put(route('admin.roles.update', $role), [
            'name' => 'Hacked',
        ])
        ->assertForbidden();
});

test('non-admin cannot delete a role', function () {
    $user = createNonAdmin();
    $role = Role::factory()->create();

    $this->actingAs($user)
        ->delete(route('admin.roles.destroy', $role))
        ->assertForbidden();
});
