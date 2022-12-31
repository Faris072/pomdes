<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['pusat_id' => null, 'role_id' => 1, 'username' => 'admin', 'password' => bcrypt(123456)],
            // ['pusat_id' => null, 'role_id' => 2, 'username' => 'pusat', 'password' => bcrypt(123456)],
            // ['pusat_id' => null, 'role_id' => 3, 'username' => 'pomdes', 'password' => bcrypt(123456)],
            // ['pusat_id' => null, 'role_id' => 4, 'username' => 'supplier', 'password' => bcrypt(123456)],
        ];

        foreach($data as $d){
            User::create($d);
        }
    }
}
