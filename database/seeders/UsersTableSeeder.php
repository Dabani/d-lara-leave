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
            'email' => 'admin@example.com',
            'password' => Hash::make('Admin@123'),
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create test users
        $testUsers = [
            [
                'name' => 'Managing Partner',
                'email' => 'mp@example.com',
                'password' => Hash::make('Test@12345'),
                'role' => 'managing-partner',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Assessor',
                'email' => 'hod@example.com',
                'password' => Hash::make('Test@12345'),
                'role' => 'assessor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Employee',
                'email' => 'user@example.com',
                'password' => Hash::make('Test@12345'),
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ];

        DB::table('users')->insert($testUsers);
    }
}
