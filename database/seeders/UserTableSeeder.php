<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Top in hierarchy
        $superAdminRole = Role::updateOrCreate([
            "id" => 1
        ], [
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);

        $permissions = Permission::all();
        $superAdminRole->syncPermissions($permissions);

        $superAdmin = User::updateOrCreate([
            "id" => 1,
        ], [
            'first_name' => 'Hishmat',
            'last_name' => 'Rano',
            'name' => 'Hishmat Rano',
            'email' => 'hishmat@axcapital.ae',
            'password' => Hash::make('hishmat@3213$'),
            'email_verified_at' => '2022-01-02 17:04:58',
            'avatar' => 'avatar-1.jpg',
            'created_at' => now(),
        ]);
        $superAdmin->assignRole($superAdminRole);

        $superAdmin2 = User::updateOrCreate([
            "id" => 2,
        ], [
            'first_name' => 'Saim',
            'last_name' => 'Aziz',
            'name' => 'Saim Aziz',
            'email' => 's.mirza@axcapital.ae',
            'password' => Hash::make('saim@3213$'),
            'email_verified_at' => '2022-01-02 17:04:58',
            'avatar' => 'avatar-1.jpg',
            'created_at' => now(),
        ]);
        $superAdmin2->assignRole($superAdminRole);


        $superAdmin3 = User::updateOrCreate(
            [
                "id" => 3,
            ],
            [
                'first_name' => 'Otabek',
                'last_name' => 'Navruzov',
                'name' => 'Otabek Navruzov',
                'email' => 'cto@axcapital.ae',
                'password' => Hash::make('axcapital@321'),
                'email_verified_at' => '2022-01-02 17:04:58',
                'avatar' => 'avatar-1.jpg',
                'created_at' => now(),
            ]
        );


        $superAdmin3->assignRole($superAdminRole);
        //Second top in hierarchy
        $headOfSecondary = Role::updateOrCreate([
            "id" => 2
        ], [
            'name' => 'Head of Secondary',
            'guard_name' => 'web',
        ]);

        $permissions = Permission::all();
        $headOfSecondary->syncPermissions($permissions);

        $headOfSecondaryUser = User::updateOrCreate(
            [
                "id" => 3,
            ], [
            'first_name' => 'Head',
            'last_name' => 'Navruzov',
            'name' => 'Head Navruzov',
            'email' => 'head@axcapital.ae',
            'password' => Hash::make('abc@321'),
            'email_verified_at' => '2022-01-02 17:04:58',
            'avatar' => 'avatar-1.jpg',
            'created_at' => now(),
        ]);


        $headOfSecondaryUser->assignRole($headOfSecondary);
        //Comes after Head of Secondary hierarchy
        $salesDirector = Role::updateOrCreate([
            'name' => 'Sales Director',
        ], [
            'guard_name' => 'web',
        ]);

        $permissions = (new PermissionTableSeederSeeder())->getSalesDirectorPermissions();
        $salesDirector->syncPermissions($permissions);


        //Comes after Sales Director in hierarchy
        $lineManager = Role::updateOrCreate([
            'name' => 'Line Manager',
        ], [
            'guard_name' => 'web',
        ]);

        $permissions = (new PermissionTableSeederSeeder())->getLineManagerPermissions();
        $lineManager->syncPermissions($permissions);

        //Comes after the Line Manager in hierarchy
        $agent = Role::updateOrCreate([
            'name' => 'Agent',
        ], [
            'guard_name' => 'web',
        ]);

        $permissions = (new PermissionTableSeederSeeder())->getAgentPermissions();
        $agent->syncPermissions($permissions);

        //Not required
        $teamLeader = Role::updateOrCreate([
            'name' => 'Team Leader',
        ], [
            'guard_name' => 'web',
        ]);

        $admin = Role::updateOrCreate([
            'name' => 'Admin',
        ], [
            'guard_name' => 'web',
        ]);

        $abuDhabiAdmin = Role::updateOrCreate([
            'name' => 'Admin - Abu Dhabi',
        ], [
            'guard_name' => 'web',
        ]);

        $primarySalesDirector = Role::create([
            'name' => 'Primary Sales Director'
        ], [
            'guard_name' => 'web',
        ]);

        $primarySalesManager = Role::create([
            'name' => 'Primary Sales Manager'
        ], [
            'guard_name' => 'web',
        ]);

        //TELESALES
        $telesales = Role::updateOrCreate([
            'name' => 'Telesales',
        ], [
            
            'guard_name' => 'web',
        ]);

        //Sales Progression
        $salesProgression = Role::updateOrCreate([
            'name' => 'Sales Progression',
        ], [
            'guard_name' => 'web',
        ]);

        $permissions = (new PermissionTableSeederSeeder())->getTelesalePermissions();
        $telesales->syncPermissions($permissions);


        $telesales_head = Role::updateOrCreate([
            "id" => 9
        ], [
            'name' => 'Telesales Head',
            'guard_name' => 'web',
        ]);
        $permissions = (new PermissionTableSeederSeeder())->getTelesaleHeadPermissions();
        $telesales_head->syncPermissions($permissions);
    }
}
