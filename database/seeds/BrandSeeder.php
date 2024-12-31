<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('brands')->insert([
           ['name'=>'Fresh Super Cement'] ,
           ['name'=>'Dhalai Special Cement'] ,
           ['name'=>'Meghnacem Delux Cement'] ,
        ]);
    }
}
