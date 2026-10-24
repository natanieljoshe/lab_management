<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('repairs_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('itemable'); // itemable_type, itemable_id

            $table->text('issue_description');
            $table->unsignedTinyInteger('status')->default('0')->comment('0: Pending | 1: In Progress | 2: Completed');

            $table->boolean('is_successful')->nullable()->comment('null: belum selesai | true: perbaikan berhasil | false: perbaikan gagal');
            $table->text('repair_notes')->nullable();

            $table->uuid('repair_id');
            $table->foreign('repair_id')->references('id')->on('repairs')->onDelete('cascade');

            $table->dateTime('started_at')->nullable();
            $table->dateTime('completed_at')->nullable();

            $table->unique(['itemable_type', 'itemable_id', 'repair_id']); // 1 item - 1 repair log
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repairs_items');
    }
};
