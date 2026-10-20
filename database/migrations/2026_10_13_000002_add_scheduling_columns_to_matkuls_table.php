<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matkuls', function (Blueprint $table) {
            $table->unsignedTinyInteger('curriculum_semester')->nullable()->comment('semester di kurikulum, 1-8');
            $table->boolean('is_required')->default(true)->comment('false = matkul pilihan');
        });
    }

    public function down(): void
    {
        Schema::table('matkuls', function (Blueprint $table) {
            $table->dropColumn(['curriculum_semester', 'is_required']);
        });
    }
};
