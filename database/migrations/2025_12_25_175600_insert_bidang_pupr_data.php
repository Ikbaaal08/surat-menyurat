<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        DB::table('bidangs')->insert([
            ['nama_bidang' => 'Bina Marga'],
            ['nama_bidang' => 'Cipta Karya'],
            ['nama_bidang' => 'Sumber Daya Air'],
            ['nama_bidang' => 'Tata Ruang'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::table('bidangs')->whereIn('nama_bidang', [
            'Bina Marga',
            'Cipta Karya',
            'Sumber Daya Air',
            'Tata Ruang',
        ])->delete();
    }
};
