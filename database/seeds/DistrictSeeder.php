<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $districts = [
            'Bandarban',
            'Barguna',
            'Barishal',
            'Bhola',
            'Bogura',
            'Brahmanbaria',
            'Chandpur',
            'Chattogram',
            'Chuadanga',
            'Cox\'s Bazar',
            'Cumilla',
            'Dhaka',
            'Dinajpur',
            'Feni',
            'Gaibandha',
            'Gazipur',
            'Jamalpur',
            'Jashore',
            'Jhalokati',
            'Jhenaidah',
            'Joypurhat',
            'Khagrachhari',
            'Khulna',
            'Kishoreganj',
            'Kurigram',
            'Kushtia',
            'Madaripur',
            'Naogaon',
            'Narail',
            'Narayanganj',
            'Narsingdi',
            'Natore',
            'Netrokona',
            'Nilphamari',
            'Noakhali',
            'Pabna',
            'Panchagarh',
            'Pirojpur',
            'Sherpur',
            'Sirajganj',
            'Sunamganj',
            'Sylhet',
            'Tangail',
            'Thakurgaon',
            'Bagerhat',
            'Chapainawabganj',
            'Faridpur',
            'Gopalganj',
            'Habiganj',
            'Lakshmipur',
            'Lalmonirhat',
            'Magura',
            'Manikganj',
            'Maulvibazar',
            'Meherpur',
            'Munshiganj',
            'Mymensingh',
            'Patuakhali',
            'Rajbari',
            'Rajshahi',
            'Rangamati',
            'Rangpur',
            'Satkhira',
            'Shariatpur',
        ];

        foreach ($districts as $district) {
            DB::table('districts')->insert(['name' => $district]);
        }
    }
}
