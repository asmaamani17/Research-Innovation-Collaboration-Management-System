<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('user_modules')->delete();
        
        // Get users and modules
        $users = DB::table('users')->select('id', 'email')->get();
        $modules = DB::table('modules')->select('id', 'module_name')->get();
        
        // Create module map
        $moduleMap = $modules->pluck('id', 'module_name')->toArray();
        
        // Assign modules based on user email
        foreach ($users as $user) {
            $moduleIds = [];
            
            // Super Admin gets all modules
            if ($user->email === 'superadmin@utem.edu.my') {
                $moduleIds = array_values($moduleMap);
            }
            // Assign specific module based on email
            elseif (str_contains($user->email, 'kpi.')) {
                $moduleIds[] = $moduleMap['KPI'] ?? null;
            }
            elseif (str_contains($user->email, 'awards.')) {
                $moduleIds[] = $moduleMap['Awards'] ?? null;
            }
            elseif (str_contains($user->email, 'products.')) {
                $moduleIds[] = $moduleMap['Products'] ?? null;
            }
            elseif (str_contains($user->email, 'ip.')) {
                $moduleIds[] = $moduleMap['Intellectual Property'] ?? null;
            }
            elseif (str_contains($user->email, 'community.')) {
                $moduleIds[] = $moduleMap['Community'] ?? null;
            }
            elseif (str_contains($user->email, 'stem.')) {
                $moduleIds[] = $moduleMap['STEM'] ?? null;
            }
            
            // Insert module assignments
            foreach ($moduleIds as $moduleId) {
                if ($moduleId) {
                    DB::table('user_modules')->insert([
                        'user_id' => $user->id,
                        'module_id' => $moduleId,
                        'created_at' => now(),
                    ]);
                }
            }
        }
    }
}
