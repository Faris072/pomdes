<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // step 1
            ['name'=>'Pengajuan Transaksi'],// 1
            ['name'=>'Penolakan Pengajuan'],// 2
            ['name'=>'Perbaikan Pengajuan'],// 3

            //step 2
            ['name'=>'Tagihan Pomdes'],// 4
            ['name'=>'Tagihan Disimpan'],// 5

            // step 3
            ['name'=>'Menunggu Pembayaran'],// 6

            // step 4
            ['name'=>'Menunggu Pengiriman'],// 7
            ['name'=>'Dalam Pengiriman'],// 8
            ['name'=>'Kendala Pengiriman'],// 9

            //step 5
            ['name'=>'BBM Telah Sampai'],// 10 ->supplier
            ['name'=>'Laporan Ketidaksesuaian'],// 11 ->pomdes
            ['name'=>'BBM Sampai'],// 12 ->pomdes
        ];

        foreach($data as $d){
            Status::create($d);
        }
    }
}
