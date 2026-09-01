<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('roles')->delete();
        
        $roles = [
            [
                'role_name' => 'superadmin',
                'description' => 'Super Administrator with full access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'admin',
                'description' => 'Administrator with limited access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'role_name' => 'staff',
                'description' => 'Staff user with basic access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert($role);
        }
    }
}
