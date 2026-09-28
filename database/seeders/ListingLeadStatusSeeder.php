<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ListingLeadStatus;
class ListingLeadStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ListingLeadStatus::create([
            'name' => 'New',
            'order' => 20,
            'status' => 1,
            'icon' => 'bx bxs-user-plus',
            'badge' => 'badge color-bg-13',
        ]);

        ListingLeadStatus::create([
            'name' => 'Assigned',
            'order' => 40,
            'status' => 1,
            'icon' => 'bx bxs-user-plus',
            'badge' => 'badge color-bg-12',
        ]);
        ListingLeadStatus::create([
            'name' => 'Contacted',
            'order' => 60,
            'status' => 1,
            'icon' => 'bx bxs-user-plus',
            'badge' => 'badge color-bg-11',
        ]);
        ListingLeadStatus::create([
            'name' => 'Qualified',
            'order' => 20,
            'status' => 1,
            'icon' => 'bx bxs-user-plus',
            'badge' => 'badge color-bg-14',
        ]);
    
        ListingLeadStatus::create([
            'name' => 'Successful',
            'order' => 100,
            'status' => 1,
            'icon' => 'bx bxs-user-plus',
            'badge' => 'badge color-bg-16',
        ]);
        ListingLeadStatus::create([
            'name' => 'Unsuccessful',
            'order' => 120,
            'status' => 1,
            'icon' => 'bx bxs-user-plus',
            'badge' => 'badge color-bg-17',
        ]);
    }
}
