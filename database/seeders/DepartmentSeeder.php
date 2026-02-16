<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'BSO', 'description' => 'Business Services & Outsourcing'],
            ['name' => 'Audit', 'description' => 'Audit & Assurance Services'],
            ['name' => 'Administration', 'description' => 'General administration'],
            ['name' => 'Consulting', 'description' => 'Consulting Services'],
            ['name' => 'Training', 'description' => 'Professional Training Services'],
        ];

        foreach ($departments as $department) {
            DB::table('departments')->insert([
                'name' => $department['name'],
                'description' => $department['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
