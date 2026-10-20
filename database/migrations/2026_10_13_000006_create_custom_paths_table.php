<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_paths', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('period_id')->constrained('periods')->cascadeOnDelete();
            $table->unsignedTinyInteger('semester');
            $table->string('name');
            $table->json('section_ids_json')->nullable()->comment('array UUID class_sections anggota jalur');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_paths');
    }
};
