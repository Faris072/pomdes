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
            ['name'=>'Pengajuan Transaksi'],
            ['name'=>'Penolakan Pengajuan'],
            ['name'=>'Perbaikan Pengajuan'],

            ['name'=>'Tagihan Pomdes'],
            ['name'=>'Tolak Pembayaran Pomdes'],
            ['name'=>'Perbaikan Pembayaran Pomdes'],

            ['name'=>'Menunggu Penerbitan Tagihan'],
        ];

        foreach($data as $d){
            Status::create($d);
        }
    }
}
