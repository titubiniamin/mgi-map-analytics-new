<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('item_types')->insert([
            ['name'=>'Item Type-1'],
            ['name'=>'Item Type-2'],
            ['name'=>'Item Type-3'],
        ]);
    }
}
