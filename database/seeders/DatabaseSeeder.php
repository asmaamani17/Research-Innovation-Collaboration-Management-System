<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // User seeder first (for login functionality)
            RoleSeeder::class,
            ModuleSeeder::class,
            UserSeeder::class,

            // Core tables (no foreign key dependencies)
            FacultySeeder::class,
            ProjectSeeder::class,
            ExhibitionLevelSeeder::class,
            ExhibitionResultSeeder::class,
            CompetitionSeeder::class,

            // Dependent tables (requires foreign keys)
            PersonTypeSeeder::class,
            StaffSeeder::class,      // depends on faculties
            CommunityStemProgramSeeder::class,  // depends on staff, projects
            AwardSeeder::class,      // depends on staff, projects, events

            // User module permissions
            UserModuleSeeder::class,

            // KPI data
            KpiSeeder::class,
        ]);
    }
}
