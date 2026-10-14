<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_stem_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('source_beneficiary_id')->nullable()->unique();
            $table->foreignId('community_stem_program_id')
                ->constrained('community_stem_programs')
                ->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_stem_beneficiaries');
    }
};
