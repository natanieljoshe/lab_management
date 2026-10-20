<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_sections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('period_id')->constrained('periods')->cascadeOnDelete();
            $table->foreignUuid('matkul_id')->constrained('matkuls')->cascadeOnDelete();
            $table->string('label', 10)->comment('A, B, C, ...');
            $table->unsignedInteger('enrollment')->default(0);
            $table->string('lecturer')->nullable();
            $table->unique(['period_id', 'matkul_id', 'label']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_sections');
    }
};
