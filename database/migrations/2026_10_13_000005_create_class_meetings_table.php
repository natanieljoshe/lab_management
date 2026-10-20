<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_meetings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('class_section_id')->constrained('class_sections')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week')->comment('0=Minggu … 6=Sabtu');
            $table->unsignedSmallInteger('start_min')->comment('menit sejak 00:00');
            $table->unsignedSmallInteger('end_min');
            $table->foreignUuid('lab_id')->nullable()->constrained('labs')->nullOnDelete()->comment('null jika ruang kuliah bukan lab');
            $table->string('room_label')->nullable()->comment('teks ruang asli dari impor');
            $table->text('raw_text')->nullable()->comment('baris asli dari file impor');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_meetings');
    }
};
