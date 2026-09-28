<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!EventType::where('name', 'Viewing')->first()) {
            EventType::create([
                'name' => 'Viewing',
                'status' => 1,
                'order' => 1,
                'icon' => 'fa fa-eye',
            ]);
        }
        if (!EventType::where('name', 'Meeting')->first()) {
            EventType::create([
                'name' => 'Meeting',
                'status' => 1,
                'order' => 2,
                'icon' => 'fa fa-handshake-o',
            ]);
        }
        if (!EventType::where('name', 'Task')->first()) {
            EventType::create([
                'name' => 'Task',
                'status' => 1,
                'order' => 3,
                'icon' => 'fa fa-tasks',
            ]);
        } if (!EventType::where('name', 'Schedule a call')->first()) {
            EventType::create([
                'name' => 'Schedule a call',
                'status' => 1,
                'order' => 4,
                'icon' => 'fa fa-phone',
            ]);
        }
        if (!EventType::where('name', 'Photo shoot Booking')->first()) {
        EventType::create([
            'name' => 'Photo shoot Booking',
            'status' => 1,
            'order' => 5,
            'icon' => 'bx bx-camera',
        ]);
        }
        if (!EventType::where('name', 'Video shoot Booking')->first()) {
        EventType::create([
            'name' => 'Video shoot Booking',
            'status' => 1,
            'order' => 6,
            'icon' => 'bx bx-video-recording',
        ]);
        }
        if (!EventType::where('name', 'Holiday/Leave')->first()) {
        EventType::create([
            'name' => 'Holiday/Leave',
            'status' => 1,
            'order' => 7,
            'icon' => 'fa fa-plane',
        ]);
        }  if (!EventType::where('name', 'Other')->first()) {
        EventType::create([
            'name' => 'Other',
            'status' => 1,
            'order' => 8,
            'icon' => 'fa fa-question',
        ]);
        }
    }
}
