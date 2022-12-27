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
            ['name'=>'Tagihan Transaksi'],
        ];

        foreach($data as $d){
            Status::create($d);
        }
    }
}
