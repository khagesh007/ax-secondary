<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class GroupCalendarActivityReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'group:calendarActivityReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $environment = \App::environment();

        $primary_agents = User::where('status', true)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Agent');
            })
            ->where('bitrix_role', 'not like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Abu-Dhabi Team%')
            ->where('bitrix_role', 'like', '%Primary%')
            ->where('bitrix_role', 'not like', '%Primary Admin%')
            ->where('region_id', '!=', 2)
            ->where('is_report_exempt', false)
            ->get();

        $primary_agents_message = "<b>\n\nPRIMARY (NA)</b>\n";
        $primary_agents_message_suspension = "<b>\n\nPRIMARY (NA)</b>\n";
        $primary_agents_data = [];

        // Sort $primary_agents collection based on the number of suspensions
        $primary_agents = $primary_agents->sortByDesc(function ($agent) {
            return $agent->suspensions->count();
        });

        foreach ($primary_agents as $primary_agents_agent) {
            if ($primary_agents_agent->suspensions->count() > 2) {
                $primary_agents_message_suspension .= '  ' . $primary_agents_agent->full_name . ": " . $primary_agents_agent->suspensions->count() . "\n";
            }

            $lastEventDate = $primary_agents_agent->events()->max('created_at');
            $daysSinceLastEvent = $lastEventDate ? now()->diffInDays($lastEventDate) : now()->diffInDays($primary_agents_agent->created_at);

            if ($daysSinceLastEvent > 7) {
                $primary_agents_data[] = [
                    'name' => $primary_agents_agent->full_name,
                    'days' => $daysSinceLastEvent,
                ];
            }
        }

// Sort $primary_agents_data array based on the number of days since the last event
        usort($primary_agents_data, function ($a, $b) {
            return $b['days'] - $a['days'];
        });

        foreach ($primary_agents_data as $agent_data) {
            $primary_agents_message .= '  ' . $agent_data['name'] . ": " . $agent_data['days'] . "\n";
        }

        // foreach ($primary_agents as $primary_agent) {
        //     $lastEventDate = $primary_agent->events()->max('created_at');

        //     if ($lastEventDate) {
        //         $daysSinceLastEvent = now()->diffInDays($lastEventDate);
        //         if ($daysSinceLastEvent > 7) {
        //             $primary_agents_message .= $primary_agent->full_name . " " . $daysSinceLastEvent . " days ago\n";
        //         }
        //     } else {
        //         $daysSinceCreation = now()->diffInDays($primary_agent->created_at);
        //         if ($daysSinceCreation > 7) {
        //             $primary_agents_message .= $primary_agent->full_name . " - U: " . $daysSinceCreation . " days ago\n";
        //         }
        //     }
        // }
        $primary_agents_message .= "==========================\n";

        $secondary = User::where('status', true)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Agent');
            })
            ->where('bitrix_role', 'like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Abu-Dhabi Team%')
            ->where('bitrix_role', 'not like', '%Primary%')
            ->where('bitrix_role', 'not like', '%Primary Admin%')
            ->where('region_id', '!=', 2)
            ->orderBy('created_at', 'desc')
            ->where('is_report_exempt', false)
            ->get();

        $secondary_message = "<b>\nSECONDARY</b>\n";
        $secondary_message_suspension = "<b>\nSECONDARY</b>\n";
        $secondary_data = [];

        // Sort $secondary collection based on the number of suspensions
        $secondary = $secondary->sortByDesc(function ($agent) {
            return $agent->suspensions->count();
        });

        foreach ($secondary as $secondary_agent) {
            if ($secondary_agent->suspensions->count() > 2) {
                $secondary_message_suspension .= '  ' . $secondary_agent->full_name . ": " . $secondary_agent->suspensions->count() . "\n";
            }

            $lastEventDate = $secondary_agent->events()->max('created_at');
            $daysSinceLastEvent = $lastEventDate ? now()->diffInDays($lastEventDate) : now()->diffInDays($secondary_agent->created_at);

            if ($daysSinceLastEvent > 7) {
                $secondary_data[] = [
                    'name' => $secondary_agent->full_name,
                    'days' => $daysSinceLastEvent,
                ];
            }
        }

        // Sort $secondary_data array based on the number of days since the last event
        usort($secondary_data, function ($a, $b) {
            return $b['days'] - $a['days'];
        });

        foreach ($secondary_data as $agent_data) {
            $secondary_message .= '  ' . $agent_data['name'] . ": " . $agent_data['days'] . "\n";
        }

        //   foreach ($secondary as $primary_agent) {
