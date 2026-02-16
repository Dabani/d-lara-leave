<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create admin user
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@b-kelanainternational.com',
            'password' => Hash::make('Admin@123'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create test users
        $testUsers = [
            [
                'name' => 'Mujawayezu Beatrice',
                'email' => 'bmujawayezu@b-kelanainternational.com',
                'password' => Hash::make('Test@12345'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Habarugira Jean Baptiste',
                'email' => 'jbhabarugira@b-kelanainternational.com',
                'password' => Hash::make('Test@12345'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Makuta Robert',
                'email' => 'rmakuta@b-kelanainternational.com',
                'password' => Hash::make('Test@12345'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ];

        DB::table('users')->insert($testUsers);
    }
}
