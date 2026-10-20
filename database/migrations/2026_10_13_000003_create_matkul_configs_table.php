<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matkul_configs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('period_id')->constrained('periods')->cascadeOnDelete();
            $table->foreignUuid('matkul_id')->constrained('matkuls')->cascadeOnDelete();
            $table->unsignedTinyInteger('scheduling_semester')->nullable()->comment('semester yang dipakai saat penjadwalan; bisa beda dari curriculum_semester');
            $table->decimal('practicum_hours', 4, 2)->default(0);
            $table->decimal('responsi_hours', 4, 2)->default(0);
            $table->boolean('is_manual')->default(false)->comment('true = dikecualikan dari penjadwalan otomatis');
            $table->unique(['period_id', 'matkul_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matkul_configs');
    }
};