//             $lastEventDate = $primary_agent->events()->max('created_at');

//             if ($lastEventDate) {
//                 $daysSinceLastEvent = now()->diffInDays($lastEventDate);
//                 if ($daysSinceLastEvent > 7) {
//                     $secondary_message .= $primary_agent->full_name . " " . $lastEventDate . " days ago\n";
//                 }
//             } else {
//                 $daysSinceCreation = now()->diffInDays($primary_agent->created_at);
//                 if ($daysSinceCreation > 7) {
//                     $secondary_message .= $primary_agent->full_name . " - U: " . $primary_agent->created_at . " days ago\n";
//                 }
//             }
//         }
        $secondary_message .= "==========================\n";

        $abu_dhabi_agents = User::where('status', true)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Agent');
            })
            ->where('region_id', 2)
            ->where('is_report_exempt', false)
            ->get();

        $abu_dhabi_agent_message = "<b>\n\nABU DHABI</b>\n";
        $abu_dhabi_agent_message_suspension = "<b>\n\nABU DHABI</b>\n";
        $abu_dhabi_agents_data = [];

// Sort $abu_dhabi_agents collection based on the number of suspensions
        $abu_dhabi_agents = $abu_dhabi_agents->sortByDesc(function ($agent) {
            return $agent->suspensions->count();
        });

        foreach ($abu_dhabi_agents as $abu_dhabi_agents_agent) {
            if ($abu_dhabi_agents_agent->suspensions->count() > 2) {
                $abu_dhabi_agent_message_suspension .= '  ' . $abu_dhabi_agents_agent->full_name . ": " . $abu_dhabi_agents_agent->suspensions->count() . "\n";
            }

            $lastEventDate = $abu_dhabi_agents_agent->events()->max('created_at');
            $daysSinceLastEvent = $lastEventDate ? now()->diffInDays($lastEventDate) : now()->diffInDays($abu_dhabi_agents_agent->created_at);

            if ($daysSinceLastEvent > 7) {
                $abu_dhabi_agents_data[] = [
                    'name' => $abu_dhabi_agents_agent->full_name,
                    'days' => $daysSinceLastEvent,
                ];
            }
        }

