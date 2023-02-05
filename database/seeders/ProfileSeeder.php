<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Profile;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            ['user_id' => 1, 'city_id' => 1, 'name' => 'Administrator', 'phone' => '085706389042', 'email' => 'faris@gmail.com'],
        ];

        foreach($datas as $d){
            Profile::create($d);
        }
    }
}
