<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('community_stem_programs', function (Blueprint $table) {
            $table->unsignedBigInteger('source_program_id')->nullable()->unique();
            $table->string('status')->nullable();
            $table->string('past_research_id')->nullable();
            $table->string('community_industry_name')->nullable();
            $table->string('organiser')->nullable();
            $table->string('sponsorship_value')->nullable();
            $table->text('brief_benefit')->nullable();
            $table->unsignedTinyInteger('star_rating')->nullable();
            $table->text('remarks')->nullable();
            $table->text('link_evidence')->nullable();
            $table->string('evidence_file')->nullable();
            $table->text('link_borang_a')->nullable();
            $table->text('link_kelulusan_kertas_kerja')->nullable();
            $table->text('link_borang_b')->nullable();
            $table->text('link_borang_c')->nullable();
            $table->text('link_borang_d')->nullable();
            $table->text('combined_evidence_folder')->nullable();
            $table->string('identity_key')->nullable();
            $table->string('sheet_sync_id')->nullable();
            $table->string('sheet_source')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('community_stem_programs', function (Blueprint $table) {
            $table->dropUnique(['source_program_id']);
            $table->dropColumn([
                'source_program_id',
                'status',
                'past_research_id',
                'community_industry_name',
                'organiser',
                'sponsorship_value',
                'brief_benefit',
                'star_rating',
                'remarks',
                'link_evidence',
                'evidence_file',
                'link_borang_a',
                'link_kelulusan_kertas_kerja',
                'link_borang_b',
                'link_borang_c',
                'link_borang_d',
                'combined_evidence_folder',
                'identity_key',
                'sheet_sync_id',
                'sheet_source',
            ]);
        });
    }
};
