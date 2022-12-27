<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DiscrepancyType;

class DiscrepancyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => 'Kelebihan'],
            ['name' => 'Kurang'],
        ];

        foreach($data as $d){
            DiscrepancyType::create($d);
        }
    }
}
