<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('users')->delete();
        
        // Get role IDs
        $roleMap = DB::table('roles')->pluck('id', 'role_name')->toArray();
        
        $users = [
            // Super Admin
            [
                'name' => 'Super Admin',
                'ic' => '803456789012',
                'email' => 'superadmin@utem.edu.my',
                'password' => Hash::make('password'),
                'role_id' => $roleMap['superadmin'] ?? null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Admins for each module
            [
                'name' => 'KPI Admin',
                'ic' => '883456789013',
                'email' => 'kpi.admin@utem.edu.my',
                'password' => Hash::make('password'),
                'role_id' => $roleMap['admin'] ?? null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Awards Admin',
                'ic' => '783456789014',
                'email' => 'awards.admin@utem.edu.my',
                'password' => Hash::make('password'),
                'role_id' => $roleMap['admin'] ?? null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Products Admin',
                'ic' => '903456789015',
                'email' => 'products.admin@utem.edu.my',
                'password' => Hash::make('password'),
                'role_id' => $roleMap['admin'] ?? null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'IP Admin',
                'ic' => '863456789016',
                'email' => 'ip.admin@utem.edu.my',
                'password' => Hash::make('password'),
                'role_id' => $roleMap['admin'] ?? null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Community Admin',
                'ic' => '853456789017',
                'email' => 'community.admin@utem.edu.my',
                'password' => Hash::make('password'),
                'role_id' => $roleMap['admin'] ?? null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'STEM Admin',
                'ic' => '813456789018',
                'email' => 'stem.admin@utem.edu.my',
                'password' => Hash::make('password'),
                'role_id' => $roleMap['admin'] ?? null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Section Staff for each module
            [
                'name' => 'KPI Staff',
                'ic' => '123456789019',
                'email' => 'kpi.staff@utem.edu.my',
                'password' => Hash::make('password'),
                'role_id' => $roleMap['staff'] ?? null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Awards Staff',
                'ic' => '123456789020',
                'email' => 'awards.staff@utem.edu.my',
                'password' => Hash::make('password'),
                'role_id' => $roleMap['staff'] ?? null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Products Staff',
                'ic' => '123456789021',
                'email' => 'products.staff@utem.edu.my',
                'password' => Hash::make('password'),
                'role_id' => $roleMap['staff'] ?? null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'IP Staff',
                'ic' => '123456789022',
                'email' => 'ip.staff@utem.edu.my',
                'password' => Hash::make('password'),
                'role_id' => $roleMap['staff'] ?? null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Community Staff',
                'ic' => '123456789023',
                'email' => 'community.staff@utem.edu.my',
                'password' => Hash::make('password'),
                'role_id' => $roleMap['staff'] ?? null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'STEM Staff',
                'ic' => '123456789024',
                'email' => 'stem.staff@utem.edu.my',
                'password' => Hash::make('password'),
                'role_id' => $roleMap['staff'] ?? null,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }
    }
}
