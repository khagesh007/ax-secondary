<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\Source;
use App\Models\User;
use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class AgentGroupNewStatusLeadsNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:newStatusLeadNotification';

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

        $cutoff = config('configurations.cutoff_date');

        $baseQuery = Lead::where('status', true)
            ->where('is_archive', 0)
            ->whereNull('converted_to_listing_lead')
            ->where('lead_status_id', 1);
        $message = '';

        // ------------------------------------------------------//
        // ------------------------------------------------------//
        // ------------------Primary Lead code------------------//
        // ------------------------------------------------------//
        // ------------------------------------------------------//

        // $primary_leads_query = clone $baseQuery;
        // $primary_leads = $primary_leads_query->whereHas('agent', function ($query) {
        //     $query->where('bitrix_role', 'like', '%Primary%')
        //         ->where('bitrix_role', 'not like', '%Primary Admin%')
        //         ->where('bitrix_role', 'not like', '%Secondary%')
        //         ->where('bitrix_role', 'not like', '%Abu-Dhabi Team%')
        //         ->where('status', true)->where('region_id', '!=', 2);
        // })->count();

        // $primary_leads_query_manual = clone $baseQuery;
        // $usersWithLeadCount = User::where('status', true)
        // ->where('bitrix_role', 'not like', '%Secondary%')
        // ->where('bitrix_role', 'not like', '%Abu-Dhabi Team%')
        // ->where('bitrix_role', 'like', '%Primary%')
        // ->where('bitrix_role', 'not like', '%Primary Admin%')->where('region_id', '!=', 2)
        // ->withCount([
        //     'leads' => function ($query) use ($cutoff) {
        //         $query->where('status', true)
        //             ->where('is_archive', 0)
        //             ->whereNull('converted_to_listing_lead')
        //             ->where('lead_status_id', 1);
        //     },
        // ])->get();
        // $message_lead = "";
        // $lead_count_primary = 0;
        // $agent_count_primary = 0;
        // $agent_count_primary_1 = 0;
        // $agent_count_primary_2 = 0;

        // $usersWithLeadCount = $usersWithLeadCount->sortByDesc('leads_count');

        // foreach ($usersWithLeadCount as $user) {
        //     $leadCount = $user->leads_count;
        //     if ($leadCount != 0) {
        //         if ($leadCount == 1) {
        //             $agent_count_primary_1++;
        //             $lead_count_primary += $leadCount;
        //         } else if ($leadCount == 2) {
        //             $agent_count_primary_2++;
        //             $lead_count_primary += $leadCount;
        //         } else {
        //             $message_lead .= '  ' . $user->full_name . ": " . $leadCount . "\n";
        //             $lead_count_primary += $leadCount;
        //         }

        //     }
        //     $agent_count_primary++;
        // }
        // if ($agent_count_primary_2 != 0) {
        //     $message_lead .= '  ' . $agent_count_primary_2 . " agents with 2 'NEW' leads" . "\n";
        // }
        // if ($agent_count_primary_1 != 0) {
        //     $message_lead .= '  ' . $agent_count_primary_1 . " agents with 1 'NEW' lead" . "\n";
        // }
        // $message_lead .= "  <b>Total Primary Leads:</b> " . $lead_count_primary . "\n";

        // $message .= "<b>PRIMARY</b>\n";
        // $message .= $message_lead;
        // $message .= "========================== \n\n\n";
        // $primary_message = $message;

        // ------------------------------------------------------//
        // ------------------------------------------------------//
        // ------------------Chinese Lead code-------------------//
        // ------------------------------------------------------//
        // ------------------------------------------------------//

        $chinese_team_leads_query = clone $baseQuery;
        $chinese_team_leads = $chinese_team_leads_query->whereHas('agent', function ($query) {
            $query->where('bitrix_role', 'like', '%Chinese%')
                ->whereNot('id', 172)
                ->where('is_commercial', 0)
                ->where('bitrix_role', 'not like', '%Chinese Admin%')->where('region_id', '!=', 2);
        })->count();

        $usersWithLeadCount = User::whereNot('id', 172)->where('status', true)
            ->where('bitrix_role', 'like', '%Chinese%')
            ->where('bitrix_role', 'not like', '%Chinese Admin%')
            ->where('region_id', '!=', 2)
            ->where('is_commercial', 0)
            ->withCount([
                'leads' => function ($query) use ($cutoff) {
                    $query->where('status', true)
                        ->where('is_archive', 0)
                        ->whereNull('converted_to_listing_lead')
                        ->where('lead_status_id', 1);
                },
            ])->get();
        $message_lead = "";
        $lead_count_chinese = 0;
        $agent_count_chinese = 0;
        $agent_count_chinese_1 = 0;
        $agent_count_chinese_2 = 0;
        $usersWithLeadCount = $usersWithLeadCount->sortByDesc('leads_count');

        foreach ($usersWithLeadCount as $user) {
            $leadCount = $user->leads_count;
            if ($leadCount != 0) {
                if ($leadCount == 1) {
                    $agent_count_chinese_1++;
                    $lead_count_chinese += $leadCount;
                } else if ($leadCount == 2) {
                    $agent_count_chinese_2++;
                    $lead_count_chinese += $leadCount;
                } else {
                    $message_lead .= '  ' . $user->full_name . ": " . $leadCount . "\n";
                    $lead_count_chinese += $leadCount;
                }
            }
            $agent_count_chinese++;
        }
        if ($agent_count_chinese_2 != 0) {
            $message_lead .= '  ' . $agent_count_chinese_2 . " agents with 2 'NEW' leads" . "\n";
        }
        if ($agent_count_chinese_1 != 0) {

            $message_lead .= '  ' . $agent_count_chinese_1 . " agents with 1 'NEW' lead" . "\n";
        }
        $message_lead .= "  <b>Total Chinese Leads: " . $lead_count_chinese . "</b>\n";
        // $message = '';
        // $message .= "<b>CHINESE </b> \n";
        // $message .= $message_lead;
        // $message .= "========================== \n\n\n";
        // $chinese_message = $message;
        $chinese_message = '';

        // ------------------------------------------------------//
        // ------------------------------------------------------//
        // -------------------Abu Dhabi Leads--------------------//
        // ------------------------------------------------------//
        // ------------------------------------------------------//

        $abu_dhabi_leads_query = clone $baseQuery;
        $abu_dhabi_leads = $abu_dhabi_leads_query->wherehas('agent', function ($q) {
            $q->whereNot('id', 172)->where('region_id', 2)->where('is_commercial', 0)
            ;
        })->count();

        $usersWithLeadCount = User::whereNot('id', 172)->where('is_commercial', 0)->where('status', true)->where('region_id', 2)->withCount([
            'leads' => function ($query) use ($cutoff) {
                $query->where('status', true)
                    ->where('is_archive', 0)
                    ->whereNull('converted_to_listing_lead')
                    ->where('lead_status_id', 1);
            },
        ])->get();
        $message_lead = "";
        $lead_count_abu_dhabi = 0;
        $agent_count_abu_dhabi = 0;
        $agent_count_abu_dhabi_1 = 0;
        $agent_count_abu_dhabi_2 = 0;
        $usersWithLeadCount = $usersWithLeadCount->sortByDesc('leads_count');
        foreach ($usersWithLeadCount as $user) {
            $leadCount = $user->leads_count;
            if ($leadCount != 0) {
                if ($leadCount == 1) {
                    $agent_count_abu_dhabi_1++;
                    $lead_count_abu_dhabi += $leadCount;
                } else if ($leadCount == 2) {
                    $agent_count_abu_dhabi_2++;
                    $lead_count_abu_dhabi += $leadCount;
                } else {
                    $message_lead .= '  ' . $user->full_name . ": " . $leadCount . "\n";
                    $lead_count_abu_dhabi += $leadCount;
                }
            }
            $agent_count_abu_dhabi++;
        }

        if ($agent_count_abu_dhabi_2 != 0) {
            $message_lead .= '  ' . $agent_count_abu_dhabi_2 . " agents with 2 'NEW' leads" . "\n";
        }
        if ($agent_count_abu_dhabi_1 != 0) {
            $message_lead .= '  ' . $agent_count_abu_dhabi_1 . " agents with 1 'NEW' lead" . "\n";

        }

        $message_lead .= "  <b>Total Abu Dhabi Leads: " . $lead_count_abu_dhabi . "</b>";

        $message_lead .= "\n";
        $message = '';
        $message .= "<b>ABU DHABI</b> \n";
        $message .= $message_lead;
        $message .= "========================== \n\n\n";
        $abu_dhabi_message = $message;

        // ------------------------------------------------------//
        // ------------------------------------------------------//
        // -------------------Commercial Leads--------------------//
        // ------------------------------------------------------//
        // ------------------------------------------------------//

        $commercial_leads_query = clone $baseQuery;
        $commercial_leads = $commercial_leads_query->wherehas('agent', function ($q) {
            $q->whereNot('id', 172)->where('is_commercial', 1);
        })->count();

        $usersWithLeadCount = User::whereNot('id', 172)->where('is_commercial', 1)->where('status', true)->withCount([
            'leads' => function ($query) use ($cutoff) {
                $query->where('status', true)
                    ->where('is_archive', 0)
                    ->whereNull('converted_to_listing_lead')
                    ->where('lead_status_id', 1);
            },
        ])->get();
        $message_lead = "";
        $lead_count_commercial = 0;
        $agent_count_commercial = 0;
        $agent_count_commercial_1 = 0;
        $agent_count_commercial_2 = 0;
        $usersWithLeadCount = $usersWithLeadCount->sortByDesc('leads_count');
        foreach ($usersWithLeadCount as $user) {
            $leadCount = $user->leads_count;
            if ($leadCount != 0) {
                if ($leadCount == 1) {
                    $agent_count_commercial_1++;
                    $lead_count_commercial += $leadCount;
                } else if ($leadCount == 2) {
                    $agent_count_commercial_2++;
                    $lead_count_commercial += $leadCount;
                } else {
                    $message_lead .= '  ' . $user->full_name . ": " . $leadCount . "\n";
                    $lead_count_commercial += $leadCount;
                }
            }
            $agent_count_commercial++;
        }

        if ($agent_count_commercial_2 != 0) {
            $message_lead .= '  ' . $agent_count_commercial_2 . " agents with 2 'NEW' leads" . "\n";
        }
        if ($agent_count_commercial_1 != 0) {
            $message_lead .= '  ' . $agent_count_commercial_1 . " agents with 1 'NEW' lead" . "\n";

        }

        $message_lead .= "  <b>Total Commercial Leads: " . $lead_count_commercial . "</b>";

        $message_lead .= "\n";
        $message = '';
        $message .= "<b>COMMERCIAL</b> \n";
        $message .= $message_lead;
        $message .= "========================== \n\n\n";
        $commercial_message = $message;

        // ------------------------------------------------------//
        // ------------------------------------------------------//
        // -------------------Secondary Leads--------------------//
        // ------------------------------------------------------//
        // ------------------------------------------------------//

        $secondary_leads_query = clone $baseQuery;
        $secondary_leads = $secondary_leads_query->whereHas('agent', function ($query) {
            $query
                ->where('bitrix_role', 'like', '%Secondary%')
                ->where('bitrix_role', 'not like', '%Secondary Admin%')
                ->where('bitrix_role', 'not like', '%Primary%')
                ->where('region_id', '!=', 2)
                ->where('is_commercial', 0)
                ->whereNot('id', 172)
                ->whereHas('roles', function ($roleQuery) {
                    $roleQuery->where('name', 'Agent'); // Adjust based on your actual role name
                });
        })->count();

        $usersWithLeadCount = User::whereNot('id', 172)->where('bitrix_role', 'like', '%Secondary%')
            ->where('bitrix_role', 'not like', '%Secondary Admin%')->where('region_id', '!=', 2)
            ->where('is_commercial', 0)
            ->where('bitrix_role', 'not like', '%Primary%')->with('roles')->whereHas('roles', function ($q) {
            $q->where('name', 'Agent');
        })->where('status', true)->withCount([
            'leads' => function ($query) use ($cutoff) {
                $query->where('status', true)
                    ->where('is_archive', 0)
                    ->whereNull('converted_to_listing_lead')
                    ->where('lead_status_id', 1);
            },
        ])->get();

        $message_lead_secondary = "";
        $lead_count_secondary = 0;
        $agent_count_secondary = 0;
        $lead_count_secondary_1 = 0;
        $lead_count_secondary_2 = 0;
        $usersWithLeadCount = $usersWithLeadCount->sortByDesc('leads_count');

        foreach ($usersWithLeadCount as $user) {
            $leadCount = $user->leads_count;
            if ($leadCount != 0) {
                if ($leadCount == 1) {
                    $lead_count_secondary_1++;
                    $lead_count_secondary += $leadCount;
                } else if ($leadCount == 2) {
                    $lead_count_secondary_2++;
                    $lead_count_secondary += $leadCount;
                } else {
                    $message_lead_secondary .= '  ' . $user->full_name . ": " . $leadCount . "\n";
                    $lead_count_secondary += $leadCount;
                }
            }
            $agent_count_secondary++;
        }

        $message_lead .= "Total Secondary Agents with 1 lead: " . $lead_count_secondary_1 . "\n";
        $message_lead .= "Total Secondary Agents with 2 leads: " . $lead_count_secondary_2 . "\n";
        $message_lead .= "<b>Total Secondary Leads: " . $lead_count_secondary . "</b>\n";
        $message_lead .= "\n";
        $message = '';
        $message .= "<b>SECONDARY</b> \n";
        if ($lead_count_secondary_2 != 0) {
            $message_lead_secondary .= '  ' . $lead_count_secondary_2 . " agents with 2 'NEW' leads" . "\n";
        }
        if ($lead_count_secondary_1 != 0) {
            $message_lead_secondary .= '  ' . $lead_count_secondary_1 . " agents with 1 'NEW' lead" . "\n";
        }
        $message_lead_secondary .= "  <b>Total Secondary Leads: " . $lead_count_secondary . "</b>\n";

        $message .= $message_lead_secondary;
        $message .= "==========================  \n\n";
        $secondary_message = $message;

        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "⚠️ <b>Leads in 'NEW' Status</b> ⚠️\n\n";
        $message .= $secondary_message;
        $message .= $commercial_message;
        $message .= $abu_dhabi_message;
        // $message .= $chinese_message;

        $message .= "\n";
        // $totalSourceLeads = $lead_count_secondary + $lead_count_chinese + $lead_count_abu_dhabi + $lead_count_commercial;
        $totalSourceLeads = $lead_count_secondary  + $lead_count_abu_dhabi + $lead_count_commercial;
        $message .= '📊<b>Total leads in "NEW" Status' . ': ' . $totalSourceLeads . "</b> 💼\n";

        $telegram = Telegram::bot('AxBot');

        $telegramMessage = $this->sendTelegramMessage($telegram, $message);
        info('Agent Group New Status Leads Notification sent successfully');
        info($message);
        // return Command::SUCCESS;

        // $message = "<b>AX Secondary CRM</b>\n\n";
        // $message .= "🚀 <b>CRM Group Report</b> 🚀\n\n";
        // $message .= "🔍 <b>Summary:</b> \n\n";

        // $message_lead_secondary .= "Total Secondary Leads: " . $lead_count_secondary . "\n";
        // $message_lead_secondary .= "Total Secondary Agents with 1 lead: " . $lead_count_secondary_1 . "\n";
        // $message_lead_secondary .= "Total Secondary Agents with 2 leads: " . $lead_count_secondary_2 . "\n";

        // $message_lead_secondary .= "\n";
        // $message .= "Secondary Leads :" . $secondary_leads . "  🔄 \n";
        // $message .= $message_lead_secondary;
        // $message .= "---------------- \n\n";
        // $message .= '<b>Note:</b> Incomplete details include missing Client Name and/or Property Reference Number for the relevant inquiry' . "\n\n";

        $hours = 24;
        $total_leads_by_source = 0;
        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "🚀 <b>Leads by Source (Past ".$hours." hrs)</b> 🚀\n\n";
        $message .= "<b>SECONDARY</b> \n";
        $secondary_agents = User::whereNot('id', 172)->where('is_commercial', 0)->where('status', true)->where('bitrix_role', 'like', '%Secondary%')->where('bitrix_role', 'not like', '%Abu-Dhabi Team%')->where('bitrix_role', 'not like', '%Primary%')->where('bitrix_role', 'not like', '%Primary Admin%')->get();
        $secondary_agents_id = $secondary_agents->pluck('id')->toArray();
        $sourceWithLeadsCount = Source::whereHas("leads", function ($query) use ($secondary_agents_id, $hours) {
            $query
                // ->where('status', 1)
                ->where('created_at', '>=', now()->subHours($hours))
                ->whereIn('agent_id', $secondary_agents_id);
                // ->where('is_archive', 0)
                // ->whereNull('converted_to_listing_lead');
        })->withCount(['leads as leads_count' => function ($query) use ($secondary_agents_id, $hours) {
            $query
                // ->where('is_archive', 0)
                ->whereIn('agent_id', $secondary_agents_id)
                ->where('created_at', '>=', now()->subHours($hours));
                // ->where('status', 1)
                // ->whereNull('converted_to_listing_lead');
        }])
            ->where('status', true)
            ->orderByDesc('leads_count')
            ->get();

        $totalSourceLeads = 0;
        foreach ($sourceWithLeadsCount as $source) {
            $message .= '  ' . $source->name . ': ' . '<b>' . $source->leads_count . '</b>' . "\n";
            $totalSourceLeads += $source->leads_count;
            $total_leads_by_source += $source->leads_count;

        }
        $message .= '  <b>Total' . ': ' . $totalSourceLeads . "</b>\n";
        $message .= "========================== \n\n";

