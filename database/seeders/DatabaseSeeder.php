<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Jayrous Eliakimu',
            'email' => 'joshuajayrous@gmail.com',
            'role' => "Secretary",
        ]);
        User::factory()->create([
            'name' => 'Laiza Charles',
            'email' => 'laiza@gmail.com',
            'role' => "Accountant",
        ]);
        User::factory()->create([
            'name' => 'Charles Eliakimu',
            'email' => 'charles@gmail.com',
            'role' => "Member",
        ]);
        User::factory()->create([
            'name' => 'Janson Eliakimu',
            'email' => 'janson@gmail.com',
            'role' => "Member",
        ]);
        User::factory()->create();

        Role::factory()->create(['name' => 'Secretary']);
        Role::factory()->create(['name' => 'Accountant']);
        Role::factory()->create(['name' => 'Member']);

    }
}