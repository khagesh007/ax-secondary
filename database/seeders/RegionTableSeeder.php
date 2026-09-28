<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regions = [
            [
                "id" => 1,
                "name" => "Dubai",
                "status" => true,
                "created_by_id" => 1,
            ]
        ];

        foreach ($regions as $region) {
            Region::updateOrCreate([
                "id" => $region["id"]
            ], $region);
        }
    }
}
