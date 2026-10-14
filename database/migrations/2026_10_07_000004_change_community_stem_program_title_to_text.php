<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('community_stem_programs', function (Blueprint $table) {
            $table->text('program')->change();
        });
    }

    public function down(): void
    {
        Schema::table('community_stem_programs', function (Blueprint $table) {
            $table->string('program', 255)->change();
        });
    }
};
