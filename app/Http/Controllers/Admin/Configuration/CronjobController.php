<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Models\EmailSetting;
use App\Models\NotificationEvent;
use App\Models\NotificationEventSubscriber;
use App\Models\NotificationUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class CronjobController extends Controller
{
    public function index()
    {

       // abort_if(Gate::denies("Cronjob Settings List"), 401);
        $roles = Role::get();
        $userAgents = [];
        $allAgents = User::whereHas("roles", fn($q) => $q->whereIn("name", ["Sales Director", "Line Manager", "Team Leader"]))->with("roles:id,name")->get(["id", "name", "email"]);
        //dd($allAgents);
        foreach ($allAgents as $agent) {
            if (!empty($agent->roles[0]->name)) {
                $userAgents[$agent->roles[0]->name][] = [
                    "id" => $agent->id,
                    "name" => $agent->name,
                ];
            }
        }
        return view("admin.configuration.cronjob.index", compact("roles", "userAgents"));
    }
    public function getTimingList(){
     $list =   [
         [
             'command' => '->everyMinute()',
             'title' => 'Every Minute',
             'param' => '0'
         ],
         [
             'command' => '->everyFiveMinutes()',
             'title' => 'Every Five Minutes',
             'param' => '0'
         ],
         [
                'command' => '->everyTenMinutes()',
                'title' => 'Every Ten Minutes',
                'param' => '0'
            ],
            [
                'command' => '->everyFifteenMinutes()',
                'title' => 'Every Fifteen Minutes',
                'param' => '0'
            ],
            [
                'command' => '->everyThirtyMinutes()',
                'title' => 'Every Thirty Minutes',
                'param' => '0'
            ],
            [
                'command' => '->hourly()',
                'title' => 'Hourly',
                'param' => '0'
            ],
            [
                'command' => '->hourlyAt(17)',
                'title' => 'Ex. Hourly At 17',
                'param' => '1'
            ],
            [
                'command' => '->daily()',
                'title' => 'Run the task every day at midnight',
                'param' => '0'
            ],
            [
                'command' => '->dailyAt($param1)',
                'title' => 'Daily At 13:00',
                'param' => '1'
            ],
            [
                'command' => '->twiceDaily($param1, $param2)',
                'title' => 'Twice Daily 1, 13',
                'param' => '2'
            ],
            [
                'command' => '->weekly()',
                'title' => 'Weekly',
                'param' => '0'
            ],
            [
                'command' => '->weeklyOn($param1, $param2)',
                'title' => 'Weekly On 1, 8:00',
                'param' => '2'
            ],
            [
                'command' => '->monthly()',
                'title' => 'Monthly',
                'param' => '0'
            ],
            [ // $param1 0 = sunday, $param2 = 00:00
                'command' => '->monthlyOn($param1, $param2)',
                'title' => 'Monthly On 4, 15:00',
                'param' => '4, 15:00'
            ],
            [
                'command' => '->quarterly()',
                'title' => 'Quarterly',
                'param' => '0'
            ],
            [
                'command'
         ]

     ];
        return response()->json($list);
//         array(
//
//            '->everyTenMinutes()' => 'Every Ten Minutes',
//            '->everyFifteenMinutes()' => 'Every Fifteen Minutes',
//            '->everyThirtyMinutes()' => 'Every Thirty Minutes',
//            '->hourly()' => 'Hourly',
//            '->hourlyAt(17)' => 'Hourly At 17',
//            '->daily()' => 'Run the task every day at midnight',
//            '->dailyAt(\'13:00\')' => 'Daily At 13:00',
//            '->twiceDaily(1, 13)' => 'Twice Daily 1, 13',
//            '->weekly()' => 'Weekly',
//            '->weeklyOn(1, \'8:00\')' => 'Weekly On 1, 8:00',
//            '->monthly()' => 'Monthly',
//            '->monthlyOn(4, \'15:00\')' => 'Monthly On 4, 15:00',
//            '->quarterly()' => 'Quarterly',
//            '->yearly()' => 'Yearly',
//            '->between(\'8:00\', \'16:00\')' => 'Between 8:00, 16:00',
//            '->weekdays()' => 'Weekdays',
//            '->saturdays()' => 'Saturdays',
//            '->sundays()' => 'Sundays',
//            '->mondays()' => 'Mondays',
//            '->tuesdays()' => 'Tuesdays',
//            '->wednesdays()' => 'Wednesdays',
//            '->thursdays()' => 'Thursdays',
//            '->fridays()' => 'Fridays',
//            '->weekends()' => 'Weekends',
//            '->days([1, 3, 5])' => 'Days 1, 3, 5',
//            '->mondaysAt(\'13:00\')' => 'Mondays At 13:00',
//            '->tuesdaysAt(\'13:00\')' => 'Tuesdays At 13:00',
//            '->wednesdaysAt(\'13:00\')' => 'Wednesdays At 13:00',
//            '->thursdaysAt(\'13:00\')' => 'Thursdays At 13:00',
//            '->fridaysAt(\'13:00\')' => 'Fridays At 13:00',
//            '->saturdaysAt(\'13:00\')' => 'Saturdays At 13:00',
//            '->sundaysAt(\'13:00\')' => 'Sundays At 13:00',
//            '->weekdaysAt(\'13:00\')' => 'Weekdays At 13:00',
//            '->weekendsAt(\'13:00\')' => 'Weekends At 13:00',
//            '->between(\'13:00\', \'14:00\')' => 'Between 13:00, 14:00',
//            '->weekdays()->between(\'13:00\', \'14:00\')' => 'Weekdays Between 13:00, 14:00',
//            '->weekends()->between(\'13:00\', \'14:00\')' => 'Weekends Between 13:00, 14:00',
//            '->mondays()->between(\'13:00\', \'14:00\')' => 'Mondays Between 13:00, 14:00',
//            '->tuesdays()->between(\'13:00\', \'14:00\')' => 'Tuesdays Between 13:00, 14:00',
//        );
    }
}
