<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Add the column only when it does not already exist to avoid duplicate column errors
        if (!Schema::hasColumn('attachments', 'original_name')) {
            Schema::table('attachments', function (Blueprint $table) {
                $table->string('original_name')->after('filename')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        if (Schema::hasColumn('attachments', 'original_name')) {
            Schema::table('attachments', function (Blueprint $table) {
                $table->dropColumn('original_name');
            });
        }
    }
};
