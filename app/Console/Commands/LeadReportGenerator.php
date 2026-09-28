<?php

namespace App\Console\Commands;

use App\Jobs\AgentLeadNotificationJob;
use App\Jobs\ManagerLeadNotificationJob;
use App\Models\Lead;
use App\Models\Source;
use App\Models\User;
use App\Traits\TelegramTrait;
use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class LeadReportGenerator extends Command
{
    use TelegramTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:LeadReportGenerator';

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
        dispatch(new AgentLeadNotificationJob());
        dispatch(new ManagerLeadNotificationJob());
        return true;
        $chineseTeam = array(
                333,
                4,
                319,
                36,
                205,
                21,
                38,
                58,
                40,
                39,
                446,
                64,
                167,
                634,
                660,
                286,
                105,
                213,
                238,
                268,
                32,
                275,
                1024
            );

        $message = "<b>AX Secondary CRM</b>\n\n";
        $message .= "🔴⚠️<b>Leads in `NEW` Status</b>⚠️🔴\n";
        // Getting all the users where they have new leads with their count greater than 2
        $usersWithNewLeads = User::whereHas('leads', function ($query) {
                $query->where('lead_status_id', 1)
                    ->where('status', 1)
                    ->where('is_archive', 0)
                    ->whereNull('converted_to_listing_lead');
            })
            ->withCount(['leads as new_leads_count' => function ($query) {
                $query->where('lead_status_id', 1)
                    ->where('is_archive', 0)
                    ->where('status', 1)
                    ->whereNull('converted_to_listing_lead');
            }])
            ->having('new_leads_count', '>', 2)
            ->whereNotIn('id', $chineseTeam)
            ->where('status', true)
            ->whereHas('roles',function($q){
                $q->where('name', "Agent");
            })
            ->orderByDesc('new_leads_count')
            ->get();


        // Agents Haging 2 Leads
        $agentsWithTwoNewLeads = User::whereHas('leads', function ($query) {
            $query->select('agent_id')
                ->where('lead_status_id', 1)
                ->where('is_archive', 0)
                ->where('status', 1)
                ->whereNull('converted_to_listing_lead')
                ->groupBy('agent_id')
                ->havingRaw('COUNT(*) = 2');
        })
        ->whereNotIn('id', $chineseTeam)
        ->where('status', true)
        ->count();
        // Agents Having 1 Lead
        $agentsWithOneNewLead = User::whereHas('leads', function ($query) {
            $query->select('agent_id')
                ->where('lead_status_id', 1)
                ->where('is_archive', 0)
                ->where('status', 1)
                ->whereNull('converted_to_listing_lead')
                ->groupBy('agent_id')
                ->havingRaw('COUNT(*) = 1');
        })
        ->where('status', true)
        ->whereNotIn('id', $chineseTeam)
        ->count();

        $total = 0;
        foreach($usersWithNewLeads as $user){
            $message.= $user->full_name . ': ' . '<b>' . $user->new_leads_count . '</b>' . "\n";
            $total += $user->new_leads_count;
        }
        if($agentsWithTwoNewLeads){
            $message.=$agentsWithTwoNewLeads." agents with 2 New leads.\n";
            $total += $agentsWithTwoNewLeads * 2;

        }
        if($agentsWithOneNewLead){
            $message.=$agentsWithOneNewLead." agents with 1 New lead.\n";
            $total += $agentsWithOneNewLead;
        }

        $message .= '<b>Total Leads' . ': ' . $total . "</b> in 'NEW' status.\n";
        $message .= "\n";
        $message .= "==========================\n";
        $message .= "\n";
        $message .= "🔵<b>Leads by Source (Past 24 hrs)</b> 🔵\n";

        $sourceWithLeadsCount = Source::whereHas("leads",function($query){
                $query
                    ->where('status', 1)
                    ->where('created_at', '>=', now()->subHours(24))
                    ->whereNotNull('agent_id')
                    ->where('is_archive', 0)
                    ->whereNull('converted_to_listing_lead');
            })->withCount(['leads as leads_count' => function ($query) {
                $query
                    ->where('is_archive', 0)
                    ->whereNotNull('agent_id')
                    ->where('created_at', '>=', now()->subHours(24))
                    ->where('status', 1)
                    ->whereNull('converted_to_listing_lead');
            }])
            ->where('status', true)
            ->orderByDesc('leads_count')
            ->get();

        $totalSourceLeads = 0;
        foreach($sourceWithLeadsCount as $source){
            $message.= $source->name . ': ' . '<b>' . $source->leads_count . '</b>' . "\n";
            $totalSourceLeads += $source->leads_count;
        }
        $message .= '<b>Total' . ': ' . $totalSourceLeads . "</b>.\n";

        $telegram = Telegram::bot('AxBot');

        $telegramMessage = $this->sendTelegramMessage($telegram, $message);

        $hishamUser = User::where('email', "h.elassaad@axcapital.ae")->first();

        if($hishamUser?->telegram_chatid){
            $personalMessage = $this->sendTelegramMessageHtml($message, $hishamUser?->telegram_chatid);
        }

        return Command::SUCCESS;



        // INCOMPLETE DETAILS


        // $message = "<b>AX Secondary CRM</b>\n\n";
        // $message .= "🔴⚠️<b>Leads with `INCOMPLETE DETAILS`</b>⚠️🔴\n";
        // $usersWithNewLeads = User::whereHas('leads', function ($query) {
        //         $query
        //         ->whereNull('property_id')
        //         ->whereNot('lead_status_id', [4,12])
        //         ->where(function ($query) {
        //             $query->where('first_name', 'LIKE', '%new%');
        //         })
        //         ->where('status', 1)
        //         ->where('is_archive', 0)
        //         ->where('created_at', '>=', now()->subHours(24))
        //         ->whereNull('converted_to_listing_lead');
        //     })
        //     ->withCount(['leads as new_leads_count' => function ($query) {
        //         $query ->whereNull('property_id')
        //         ->whereNot('lead_status_id', [4,12])
        //         ->where(function ($query) {
        //             $query->where('first_name', 'LIKE', '%new%');
        //         })
        //         ->where('is_archive', 0)
        //         ->where('status', 1)
        //         ->where('created_at', '>=', now()->subHours(24))
        //         ->whereNull('converted_to_listing_lead');
        //     }])
        //     ->having('new_leads_count', '>', 2)
        //     ->whereNotIn('id', $chineseTeam)
        //     ->where('status', true)
        //     ->whereHas('roles',function($q){
        //         $q->where('name', "Agent");
        //     })
        //     ->orderByDesc('new_leads_count')
        //     ->get();


        // // Agents Haging 2 Leads
        // $agentsWithTwoNewLeads = User::whereHas('leads', function ($query) {
        //     $query->select('agent_id')
        //     ->whereNull('property_id')
        //     ->whereNot('lead_status_id', [4,12])
        //     ->where(function ($query) {
        //         $query->where('first_name', 'LIKE', '%new%');
        //     })
        //     ->where('is_archive', 0)
        //     ->where('status', 1)
        //     ->where('created_at', '>=', now()->subHours(24))
        //     ->whereNull('converted_to_listing_lead')
        //     ->groupBy('agent_id')
        //     ->havingRaw('COUNT(*) = 2');
        // })
        // ->whereNotIn('id', $chineseTeam)
        // ->where('status', true)
        // ->count();
        // // Agents Having 1 Lead
        // $agentsWithOneNewLead = User::whereHas('leads', function ($query) {
        //     $query->select('agent_id')
        //     ->whereNull('property_id')
        //     ->whereNot('lead_status_id', [4,12])
        //     ->where(function ($query) {
        //         $query->where('first_name', 'LIKE', '%new%');
        //     })
        //     ->where('is_archive', 0)
        //     ->where('created_at', '>=', now()->subHours(24))
        //     ->where('status', 1)
        //     ->whereNull('converted_to_listing_lead')
        //     ->groupBy('agent_id')
        //     ->havingRaw('COUNT(*) = 1');
        // })
        // ->where('status', true)
        // ->whereNotIn('id', $chineseTeam)
        // ->count();

        // $total = 0;
        // foreach($usersWithNewLeads as $user){
        //     $message.= $user->full_name . ': ' . '<b>' . $user->new_leads_count . '</b>' . "\n";
        //     $total += $user->new_leads_count;
        // }
        // if($agentsWithTwoNewLeads){
        //     $message.=$agentsWithTwoNewLeads." agents with 2 leads.\n";
        //     $total += $agentsWithTwoNewLeads * 2;

        // }
        // if($agentsWithOneNewLead){
        //     $message.=$agentsWithOneNewLead." agents with 1 lead.\n";
        //     $total += $agentsWithOneNewLead;
        // }

        // $message .= '<b>Total Leads' . ': ' . $total . "</b> with 'INCOMPLETE DETAILS'.\n";
        // $message .= "\n";
        // $message .= "==========================\n";
        // $message .= "\n";
        // $message .= "🔵<b>Leads by Source (Past 24 hrs)</b> 🔵\n";

        // $sourceWithLeadsCount = Source::whereHas("leads",function($query){
        //         $query->where('status', 1)
        //             ->where('created_at', '>=', now()->subHours(24))
        //             ->whereNotNull('agent_id')
        //             ->where('is_archive', 0)
        //             ->whereNull('converted_to_listing_lead');
        //     })->withCount(['leads as leads_count' => function ($query) {
        //         $query ->where('is_archive', 0)
        //             ->whereNotNull('agent_id')
        //             ->where('created_at', '>=', now()->subHours(24))
        //             ->where('status', 1)
        //             ->whereNull('converted_to_listing_lead');
        //     }])
        //     ->where('status', true)
        //     ->orderByDesc('leads_count')
        //     ->get();

        // $totalSourceLeads = 0;
        // foreach($sourceWithLeadsCount as $source){
        //     $message.= $source->name . ': ' . '<b>' . $source->leads_count . '</b>' . "\n";
        //     $totalSourceLeads += $source->leads_count;
        // }
        // $message .= '<b>Total' . ': ' . $totalSourceLeads . "</b>.\n\n";
        // $message .= '<b>Note:</b> Incomplete details include missing Client Name and/or Property Reference Number for the relevant inquiry' . "\n\n";

        // $telegram = Telegram::bot('AxBot');

        // $telegramMessage = $this->sendTelegramMessage($telegram, $message);

    }

    public function sendTelegramMessage($telegram, $message)
    {
        try {
            $telegram->sendMessage([
                'chat_id' => '-1001435755800',
                'text' => $message,
                'parse_mode' => 'html',
            ]);
            return true;
        } catch (\Throwable $th) {
            \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: -1001435755800 message is: " . $message);
            return false;
        }
    }

    public function sendTelegramMessageHtml($message, $chatId)
    {
        try {
            $telegram = Telegram::bot("lead_bot");
            if($chatId){
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => $message,
                    'parse_mode' => 'html',
                ]);
            }

            return true;
        } catch (\Throwable $th) {
            \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: -1001435755800 message is: " . $message);
            return false;
        }
    }

}
