<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_responses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('feedback_id')->constrained('feedbacks')->cascadeOnDelete();
            $table->foreignUuid('responder_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->unsignedTinyInteger('status_change')->nullable()
                  ->comment('Status laporan setelah aksi ini; null = tidak berubah (1-5)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_responses');
    }
};
