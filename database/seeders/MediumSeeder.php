<?php

namespace Database\Seeders;

use App\Models\Medium;
use Illuminate\Database\Seeder;

class MediumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Medium::where('name', 'Email')->first()) {
            Medium::create([
                'name' => 'Email',
                'status' => 1,
                'is_contact_medium' => 1,
                'is_lead_medium' => 1,
                'order' => 1,
            ]);
        }

        if (!Medium::where('name', 'Call')->first()) {
            Medium::create([
                'name' => 'Call',
                'status' => 1,
                'is_contact_medium' => 1,
                'is_lead_medium' => 1,
                'order' => 2,
            ]);
        }
        if (!Medium::where('name', 'SMS')->first()) {
            Medium::create([
                'name' => 'SMS',
                'status' => 1,
                'is_contact_medium' => 1,
                'is_lead_medium' => 1,
                'order' => 3,
            ]);
        }
        if (!Medium::where('name', 'WhatsApp')->first()) {
            Medium::create([
                'name' => 'WhatsApp',
                'status' => 1,
                'is_contact_medium' => 1,
                'is_lead_medium' => 1,
                'order' => 4,
            ]);
        }
        if (!Medium::where('name', 'Telegram')->first()) {
            Medium::create([
                'name' => 'Telegram',
                'status' => 1,
                'is_contact_medium' => 1,
                'is_lead_medium' => 1,
                'order' => 5,
            ]);
        }
        if (!Medium::where('name', 'Api')->first()) {
            Medium::create([
                'name' => 'Api',
                'status' => 1,
                'is_contact_medium' => 1,
                'is_lead_medium' => 1,
                'order' => 5,
            ]);
        }
    }
}
