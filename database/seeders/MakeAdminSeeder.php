<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MakeAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure 'admin' role exists
        $roleId = DB::table('roles')->where('role_name', 'admin')->value('id');

        if (!$roleId) {
            $roleId = DB::table('roles')->insertGetId([
                'role_name' => 'admin',
                'description' => 'Administrator',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Find user by IC
        $user = DB::table('users')->where('ic', '910101010101')->first();

        if ($user) {
            // Assign role and activate; do not overwrite password
            DB::table('users')->where('id', $user->id)->update([
                'role_id' => $roleId,
                'status' => 'active',
                'updated_at' => now(),
            ]);
        } else {
            // Create a new admin user with a default password
            DB::table('users')->insert([
                'name' => 'Admin',
                'ic' => '910101010101',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role_id' => $roleId,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
