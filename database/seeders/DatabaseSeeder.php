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
            'email' => 'jayrouseliakimu@gmail.com',
            'role' => "Secretary",
        ]);
        User::factory()->create([
            'name' => 'Laiza Charles',
            'email' => 'laizacharles@gmail.com',
            'role' => "Accountant",
        ]);


        Role::factory()->create(['name' => 'Secretary']);
        Role::factory()->create(['name' => 'Accountant']);
        Role::factory()->create(['name' => 'Member']);

    }
}