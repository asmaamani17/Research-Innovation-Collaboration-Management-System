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
        $users = DB::table('users')->select('id', 'email')->get();
        $modules = DB::table('modules')->select('id', 'module_name')->get();
        $moduleMap = $modules->pluck('id', 'module_name')->toArray();

        foreach ($users as $user) {
            $moduleIds = [];

            if ($user->email === 'superadmin@utem.edu.my') {
                $moduleIds = array_values($moduleMap);
            } elseif (str_contains(strtolower($user->email), 'kpi.')) {
                $moduleIds[] = $moduleMap['KPI'] ?? null;
            } elseif (str_contains(strtolower($user->email), 'awards.')) {
                $moduleIds[] = $moduleMap['Awards'] ?? null;
            } elseif (str_contains(strtolower($user->email), 'products.')) {
                $moduleIds[] = $moduleMap['Products'] ?? null;
            } elseif (str_contains(strtolower($user->email), 'ip.')) {
                $moduleIds[] = $moduleMap['Intellectual Property'] ?? null;
            } elseif (
                str_contains(strtolower($user->email), 'community.')
                || str_contains(strtolower($user->email), 'stem.')
                || str_contains(strtolower($user->email), 'industry.')
                || str_contains(strtolower($user->email), 'industri.')
            ) {
                $moduleIds[] = $moduleMap['Community/STEM/Industry'] ?? null;
            }

            foreach ($moduleIds as $moduleId) {
                if ($moduleId) {
                    $assignmentExists = DB::table('user_modules')
                        ->where('user_id', $user->id)
                        ->where('module_id', $moduleId)
                        ->exists();

                    if (!$assignmentExists) {
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
}
