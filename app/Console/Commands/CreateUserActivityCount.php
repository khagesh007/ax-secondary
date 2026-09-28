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
use App\Models\UserActivityCount;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Activitylog\Models\Activity;

class CreateUserActivityCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:user-activity-count';

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
        $exceptedUserIds = [1046, 1317, 1403, 1044, 1047, 1433, 1444, 1445, 1446, 1447, 1434, 1402, 1215, 1119, 1084, 1081, 1045];

        $today = Carbon::now()->format('Y-m-d');

        User::where('is_report_exempt', false)
            ->where('status', true)
            ->whereNotIn('id', $exceptedUserIds)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'Agent');
            })->chunk(50, function ($chunk) use ($today){
                foreach ($chunk as $user) {
                    $approvalCount = Approval::whereJsonContains('new_data', ['updated_by_id' => $user->id])
                        ->where(function ($q) use ($today){
                            $q->whereDate('created_at', $today)->orWhereDate('updated_at', $today);
                        })->count();

                    $leadCount = Lead::where(function ($q) use ($user) {
                        $q->where('updated_by_id', $user->id)->orWhere('created_by_id', $user->id);
                    })->where(function ($q) use ($today){
                        $q->whereDate('created_at', $today)->orWhereDate('updated_at', $today);
                    })->count();

                    $contactCount = Contact::where(function ($q) use ($user) {
                        $q->where('updated_by_id', $user->id)->orWhere('created_by_id', $user->id);
                    })->where(function ($q) use ($today){
                        $q->whereDate('created_at', $today)->orWhereDate('updated_at', $today);
                    })->count();

                    $leadNoteCount = LeadNote::where('created_by_id', $user->id)->where(function ($q) use ($today) {
                        $q->whereDate('created_at', $today)->orWhereDate('updated_at', $today);
                    })->count();

                    $propertyCount = Property::where(function ($q) use ($user) {
                        $q->where('updated_by_id', $user->id)->orWhere('created_by_id', $user->id);
                    })->where(function ($q) use ($today){
                        $q->whereDate('created_at', $today)->orWhereDate('updated_at', $today);
                    })->count();

                    $propertyNoteCount = PropertyNote::where('created_by_id', $user->id)
                        ->where(function ($q) use ($today) {
                            $q->whereDate('created_at', $today)->orWhereDate('updated_at', $today);
                    })->count();

                    $listingLeadCount = ListingLead::where(function ($q) use ($user) {
                        $q->where('updated_by_id', $user->id)->orWhere('created_by_id', $user->id);
                    })->where(function ($q) use ($today){
                        $q->whereDate('created_at', $today)->orWhereDate('updated_at', $today);
                    })->count();

                    $listingLeadNoteCount = ListingLeadNote::where('created_by_id', $user->id)->where(function ($q) use ($today) {
                        $q->whereDate('created_at', $today)->orWhereDate('updated_at', $today);
                    })->count();

                    $eventCount = Event::where(function ($q) use ($user) {
                        $q->where('updated_by_id', $user->id)->orWhere('created_by_id', $user->id);
                    })->where(function ($q) use ($today){
                        $q->whereDate('created_at', $today)->orWhereDate('updated_at', $today);
                    })->count();

                    $contactMediaCount = ContactMedia::where(function ($q) use ($user) {
                        $q->where('updated_by_id', $user->id)->orWhere('created_by_id', $user->id);
                    })->where(function ($q) use ($today){
                        $q->whereDate('created_at', $today)->orWhereDate('updated_at', $today);
                    })->count();

                    $leadMediaCount = LeadMedia::where(function ($q) use ($user) {
                        $q->where('updated_by_id', $user->id)->orWhere('created_by_id', $user->id);
                    })->where(function ($q) use ($today){
                        $q->whereDate('created_at', $today)->orWhereDate('updated_at', $today);
                    })->count();

                    $propertyMediaCount = PropertyMedia::where(function ($q) use ($user) {
                        $q->where('updated_by_id', $user->id)->orWhere('created_by_id', $user->id);
                    })->where(function ($q) use ($today){
                        $q->whereDate('created_at', $today)->orWhereDate('updated_at', $today);
                    })->count();

                    $contactNoteCount = ContactNote::where('created_by_id', $user->id)->where(function ($q) use ($today) {
                        $q->whereDate('created_at', $today)->orWhereDate('updated_at', $today);
                    })->count();

                    $modelUpdateCount = ModelUpdate::where('updated_by_id', $user->id)->where(function ($q) use ($today) {
                        $q->whereDate('created_at', $today)->orWhereDate('updated_at', $today);
                    })->count();

                    $activityLogCount = Activity::where('causer_id', $user->id)
                        ->where('causer_type', User::class)
                        ->where(function ($q) use ($today) {
                            $q->whereDate('created_at', $today)->orWhereDate('updated_at', $today);
                        })
                        ->count();

                    $jsonData = [
                        "approvals" => $approvalCount,
                        "leads" => $leadCount,
                        "contacts" => $contactCount,
                        "lead_notes" => $leadNoteCount,
                        "properties" => $propertyCount,
                        "property_notes" => $propertyNoteCount,
                        "listing_leads" => $listingLeadCount,
                        "listing_lead_notes" => $listingLeadNoteCount,
                        "events" => $eventCount,
                        "contact_media" => $contactMediaCount,
                        "lead_media" => $leadMediaCount,
                        "property_media" => $propertyMediaCount,
                        "contact_notes" => $contactNoteCount,
                        "model_update" => $modelUpdateCount,
                        "activity_logs" => $activityLogCount,
                    ];

                    UserActivityCount::create([
                        "user_id" => $user->id,
                        "reporting_date" => $today,
                        "report_generated_at" => now(),
                        "data" => json_encode($jsonData),
                        "count" => ($approvalCount + $leadCount + $contactCount + $leadNoteCount +
                            $propertyCount + $propertyNoteCount + $listingLeadCount + $listingLeadNoteCount +
                            $eventCount + $contactMediaCount + $leadMediaCount + $propertyMediaCount +
                            $contactNoteCount + $modelUpdateCount + $activityLogCount
                        )
                    ]);
                }
            });


        return Command::SUCCESS;
    }
}
