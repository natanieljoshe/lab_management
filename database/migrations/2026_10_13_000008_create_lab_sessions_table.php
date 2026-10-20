<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('schedule_id')->constrained('schedules')->cascadeOnDelete();
            $table->foreignUuid('class_section_id')->constrained('class_sections')->cascadeOnDelete();
            $table->enum('session_type', ['PRAKTIKUM', 'RESPONSI']);
            $table->unsignedSmallInteger('duration_min');
            $table->enum('status', ['SCHEDULED', 'UNSCHEDULED'])->default('UNSCHEDULED');
            $table->foreignUuid('lab_id')->nullable()->constrained('labs')->nullOnDelete();
            $table->unsignedTinyInteger('day_of_week')->nullable()->comment('0=Minggu … 6=Sabtu; null jika UNSCHEDULED');
            $table->unsignedSmallInteger('start_min')->nullable();
            $table->unsignedSmallInteger('end_min')->nullable();
            $table->string('unscheduled_reason')->nullable();
            $table->boolean('is_locked')->default(false)->comment('true = engine otomatis tidak boleh memindahkan');
            $table->boolean('is_manual_placed')->default(false)->comment('true = ditempatkan manual oleh pengguna');
            $table->boolean('is_overridden')->default(false)->comment('true = pengguna menerima pelanggaran constraint');
            $table->json('violations')->nullable();
            $table->unique(['schedule_id', 'class_section_id', 'session_type']);
            $table->index(['schedule_id', 'day_of_week', 'lab_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_sessions');
    }
};
