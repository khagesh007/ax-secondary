<?php

namespace App\Console\Commands;

use App\Models\Approval;
use App\Models\Contact;
use App\Models\ContactMedia;
use App\Models\ContactNote;
use App\Models\Event;
use App\Models\LeadMedia;
use App\Models\ModelUpdate;
use App\Models\PropertyMedia;
use App\Models\User;
use App\Models\UserLastActivity;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\Property;
use App\Models\PropertyNote;
use App\Models\ListingLead;
use App\Models\ListingLeadNote;
use Spatie\Activitylog\Models\Activity;
class AgentLastActivityGrouped extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agent:lastActivityReportGrouped';
    protected $counter = 0;

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
        $collection = collect();
        $exempted_users = [
            1403, 307, 4, 172,1046, 1047, 1081, 1215, 1432,1522

        ];

        $users = User::where('is_report_exempt',false)
            ->where('status', true)
            ->whereNotIn('id', $exempted_users)
            ->whereHas('roles',function($q){
                $q->where('name','Agent');
            })->chunk(50, function ($chunk) use ($collection) {



                foreach ($chunk as $user) {
//
//                    $approval = Approval::whereJsonContains('new_data', ['updated_by_id' => $user->id])->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->first();
//                    $lead_created = Lead::where('created_by_id', $user->id)->orderBy('updated_at', 'desc')->orderBy('created_at', 'desc')->first();
//                    $lead_updated = Lead::where('updated_by_id', $user->id)->orWhere('created_by_id', $user->id)->orderBy('updated_at', 'desc')->orderBy('created_at', 'desc')->first();
//                    $contact = Contact::where('updated_by_id', $user->id)->orWhere('created_by_id', $user->id)->orderBy('updated_at', 'desc')->orderBy('created_at', 'desc')->first();
//                    $lead_note = LeadNote::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
//                    $property_created = Property::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
//                    $property_updated = Property::where('updated_by_id', $user->id)->orderBy('updated_at', 'desc')->first();
//                    $property_note = PropertyNote::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
//                    $listingLeadCreated = ListingLead::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
//                    $listingLeadUpdated = ListingLead::where('updated_by_id', $user->id)->orderBy('updated_at', 'desc')->first();
//                    $listingLead_note = ListingLeadNote::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
//                    $events = Event::where('created_by_id', $user->id)->orWhere('updated_by_id', $user->id)->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->first();
//                    $contact_media = ContactMedia::where('created_by_id', $user->id)->orWhere('updated_by_id', $user->id)->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->first();
//                    $lead_media = LeadMedia::where('created_by_id', $user->id)->orWhere('updated_by_id', $user->id)->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->first();
//                    $property_media = PropertyMedia::where('created_by_id', $user->id)->orWhere('updated_by_id', $user->id)->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->first();
//                    $contact_note = ContactNote::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
//                    $model_updates = ModelUpdate::where('updated_by_id', $user->id)->orderBy('created_at', 'desc')->first();
//                    $activity_log = Activity::where('causer_id', $user->id)->where('causer_type',User::class)->orderBy('created_at', 'desc')->first();
////                $userModel = 'App\\Models\\User:'.$user->id;
////                $telescope_login_entry = DB::connection('log_db')
////                    ->table('telescope_entries')
////                    ->whereJsonContains('content->name', 'PragmaRX\\Google2FALaravel\\Events\\LoginSucceeded')
////                    ->whereJsonContains('content->payload->user', $userModel)
////                    ->latest()->pluck('created_at')->first() ?? null;
//
//                    $last_time = collect([
//                        $user?->last_login ?? null,
//                        $contact?->updated_at ?? null,
//                        $contact?->created_at ?? null,
//                        $lead_updated?->updated_at ?? null,
//                        $lead_created?->created_at ?? null,
//                        $lead_note?->created_at ?? null,
//                        $property_updated?->updated_at ?? null,
//                        $property_created?->created_at ?? null,
//                        $property_note?->created_at ?? null,
//                        $listingLeadUpdated?->updated_at ?? null,
//                        $listingLeadCreated?->created_at ?? null,
//                        $listingLead_note?->created_at ?? null,
//                        $approval?->updated_at ?? null,
//                        $approval?->created_at ?? null,
//                        $events?->updated_at ?? null,
//                        $events?->created_at ?? null,
//                        $contact_media?->updated_at ?? null,
//                        $contact_media?->created_at ?? null,
//                        $lead_media?->updated_at ?? null,
//                        $lead_media?->created_at ?? null,
//                        $property_media?->updated_at ?? null,
//                        $property_media?->created_at ?? null,
//                        $contact_note?->created_at ?? null,
//                        $model_updates?->created_at ?? null,
//                        $activity_log?->created_at ?? null,
//                    ])->filter()->max();

                    $last_activity = UserLastActivity::where('user_id', $user->id)->first();
                    if($last_activity != null) {
                        $last_time = $last_activity->last_active;
                    }else{
                        $last_time = null;
                    }
                    $last_login_time =   $user?->last_login ?? null;
                    $last_login_time = $last_login_time ? getSingleUnitTimeDiff($last_login_time) : null;
                    $hours = 0;

                    if($last_time == null){
                        $tempMsg = "-<b>" . $user->full_name . "</b> has No Activity.\n";
                        $minutes = 0;
                    }else{
//                    $last_login = Carbon::parse($last_time);
//                    $current_time = Carbon::now();
//                    $time_difference = $current_time->diff($last_login);
//
//                    $hours = $time_difference->h + ($time_difference->d * 24);

                        $last_login = new \DateTime($last_time);
                        $current_time = new \DateTime();
                        $time_difference = $current_time->diff($last_login);

                        $hours = $time_difference->h + ($time_difference->days * 24);
                        $minutes = $time_difference->i;

                        $dayHours =  formatTimeReport($hours);
                        //$tempMsg = "-<b>" . $user->full_name . "</b> : (🕐" .$last_login_time.") ".$dayHours." ".$minutes. "m\n";
                        if($last_login_time != null){
                            $tempMsg = "-<b>" . $user->full_name . "</b> (🕐" .$last_login_time.") " . $dayHours . $minutes . "m\n";
                        }else {
                            $tempMsg = "-<b>" . $user->full_name . "</b> ". $dayHours . $minutes . "m\n";
                        }

                    }
                    if($hours < 1) {
                        if ($minutes > 0){
                            $hours = $minutes / 60;
                        }
                    }

                    $collection->push([
                        'user_id' => $user->id,
                        'hours' => $hours,
                        'message' => $tempMsg
                    ]);

                    //sort the collection



                }


            });
        $sorted = $collection->sortBy('hours');
        $sorted->values()->all();
        $collection = $sorted->reverse();

        $moreThanAMonth = [];
        $moreThan15Days = [];
        $moreThan7days = [];
        $moreThan3days = [];
        $moreThan48Hours = [];
        $moreThan24Hours = [];
        $lessThan24Hours = [];
        $extra = [];
        foreach ($collection as $item) {
         //   $this->info($item['message'].' -- '.(float)$item['hours']);
            $days = floor($item['hours'] / 24);
            $remaining_hours = $item['hours'] % 24;
            //$remaining_hours = floor($remaining_hours);
            $hr = (float)$item['hours'];
            if($hr < 24) {
                $lessThan24Hours[] = $item;
            } else if($hr >= 24 && $hr < 48) {
                $moreThan24Hours[] = $item;
            } else if($hr >= 48 && $hr < 72) {
                $moreThan48Hours[] = $item;
            } else if($hr >= 72 && $hr < 168) { //more than 3 days less than 7 days
                $moreThan3days[] = $item;
            } else if($hr >= 168 && $hr < 360) { //more than 7 days less than 15 days
                $moreThan7days[] = $item;
            } else if($hr >= 360 && $hr < 720) { //more than 15 days less than 30 days (720 hours = 30 days)
                $moreThan15Days[] = $item;
            } else if($hr >= 720){ // 30 days or more
                $moreThanAMonth[] = $item;
            } else {
                $extra[] = $item;
            }

        }

        $header  = "<b>AX Secondary CRM</b>\n\n";
        $header .= "⏰ Agent Last Activity Report  ⏰\n";
           $message = '';
        if(count($moreThanAMonth)>0) {
            $message .= $header;
            $message .= "----⚠️More than a month ⚠️----\n\n";
            $array = array_chunk($moreThanAMonth, 40);
            $i = 0;
            foreach ($array as $items) {
                if($i != 0) {
                    $message = '';
                }
                foreach ($items as $item) {
                    $message .= $item['message'];
                }
                $telegram = Telegram::bot('notification_bot');
                $this->sendTelegramMessage($telegram, $message);
                $i++;
            }
        }
        $message = '';
        if(count($moreThan15Days)>0) {
            $message .= $header;

            $message .= "----⚠️More than 15 days ⚠️----\n\n";
            $array = array_chunk($moreThan15Days, 40);
            $i = 0;
            foreach ($array as $items) {
                if($i != 0) {
                    $message = '';
                }
                foreach ($items as $item) {
                    $message .= $item['message'];
                }
                $telegram = Telegram::bot('notification_bot');
                $this->sendTelegramMessage($telegram, $message);
                $i++;
            }
        }
        $message = '';
        if(count($moreThan7days)>0) {

        $message.= $header;
        $message.= "----⚠️More than 7 days ⚠️----\n\n";
        //split this array in multiple arrays of 40 items otherwise telegram will throw long text error
            $array = array_chunk($moreThan7days, 40);
            $i = 0;
            foreach ($array as $items) {
              if($i != 0) {
                  $message = '';
              }
                foreach ($items as $item) {
                    $message .= $item['message'];
                }
                $telegram = Telegram::bot('notification_bot');
               $this->sendTelegramMessage($telegram, $message);
            $i++;
            }
        }
        $message = '';
        if(count($moreThan3days)>0) {

        $message.= $header;
        $message.= "----⚠️More than 3 days ⚠️----\n\n";

            $array = array_chunk($moreThan3days, 40);
            $i = 0;
            foreach ($array as $items) {
                if($i != 0) {
                    $message = '';
                }
                foreach ($items as $item) {
                    $message .= $item['message'];
                }
                $telegram = Telegram::bot('notification_bot');
                $this->sendTelegramMessage($telegram, $message);
                $i++;
            }
            }
        $message = '';
        if(count($moreThan48Hours)>0) {
            $message .= $header;
            $message .= "----⚠️More than 48 hours ⚠️----\n\n";
            $array = array_chunk($moreThan48Hours, 40);
            $i = 0;
            foreach ($array as $items) {
                if($i != 0) {
                    $message = '';
                }
                foreach ($items as $item) {
                    $message .= $item['message'];
                }
                $telegram = Telegram::bot('notification_bot');
                $this->sendTelegramMessage($telegram, $message);
                $i++;
            }
        }
        $message = '';
        if(count($moreThan24Hours)>0) {
            $message .= $header;
            $message .= "----⚠️More than 24 hours ⚠️----\n\n";
            $array = array_chunk($moreThan24Hours, 40);
            $i = 0;
            foreach ($array as $items) {
                if($i != 0) {
                    $message = '';
                }
                foreach ($items as $item) {
                    $message .= $item['message'];
                }
                $telegram = Telegram::bot('notification_bot');
                $this->sendTelegramMessage($telegram, $message);
                $i++;
            }
        }

        $message = '';
        if(count($lessThan24Hours)>0){
        $message.= $header;
        $message.= "----⚠️ Less than 24 hours ⚠️----\n\n";
            $array = array_chunk($lessThan24Hours, 40);
            $i = 0;
            foreach ($array as $items) {
                if($i != 0) {
                    $message = '';
                }
                foreach ($items as $item) {
                    $message .= $item['message'];
                }
                $telegram = Telegram::bot('notification_bot');
                $this->sendTelegramMessage($telegram, $message);
                $i++;
            }
            }

        return Command::SUCCESS;
    }

    public function sendTelegramMessage($telegram, $message)
    {

        if (env('APP_ENV') == 'production') {

            $chatids = [
               // '795165330', //khagesh id
               // '5970238984',
                '1417490671', //Hishmat id
                // '1669212762' //ben thomos
            ];

            foreach ($chatids as $chatid) {
                try {
                    $telegram->sendMessage([
                        'chat_id' => $chatid,
                        'text' => $message,
                        'parse_mode' => 'html',
                    ]);
                   // \Log::info('Telegram message sent to chat_id: ' . $chatid . ' message is: ' . $message);
                } catch (\Throwable $th) {
                    \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: " . $chatid . " message is: " . $message);
                    continue;
                }
            }

        } else {

            $chatids = [
                 '795165330', //khagesh id
             //  '5970238984',
//                '1417490671', //Hishmat id
//            '1669212762' //ben thomos
            ];
            foreach ($chatids as $chatid) {
                try {
                    $telegram->sendMessage([
                        'chat_id' => $chatid,
                        'text' => $message,
                        'parse_mode' => 'html',
                    ]);
              //      \Log::info('Telegram message sent to chat_id: ' . $chatid . ' message is: ' . $message);

                } catch (\Throwable $th) {
                    \Log::info('Telegram error log: ' . $th->getMessage() . " chat_id is: " . $chatid . " message is: " . $message);
                    continue;
                }
            }

        }

    }
}
