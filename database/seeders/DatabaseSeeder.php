<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // utilisateur fixe pour les tests
        $user = User::create([
            'firstname' => 'user',
            'lastname' => 'user',
            'email' => 'user@gmail.com',
            'password' => Hash::make('password'),
        ]);

        $this->call([
            StatusSeeder::class,
        ]);

        Project::factory(3)
            ->for($user)
            ->create();
    }
}







