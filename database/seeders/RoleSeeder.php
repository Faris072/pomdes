<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => 'Administrator'],
            ['name' => 'Pusat'],
            ['name' => 'Pomdes'],
            ['name' => 'Supplier'],
        ];

        foreach($data as $d){
            Role::create($d);
        }
    }
}
