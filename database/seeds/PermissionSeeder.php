<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('permissions')->insert([
            ['id' => 2,'name' => 'manage_role','guard_name' => 'web','group_name' => 'role',
            ],
            [
                'id' => 3, 'name' => 'manage_permission',
                'guard_name' => 'web','group_name' => 'permission',
            ],
            [
                'id' => 4,
                'name' => 'manage_user',
                'guard_name' => 'web','group_name' => 'user',
            ],
            [
                'id' => 5,
                'name' => 'manage_sales',
                'guard_name' => 'web','group_name' => 'sales',
            ],
            [
                'id' => 6,
                'name' => 'manage_projects',
                'guard_name' => 'web','group_name' => 'projects',
            ],
            [
                'id' => 7,
                'name' => 'inventory_item_create',
                'guard_name' => 'web','group_name' => 'inventory',
            ],
            [
                'id' => 8,
                'name' => 'inventory_item_update',
                'guard_name' => 'web','group_name' => 'inventory',
            ],
            [
                'id' => 9,
                'name' => 'inventory_item_delete',
                'guard_name' => 'web','group_name' => 'inventory',
            ],
            ['id' => 10,'name' => 'dealer.create', 'guard_name' => 'admin', 'group_name' => 'dealer'],
            ['id' => 11,'name' => 'dealer.view', 'guard_name' => 'admin', 'group_name' => 'dealer'],
            ['id' => 12,'name' => 'dealer.edit', 'guard_name' => 'admin', 'group_name' => 'dealer'],
            ['id' => 13,'name' => 'dealer.delete', 'guard_name' => 'admin', 'group_name' => 'dealer'],
            ['id' => 14,'name' => 'dealer.import-show', 'guard_name' => 'admin', 'group_name' => 'dealer'],
            ['id' => 15,'name' => 'dealer.import', 'guard_name' => 'admin', 'group_name' => 'dealer'],
        ]);
    }
}
