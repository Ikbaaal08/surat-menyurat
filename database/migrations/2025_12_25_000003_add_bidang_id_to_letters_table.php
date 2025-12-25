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
        Schema::table('letters', function (Blueprint $table) {
            if (!Schema::hasColumn('letters', 'bidang_id')) {
                $table->foreignId('bidang_id')->nullable()->constrained('bidangs')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            if (Schema::hasColumn('letters', 'bidang_id')) {
                $table->dropForeign(['bidang_id']);
                $table->dropColumn('bidang_id');
            }
        });
    }
};