// Sort $abu_dhabi_agents_data array based on the number of days since the last event
        usort($abu_dhabi_agents_data, function ($a, $b) {
            return $b['days'] - $a['days'];
        });

        foreach ($abu_dhabi_agents_data as $agent_data) {
            $abu_dhabi_agent_message .= '  ' . $agent_data['name'] . ": " . $agent_data['days'] . "\n";
        }

        // $abu_dhabi_agent_message = "\n\n<b>ABU DHABI</b>\n";
        // foreach ($abu_dhabi_agent as $primary_agent) {
        //     $lastEventDate = $primary_agent->events()->max('created_at');

        //     if ($lastEventDate) {
        //         $daysSinceLastEvent = now()->diffInDays($lastEventDate);
        //         if ($daysSinceLastEvent > 7) {
        //             $abu_dhabi_agent_message .= $primary_agent->full_name . " " . $daysSinceLastEvent . " days ago\n";
        //         }
        //     } else {
        //         $daysSinceCreation = now()->diffInDays($primary_agent->created_at);
        //         if ($daysSinceCreation > 7) {
        //             $abu_dhabi_agent_message .= $primary_agent->full_name . " - U: " . $daysSinceCreation . " days ago\n";
        //         }
        //     }
        // }
        $abu_dhabi_agent_message .= "==========================\n";

        $chinese_agents = User::where('status', true)
        ->whereHas('roles', function ($query) {
            $query->where('name', 'Agent');
        })
        ->where('bitrix_role', 'like', '%CHINESE%')
        ->where('region_id', '!=', 2)
            ->where('is_report_exempt', false)
        ->get();

    $chinese_agent_message = "<b>\n\nCHINESE</b>\n";
    $chinese_agent_message_suspension = "<b>\n\nCHINESE</b>\n";
    $chinese_agents_data = [];

    // Sort $chinese_agents collection based on the number of suspensions
    $chinese_agents = $chinese_agents->sortByDesc(function ($agent) {
        return $agent->suspensions->count();
    });

    foreach ($chinese_agents as $chinese_agent) {
        if ($chinese_agent->suspensions->count() > 2) {
            $chinese_agent_message_suspension .= '  ' . $chinese_agent->full_name . ": " . $chinese_agent->suspensions->count() . "\n";
        }

        $lastEventDate = $chinese_agent->events()->max('created_at');
        $daysSinceLastEvent = $lastEventDate ? now()->diffInDays($lastEventDate) : now()->diffInDays($chinese_agent->created_at);

        if ($daysSinceLastEvent > 7) {
            $chinese_agents_data[] = [
                'name' => $chinese_agent->full_name,
                'days' => $daysSinceLastEvent,
            ];
        }
    }

    usort($chinese_agents_data, function ($a, $b) {
        return $b['days'] - $a['days'];
    });

    foreach ($chinese_agents_data as $agent_data) {
        $chinese_agent_message .= '  ' . $agent_data['name'] . ": " . $agent_data['days'] . "\n";
    }
        $chinese_agent_message .= "==========================\n";

        // foreach ($chinese_agent as $primary_agent) {
        //     $lastEventDate = $primary_agent->events()->max('created_at');

        //     if ($lastEventDate) {
        //         $daysSinceLastEvent = now()->diffInDays($lastEventDate);
        //         if ($daysSinceLastEvent > 7) {
        //             $chinese_agent_message .= $primary_agent->full_name . " " . $daysSinceLastEvent . " days ago\n";
        //         }
        //     } else {
        //         $daysSinceCreation = now()->diffInDays($primary_agent->created_at);
        //         if ($daysSinceCreation > 7) {
        //             $chinese_agent_message .= $primary_agent->full_name . " - U: " . $daysSinceCreation . " days ago\n";
        //         }
        //     }
        // }
        // $chinese_agent_message .= "==========================\n";

        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "🗓 <b> DAYS SINCE LAST EVENT (Viewing, Follow-up, Meeting) CREATED IN THE SYSTEM</b> 🗓\n\n";
        $message .= $secondary_message;
        // $message .= $primary_agents_message;
        // $message .= $abu_dhabi_agent_message;
        // $message .= $chinese_agent_message;

        $telegram = Telegram::bot('notification_bot');
        $telegramMessage = $this->sendTelegramMessage($telegram, $message);

        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "🚨<b>NUMBER OF TIMES ACCOUNT SUSPENDED DUE TO NON-ACTIONED LEADS.</b> 🚨\n\n";
        $message .= $secondary_message_suspension;
        // $message .= $primary_agents_message_suspension;
        $message .= $abu_dhabi_agent_message_suspension;
        // $message .= $chinese_agent_message_suspension;

        $telegram = Telegram::bot('notification_bot');
        $telegramMessage = $this->sendTelegramMessage($telegram, $message);

        return Command::SUCCESS;
    }

    public function sendTelegramMessage($telegram, $message)
    {
        try {
            if (env('APP_ENV') == 'production') {
                \Log::info('Telegram message sending for Group Calendar Activity Report.');
                $chatids = [
                    '1417490671',
                    // '1669212762',
                ];

                foreach ($chatids as $chatid) {
                    $telegram->sendMessage([
                        'chat_id' => $chatid,
                        'text' => $message,
                        'parse_mode' => 'html',
                    ]);
                }


            } else {
                $chatids = [

                    '5970238984',
                    // '1417490671',
                    // '795165330',
                ];

                foreach ($chatids as $chatid) {
                    $telegram->sendMessage([
                        'chat_id' => $chatid,
                        'text' => $message,
                        'parse_mode' => 'html',
                    ]);
                }
            }

            return true;
        } catch (\Throwable $th) {
            \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: 1669212762 message is: " . $message);
            return false;
        }
    }
}
