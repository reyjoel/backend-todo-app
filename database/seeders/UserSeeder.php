<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Test User',
            'email' => 'user@test.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Test User 1',
            'email' => 'user1@test.com',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Test User 2',
            'email' => 'user2@test.com',
            'password' => Hash::make('password'),
        ]);
    }
}
