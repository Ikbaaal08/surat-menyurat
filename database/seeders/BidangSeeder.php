<?php

namespace Database\Seeders;

use App\Models\Bidang;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BidangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $bidangs = [
            ['nama_bidang' => 'Bidang Bina Marga'],
            ['nama_bidang' => 'Bidang Cipta Karya'],
            ['nama_bidang' => 'Bidang Sumber Daya Air'],
            ['nama_bidang' => 'Bidang Penataan Bangunan'],
            ['nama_bidang' => 'Sekretariat'],
        ];

        foreach ($bidangs as $bidang) {
            Bidang::create($bidang);
        }
    }
    // public function run(): void
    // {
    //     $bidangs = [
    //         'Bina Marga',
    //         'Sumber Daya Air',
    //         'Cipta Karya',
    //         'Perumahan',
    //         'Irigasi',
    //         'Pengembangan Infrastruktur',
    //         'Pengelolaan Aset',
    //     ];

    //     foreach ($bidangs as $nama) {
    //         Bidang::firstOrCreate([
    //             'nama_bidang' => $nama,
    //         ], [
    //             'slug' => Str::slug($nama),
    //         ]);
    //     }
    // }
}
