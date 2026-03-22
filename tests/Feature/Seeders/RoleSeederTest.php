<?php

use App\Models\Role;
use Database\Seeders\RoleSeeder;

test('role seeder creates all expected roles', function () {
    $this->seed(RoleSeeder::class);

    expect(Role::pluck('slug')->sort()->values()->all())->toBe([
        'admin',
        'adsm',
        'dsecretary',
        'dsm',
        'dtreasurer',
        'grapevine-rep',
        'notetaker',
    ]);
});

test('role seeder does not duplicate roles when run twice', function () {
    $this->seed(RoleSeeder::class);
    $this->seed(RoleSeeder::class);

    expect(Role::count())->toBe(7);
});

test('role seeder creates roles with correct names and descriptions', function (string $slug, string $name, string $description) {
    $this->seed(RoleSeeder::class);

    $role = Role::where('slug', $slug)->first();

    expect($role)->not->toBeNull()
        ->and($role->name)->toBe($name)
        ->and($role->description)->toBe($description);
})->with([
    ['admin', 'Admin', 'Full administrative access'],
    ['dsm', 'DSM', 'District Service Member'],
    ['adsm', 'aDSM', 'Alternate District Service Member'],
    ['grapevine-rep', 'Grapevine Rep', 'Grapevine Representative'],
    ['dsecretary', 'District Secretary', 'District Secretary'],
    ['dtreasurer', 'District Treasurer', 'District Treasurer'],
    ['notetaker', 'Notetaker', 'Meeting Notetaker'],
]);
