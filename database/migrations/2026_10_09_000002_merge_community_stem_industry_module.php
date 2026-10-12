<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['modules', 'user_modules'] as $table) {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                throw new RuntimeException("Cannot consolidate module assignments: table '{$table}' is missing.");
            }
        }

        $legacyNames = ['Community', 'STEM', 'Industry', 'Industri', 'Community/STEM/Industry'];
        $modules = DB::table('modules')
            ->whereIn('module_name', $legacyNames)
            ->orderBy('id')
            ->get(['id', 'module_name']);

        $target = $modules->firstWhere('module_name', 'Community/STEM/Industry');
        $targetId = $target?->id ?? DB::table('modules')->insertGetId([
            'module_name' => 'Community/STEM/Industry',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $moduleIds = $modules->pluck('id')->all();
        if (!in_array($targetId, $moduleIds, true)) {
            $moduleIds[] = $targetId;
        }

        $userIds = DB::table('user_modules')
            ->whereIn('module_id', $moduleIds)
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id');

        DB::table('user_modules')->whereIn('module_id', $moduleIds)->delete();

        foreach ($userIds->chunk(200) as $userIdChunk) {
            DB::table('user_modules')->insert(
                $userIdChunk->map(fn ($userId) => [
                    'user_id' => $userId,
                    'module_id' => $targetId,
                    'created_at' => now(),
                ])->all()
            );
        }

        DB::table('modules')
            ->whereIn('id', $moduleIds)
            ->where('id', '!=', $targetId)
            ->delete();
    }

    public function down(): void
    {
        throw new RuntimeException('The merged Community/STEM/Industry assignments cannot be split back safely.');
    }
};
