<?php

namespace Database\Seeders;

use App\Models\QuizLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuizLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $level = new QuizLevel();
        $level->name = 'Trial Level';
        $level->description = 'This is a trial level';
        $level->status = 1;
        $level->save();


        QuizLevel::updateOrCreate([
            'id' => 1,
            'name' => 'Trial Level',
        ],[
            'description' => 'This is level 1',
            'status' => 1,
        ]);
    }
}
