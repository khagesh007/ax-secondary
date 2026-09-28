<?php

namespace Database\Seeders;

use App\Models\EntityCompany;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EntityCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $companies = [
            [
                'name' => 'D A X REAL ESTATE ONE PERSON COMPANY L.L.C',
                'orn' => '21635'
            ],
            [
                'name' => 'AXC VACATION HOMES RENTAL )BR OF PRANO INVESTMENT L.L.C(',
                'orn' => null
            ],
            [
                'name' => 'A X Capital Real Estate',
                'orn' => '34726'
            ],
            [
                'name' => 'Ax Capital Real Estate - Abu Dhabi',
                'orn' => null
            ],
            [
                'name' => 'Ax Capital Real Estate - Off Plan',
                'orn' => null
            ],
            [
                'name' => 'AX Capital - Commercial',
                'orn' => '21635'
            ],
            [
                'name' => 'REVEAL REAL ESTATE BROKERAGE L.L.C',
                'orn' => '36672'
            ],
        ];

        foreach ($companies as $company) {
            EntityCompany::updateOrCreate([
                'name' => $company['name'],
            ], $company);
        }
    }
}
