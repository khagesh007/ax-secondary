<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

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
            PermissionTableSeederSeeder::class,
            UserTableSeeder::class,
            RegionTableSeeder::class,
            CommunityTableSeeder::class,
            SubCommunityTableSeeder::class,
            CommunityPropertyTableSeeder::class,
            PortalSeeder::class,
            ParametersSeeder::class,
            PropertyCategorySeeder::class,
            CountryTableSeeder::class,
            MediumSeeder::Class,
            SourceSeeder::Class,
            TypeSeeder::Class,
        ]);
//         \App\Models\User::factory(50)->create();


        $users = User::where("id", ">", 1)->get();
        foreach ($users as $user){
            $role = Role::inRandomOrder()->first();
            $user->assignRole($role);
        }
    }
}
