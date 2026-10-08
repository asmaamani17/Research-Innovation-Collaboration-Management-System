<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trl_products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedTinyInteger('trl_level')->index();
            $table->text('product');
            $table->text('researcher')->nullable();
            $table->string('faculty', 100)->nullable()->index();
            $table->text('category')->nullable();
            $table->string('status', 100)->nullable()->index();
            $table->string('action', 150)->nullable();
            $table->text('abstract')->nullable();
            $table->unsignedSmallInteger('product_year')->nullable()->index();
            $table->string('source_sheet', 20)->nullable();
            $table->unsignedInteger('source_no')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trl_products');
    }
};
