<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Type;
class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Type::create([
            'name' => 'Tenant',
            'status' => 1,
            'is_contact_type' => 1,
            'is_lead_type' => 1,
            'order' => 1,
        ]);
        Type::create([
            'name' => 'Buyer',
            'status' => 1,
            'is_contact_type' => 1,
            'is_lead_type' => 1,
            'order' => 1,
        ]);
        Type::create([
            'name' => 'Landlord',
            'status' => 1,
            'is_contact_type' => 1,
            'is_lead_type' => 1,
            'order' => 1,
        ]);
        Type::create([
            'name' => 'Seller',
            'status' => 1,
            'is_contact_type' => 1,
            'is_lead_type' => 1,
            'order' => 1,
        ]);
        Type::create([
            'name' => 'Landlord+Seller',
            'status' => 1,
            'is_contact_type' => 1,
            'is_lead_type' => 1,
            'order' => 1,
        ]);
        Type::create([
            'name' => 'Representative of Tenant',
            'status' => 1,
            'is_contact_type' => 1,
            'is_lead_type' => 1,
            'order' => 1,
        ]);
        Type::create([
            'name' => 'Agent',
            'status' => 1,
            'is_contact_type' => 1,
            'is_lead_type' => 1,
            'order' => 1,
        ]);
        Type::create([
            'name' => 'Investor',
            'status' => 1,
            'is_contact_type' => 1,
            'is_lead_type' => 1,
            'order' => 1,
        ]);
        Type::create([
            'name' => 'Representative of Landlord',
            'status' => 1,
            'is_contact_type' => 1,
            'is_lead_type' => 1,
            'order' => 1,
        ]);
        Type::create([
            'name' => 'Other',
            'status' => 1,
            'is_contact_type' => 1,
            'is_lead_type' => 1,
            'order' => 1,
        ]);
        Type::create([
            'name' => 'Unspecified',
            'status' => 1,
            'is_contact_type' => 1,
            'is_lead_type' => 1,
            'order' => 1,
        ]);
      
    }
}
