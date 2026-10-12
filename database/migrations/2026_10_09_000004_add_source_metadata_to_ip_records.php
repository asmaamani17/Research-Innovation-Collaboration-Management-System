<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('ip_records', 'source_record_id')) {
            Schema::table('ip_records', function (Blueprint $table) {
                $table->string('source_record_id', 36)->nullable()->unique();
            });
        }

        $columns = [
            'main_category' => fn (Blueprint $table) => $table->string('main_category')->nullable(),
            'secondary_category' => fn (Blueprint $table) => $table->string('secondary_category')->nullable(),
            'primary_application' => fn (Blueprint $table) => $table->string('primary_application')->nullable(),
            'status_renewal' => fn (Blueprint $table) => $table->string('status_renewal')->nullable(),
        ];

        foreach ($columns as $column => $definition) {
            if (! Schema::hasColumn('ip_records', $column)) {
                Schema::table('ip_records', $definition);
            }
        }
    }

    public function down(): void
    {
        $columns = [
            'status_renewal',
            'primary_application',
            'secondary_category',
            'main_category',
            'source_record_id',
        ];
        $existingColumns = array_values(array_filter(
            $columns,
            fn (string $column) => Schema::hasColumn('ip_records', $column)
        ));

        if ($existingColumns !== []) {
            Schema::table('ip_records', function (Blueprint $table) use ($existingColumns) {
                if (in_array('source_record_id', $existingColumns, true)) {
                    $table->dropUnique(['source_record_id']);
                }

                $table->dropColumn($existingColumns);
            });
        }
    }
};
