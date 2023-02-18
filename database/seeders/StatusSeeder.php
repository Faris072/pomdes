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
            ['name'=>'Pengajuan Transaksi'],// 1
            ['name'=>'Penolakan Pengajuan'],// 2
            ['name'=>'Perbaikan Pengajuan'],// 3

            ['name'=>'Tagihan Pomdes'],// 4
            ['name'=>'Tagihan Disimpan'],// 5

            ['name'=>'Menunggu Pembayaran'],// 6

            ['name'=>'Menunggu Pengiriman'],// 7
            ['name'=>'Dikirim'],// 8
            ['name'=>'Kendala Pengiriman'],// 9

            ['name'=>'BBM Telah Sampai'],// 9 ->supplier
            ['name'=>'Laporan Ketidaksesuaian'],// 9 ->pomdes
            ['name'=>'Selesai'],// 9 ->pomdes
        ];

        foreach($data as $d){
            Status::create($d);
        }
    }
}
