<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('period_id')->constrained('periods')->cascadeOnDelete();
            $table->foreignUuid('pic_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name')->comment('nama versi, mis. "Draft 1"');
            $table->boolean('is_active')->default(false)->comment('hanya satu per periode; dijaga di layer aplikasi');
            $table->text('notes')->nullable();
            $table->json('snapshot_json')->nullable()->comment('snapshot state jadwal sesuai automateDB');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
