<?php

use Database\Seeders\ItemTypeSeeder;
use Database\Seeders\BloodGroupSeeder;
use Database\Seeders\BrandSeeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\DesignationSeeder;
use Database\Seeders\DistrictSeeder;
use Database\Seeders\OfficeSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ZoneSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            RolePermissionSeeder::class,
            DepartmentSeeder::class,
            DesignationSeeder::class,
            ZoneSeeder::class,
            DistrictSeeder::class,
            OfficeSeeder::class,
            BloodGroupSeeder::class,
            BrandSeeder::class,
            ItemTypeSeeder::class
        ]);
    }
}
