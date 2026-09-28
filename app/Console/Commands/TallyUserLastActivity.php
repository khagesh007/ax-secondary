<?php

namespace App\Console\Commands;

use App\Models\Approval;
use App\Models\Contact;
use App\Models\ContactMedia;
use App\Models\ContactNote;
use App\Models\Event;
use App\Models\Lead;
use App\Models\LeadMedia;
use App\Models\LeadNote;
use App\Models\ListingLead;
use App\Models\ListingLeadNote;
use App\Models\ModelUpdate;
use App\Models\Property;
use App\Models\PropertyMedia;
use App\Models\PropertyNote;
use App\Models\User;
use App\Models\UserLastActivity;
use Illuminate\Console\Command;
use Spatie\Activitylog\Models\Activity;

class TallyUserLastActivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:tally-last-activity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will check user last activity and store it on db';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $exempted_users = [
            1403, 307, 4, 172,1046, 1047, 1081, 1215, 1432,1522

        ];

        $users = User::where('is_report_exempt',false)
            ->where('status', true)
            ->whereNotIn('id', $exempted_users)
            ->whereHas('roles',function($q){
                $q->where('name','Agent');
            })->chunk(10, function ($chunk)  {



                foreach ($chunk as $user) {
                $last_activity = UserLastActivity::where('user_id', $user->id)->first();
                //check if user last activity is less than 10 minutes
                if($last_activity){
                    $last_activity_time = $last_activity->last_active;
                    $now = now();
                    $diff = $now->diffInMinutes($last_activity_time);
                    if($diff < 10) {
                        continue;
                    }
                }
                    $approval = Approval::whereJsonContains('new_data', ['updated_by_id' => $user->id])->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->first();
                    $lead_created = Lead::where('created_by_id', $user->id)->orderBy('updated_at', 'desc')->orderBy('created_at', 'desc')->first();
                    $lead_updated = Lead::where('updated_by_id', $user->id)->orWhere('created_by_id', $user->id)->orderBy('updated_at', 'desc')->orderBy('created_at', 'desc')->first();
                    $contact = Contact::where('updated_by_id', $user->id)->orWhere('created_by_id', $user->id)->orderBy('updated_at', 'desc')->orderBy('created_at', 'desc')->first();
                    $lead_note = LeadNote::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
                    $property_created = Property::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
                    $property_updated = Property::where('updated_by_id', $user->id)->orderBy('updated_at', 'desc')->first();
                    $property_note = PropertyNote::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
                    $listingLeadCreated = ListingLead::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
                    $listingLeadUpdated = ListingLead::where('updated_by_id', $user->id)->orderBy('updated_at', 'desc')->first();
                    $listingLead_note = ListingLeadNote::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
                    $events = Event::where('created_by_id', $user->id)->orWhere('updated_by_id', $user->id)->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->first();
                    $contact_media = ContactMedia::where('created_by_id', $user->id)->orWhere('updated_by_id', $user->id)->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->first();
                    $lead_media = LeadMedia::where('created_by_id', $user->id)->orWhere('updated_by_id', $user->id)->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->first();
                    $property_media = PropertyMedia::where('created_by_id', $user->id)->orWhere('updated_by_id', $user->id)->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->first();
                    $contact_note = ContactNote::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
                    $model_updates = ModelUpdate::where('updated_by_id', $user->id)->orderBy('created_at', 'desc')->first();
                    $activity_log = Activity::where('causer_id', $user->id)->where('causer_type',User::class)->orderBy('created_at', 'desc')->first();
//                $userModel = 'App\\Models\\User:'.$user->id;
//                $telescope_login_entry = DB::connection('log_db')
//                    ->table('telescope_entries')
//                    ->whereJsonContains('content->name', 'PragmaRX\\Google2FALaravel\\Events\\LoginSucceeded')
//                    ->whereJsonContains('content->payload->user', $userModel)
//                    ->latest()->pluck('created_at')->first() ?? null;

                    $last_time = collect([
                        $user?->last_login ?? null,
                        $contact?->updated_at ?? null,
                        $contact?->created_at ?? null,
                        $lead_updated?->updated_at ?? null,
                        $lead_created?->created_at ?? null,
                        $lead_note?->created_at ?? null,
                        $property_updated?->updated_at ?? null,
                        $property_created?->created_at ?? null,
                        $property_note?->created_at ?? null,
                        $listingLeadUpdated?->updated_at ?? null,
                        $listingLeadCreated?->created_at ?? null,
                        $listingLead_note?->created_at ?? null,
                        $approval?->updated_at ?? null,
                        $approval?->created_at ?? null,
                        $events?->updated_at ?? null,
                        $events?->created_at ?? null,
                        $contact_media?->updated_at ?? null,
                        $contact_media?->created_at ?? null,
                        $lead_media?->updated_at ?? null,
                        $lead_media?->created_at ?? null,
                        $property_media?->updated_at ?? null,
                        $property_media?->created_at ?? null,
                        $contact_note?->created_at ?? null,
                        $model_updates?->created_at ?? null,
                        $activity_log?->created_at ?? null,
                    ])->filter()->max();

                    if($last_time == null){
                        UserLastActivity::updateOrCreate(
                            ['user_id' => $user->id],
                            ['last_active' => null]
                        );
                    }else{
                        UserLastActivity::updateOrCreate(
                            ['user_id' => $user->id],
                            ['last_active' => $last_time]
                        );



                    }




                }


            });

        return Command::SUCCESS;
    }
}
