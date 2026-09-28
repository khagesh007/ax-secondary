<?php

namespace Database\Seeders;

use App\Models\DealDeductibleType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DealSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $deductible_types = [
            [
                'name' => 'Cash to Cash',
                'type' => 'fixed',
                'type_value' => '3150',
                'status' => 1,
                'order' => 1,
                'icon' => 'fa fa-eye',
            ],
          [
                    'name' => 'Cash to Mortgage',
                    'type' => 'fixed',
                    'type_value' => '3150',
                    'status' => 1,
                    'order' => 2,
                    'icon' => 'fa fa-handshake-o',
                ],
            [
                'name' => 'Mortgage to Cash',
                'type' => 'fixed',
                'type_value' => '5250',
                'status' => 1,
                'order' => 2,
                'icon' => 'fa fa-handshake-o',
            ],
            [
                'name' => 'Mortgage to Mortgage',
                'type' => 'fixed',
                'type_value' => '5250',
                'status' => 1,
                'order' => 2,
                'icon' => 'fa fa-handshake-o',
            ],
            [
                'name' => 'Gifting',
                'type' => 'fixed',
                'type_value' => '3150',
                'status' => 1,
                'order' => 2,
                'icon' => 'fa fa-handshake-o',
            ],[
                'name' => 'Company involved',
                'type' => 'fixed',
                'type_value' => '5250',
                'status' => 1,
                'order' => 2,
                'icon' => 'fa fa-handshake-o',
            ]

        ];
        foreach ($deductible_types as $deductible_type) {
            DealDeductibleType::updateOrCreate(['name' => $deductible_type['name']], $deductible_type);
        }
    }
}
