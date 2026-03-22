<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Full administrative access'],
            ['name' => 'DSM', 'slug' => 'dsm', 'description' => 'District Service Member'],
            ['name' => 'aDSM', 'slug' => 'adsm', 'description' => 'Alternate District Service Member'],
            ['name' => 'Grapevine Rep', 'slug' => 'grapevine-rep', 'description' => 'Grapevine Representative'],
            ['name' => 'District Secretary', 'slug' => 'dsecretary', 'description' => 'District Secretary'],
            ['name' => 'District Treasurer', 'slug' => 'dtreasurer', 'description' => 'District Treasurer'],
            ['name' => 'Notetaker', 'slug' => 'notetaker', 'description' => 'Meeting Notetaker'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