//        $message .= "<b>COMMERCIAL</b> \n";
//        $commercial = User::whereNot('id', 172)->where('is_commercial', 1)->where('status', true)->get();
//        $commercial_id = $commercial->pluck('id')->toArray();
//        $sourceWithLeadsCount = Source::whereHas("leads", function ($query) use ($commercial_id, $hours) {
//            $query
//                // ->where('status', 1)
//                ->where('created_at', '>=', now()->subHours($hours))
//                ->whereIn('agent_id', $commercial_id);
//                // ->where('is_archive', 0)
//                // ->whereNull('converted_to_listing_lead');
//        })->withCount(['leads as leads_count' => function ($query) use ($commercial_id, $hours) {
//            $query
//                // ->where('is_archive', 0)
//                ->whereIn('agent_id', $commercial_id)
//                ->where('created_at', '>=', now()->subHours($hours));
//                // ->where('status', 1)
//                // ->whereNull('converted_to_listing_lead');
//        }])
//            ->where('status', true)
//            ->orderByDesc('leads_count')
//            ->get();
//
//        $totalSourceLeads = 0;
//        foreach ($sourceWithLeadsCount as $source) {
//            $message .= '  ' . $source->name . ': ' . '<b>' . $source->leads_count . '</b>' . "\n";
//            $totalSourceLeads += $source->leads_count;
//            $total_leads_by_source += $source->leads_count;
//
//        }
//
//        $message .= '  <b>Total' . ': ' . $totalSourceLeads . "</b>\n";
//        $message .= "========================== \n\n";

        $message .= "<b>ABU DHABI</b> \n";
        $abu_dhabi_agents = User::whereNot('id', 172)->where('is_commercial', 0)->where('status', true)->where('region_id', 2)->get();
        $abu_dhabi_agents_id = $abu_dhabi_agents->pluck('id')->toArray();
        $sourceWithLeadsCount = Source::whereHas("leads", function ($query) use ($abu_dhabi_agents_id, $hours) {
            $query
                // ->where('status', 1)
                ->where('created_at', '>=', now()->subHours($hours))
                ->whereIn('agent_id', $abu_dhabi_agents_id);
                // ->where('is_archive', 0)
                // ->whereNull('converted_to_listing_lead');
        })->withCount(['leads as leads_count' => function ($query) use ($abu_dhabi_agents_id, $hours) {
            $query
                // ->where('is_archive', 0)
                ->whereIn('agent_id', $abu_dhabi_agents_id)
                ->where('created_at', '>=', now()->subHours($hours));
                // ->where('status', 1)
                // ->whereNull('converted_to_listing_lead');
        }])
            ->where('status', true)
            ->orderByDesc('leads_count')
            ->get();

        $totalSourceLeads = 0;
        foreach ($sourceWithLeadsCount as $source) {
            $message .= '  ' . $source->name . ': ' . '<b>' . $source->leads_count . '</b>' . "\n";
            $totalSourceLeads += $source->leads_count;
            $total_leads_by_source += $source->leads_count;

        }

        $message .= '  <b>Total' . ': ' . $totalSourceLeads . "</b>\n";
        $message .= "========================== \n\n";

        // $message .= "<b>CHINESE</b> \n";
        $chinese_agents = User::whereNot('id', 172)->where('is_commercial', 0)->where('status', true)->where('bitrix_role', 'like', '%Chinese%')->get();
        $chinese_agents_id = $chinese_agents->pluck('id')->toArray();
        $sourceWithLeadsCount = Source::whereHas("leads", function ($query) use ($chinese_agents_id, $hours) {
            $query
                // ->where('status', 1)
                ->where('created_at', '>=', now()->subHours($hours))
                ->whereIn('agent_id', $chinese_agents_id);
                // ->where('is_archive', 0)
                // ->whereNull('converted_to_listing_lead');
        })->withCount(['leads as leads_count' => function ($query) use ($chinese_agents_id, $hours) {
            $query
                // ->where('is_archive', 0)
                ->whereIn('agent_id', $chinese_agents_id)
                ->where('created_at', '>=', now()->subHours($hours));
                // ->where('status', 1)
                // ->whereNull('converted_to_listing_lead');
        }])
            ->where('status', true)
            ->orderByDesc('leads_count')
            ->get();

        $totalSourceLeads = 0;
        foreach ($sourceWithLeadsCount as $source) {
            $message .= '  ' . $source->name . ': ' . '<b>' . $source->leads_count . '</b>' . "\n";
            $totalSourceLeads += $source->leads_count;
            $total_leads_by_source += $source->leads_count;

        }
        // $message .= '  <b>Total' . ': ' . $totalSourceLeads . "</b>\n";

        // $message .= "========================== \n\n";
        // $message .= "<b>PRIMARY</b>\n";
        // $primary_agents = User::whereNot('id', 172)->where('is_commercial', 0)->where('status', true)->where('bitrix_role', 'not like', '%Secondary%')->where('bitrix_role', 'not like', '%Abu-Dhabi Team%')->where('bitrix_role', 'like', '%Primary%')->where('bitrix_role', 'not like', '%Primary Admin%')->get();
        // $primary_agents_id = $primary_agents->pluck('id')->toArray();
        // $sourceWithLeadsCount = Source::whereHas("leads", function ($query) use ($primary_agents_id, $hours) {
        //     $query
        //         ->where('status', 1)
        //         ->where('created_at', '>=', now()->subHours($hours))
        //         ->whereIn('agent_id', $primary_agents_id)
        //         ->where('is_archive', 0)
        //         ->whereNull('converted_to_listing_lead');
        // })->withCount(['leads as leads_count' => function ($query) use ($primary_agents_id, $hours) {
        //     $query
        //         ->where('is_archive', 0)
        //         ->whereIn('agent_id', $primary_agents_id)
        //         ->where('created_at', '>=', now()->subHours($hours))
        //         ->where('status', 1)
        //         ->whereNull('converted_to_listing_lead');
        // }])
        //     ->where('status', true)
        //     ->orderByDesc('leads_count')
        //     ->get();

        // $totalSourceLeads = 0;
        // foreach ($sourceWithLeadsCount as $source) {
        //     $message .= '  ' . $source->name . ': ' . '<b>' . $source->leads_count . '</b>' . "\n";
        //     $totalSourceLeads += $source->leads_count;
        //     $total_leads_by_source += $source->leads_count;
        // }
        // $message .= '  <b>Total' . ': ' . $totalSourceLeads . "</b>\n";
        // $message .= "========================== \n\n";

        $message .= '  <b>Total Leads by Source' . ': ' . $total_leads_by_source . "</b> \n";

        $telegramMessage = $this->sendTelegramMessage($telegram, $message);

        return Command::SUCCESS;

    }

    public function sendTelegramMessage($telegram, $message)
    {
        try {
            if (env('APP_ENV') == 'production') {
                $chatids = [
                    '-1001435755800',
                    '1473874718'
                ];

                foreach ($chatids as $chatid) {
                    $telegram->sendMessage([
                        'chat_id' => $chatid,
                        'text' => $message,
                        'parse_mode' => 'html',
                    ]);
                }

            } else {
                $telegram = Telegram::bot('AxBot');
                $chatids = [
                    '795165330'
//                    '6085218419',
//                    '1417490671',
                    // '5970238984',
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
            \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: -1001435755800 message is: " . $message);
            return false;
        }
    }

}
