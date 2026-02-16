<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateExistingUsersGenderSeeder extends Seeder
{
    public function run(): void
    {
        // This is optional - you may want to manually set gender for existing users
        // or create a form for users to update their profiles
        
        // Example: Set all existing users to null (they'll need to update)
        User::whereNull('gender')->update(['gender' => null]);
    }
}