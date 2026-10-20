<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ticket_number')->unique()->comment('Format FB-YYYYMMDD-NNN, generate di creating event');
            $table->enum('type', ['KELUHAN', 'SARAN']);
            $table->string('subject');
            $table->text('description');
            $table->foreignUuid('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('lab_id')->nullable()->constrained('labs')->nullOnDelete();
            $table->unsignedTinyInteger('status')->default(1)
                  ->comment('1=Open, 2=In Progress, 3=Resolved, 4=Closed, 5=Rejected');
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('period_id')->constrained('periods')->cascadeOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
