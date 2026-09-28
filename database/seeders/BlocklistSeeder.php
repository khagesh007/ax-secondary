<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlacklistedNumber;

class BlocklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $numbers = [
            '0557607350',
            '0585154932',
            '0563946059',
            '0555559210',
            '0565494673',
            '0563946059',
            '050527226',
            '0506539371',
            '0586502204',
            '0506258900',
            '0535492864',
            '0556555549',
            '0508476925',
            '0507155555',
            '0559207084',
            '0563946059',
            '0506177477',
        ];


        foreach($numbers as $number){
            BlacklistedNumber::updateOrCreate([
                'number' => validatePhoneNumber($number),
                'blocked_by' => 1,
                'status' => true,
            ]);
        }
    }
}
