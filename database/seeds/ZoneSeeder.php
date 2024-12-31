<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('zones')->insert([
            ['name' => 'Zone-1'],
            ['name' => 'Zone-2'],
            ['name' => 'Zone-3'],
        ]);
    }
}
