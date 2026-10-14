<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_stem_programs', function (Blueprint $table) {
            $table->id();
            $table->string('project_id')->nullable()->index();
            $table->string('staff_id')->index();
            $table->string('faculty')->nullable();
            $table->string('program');
            $table->string('kategori')->nullable();
            $table->enum('classification', ['Community', 'STEM', 'Industri'])->default('Community');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamp('submitted_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_stem_programs');
    }
};
