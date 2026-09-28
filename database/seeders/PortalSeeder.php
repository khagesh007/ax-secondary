<?php

namespace Database\Seeders;

use App\Models\Portal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PortalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $portals = array(
            array('id' => '1','name' => 'Property Finder','short_name' => 'PF', 'slug' => 'property-finder','status' => '1','image'=>'https://s3.eu-west-1.amazonaws.com/secondary.crm/images/thumb166970050120221129054141.png','created_at' => '2022-09-13 23:54:06','updated_at' => '2022-09-13 23:54:06','deleted_at' => NULL),
            array('id' => '2','name' => 'Bayut','short_name' => 'BYT', 'slug' => 'bayut','status' => '1','image' =>'https://s3.eu-west-1.amazonaws.com/secondary.crm/images/thumb166970051320221129054153.png','created_at' => '2022-09-13 23:54:06','updated_at' => '2022-09-13 23:54:06','deleted_at' => NULL),
            array('id' => '3','name' => 'Dubizzle','short_name' => 'DBZL', 'slug' => 'Dubizzle','image' => 'https://s3.eu-west-1.amazonaws.com/secondary.crm/images/thumb166970052320221129054203.png','status' => '1','created_at' => '2022-09-13 23:54:06','updated_at' => '2022-09-13 23:54:06','deleted_at' => NULL),
            array('id' => '4','name' => 'Private Site','short_name' => null, 'slug' => null,'status' => '1','created_at' => '2022-09-13 23:54:06','updated_at' => '2022-09-13 23:54:06','deleted_at' => NULL),
            array('id' => '5','name' => 'Generic Portal','short_name' => null, 'slug' => null,'status' => '1','created_at' => '2022-09-13 23:54:06','updated_at' => '2022-09-13 23:54:06','deleted_at' => NULL),
            array('id' => '6','name' => 'Right Move','short_name' => null, 'slug' => null,'status' => '1','created_at' => '2022-09-13 23:54:06','updated_at' => '2022-09-13 23:54:06','deleted_at' => NULL),
        );

        foreach ($portals as $portal) {
            Portal::updateOrCreate([
                "id" => $portal["id"]
            ], $portal);
        }
    }
}
