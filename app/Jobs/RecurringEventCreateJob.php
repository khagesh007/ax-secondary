<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecurringEventCreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     * $request, $dateTime, $end_date, $parent_event
     */
    public $request;
    public $dateTime;
    public $end_date;
    public $parent_event;
    public function __construct($request, $dateTime, $end_date, $parent_event)
    {
        $this->request = $request;
        $this->dateTime = $dateTime;
        $this->end_date = $end_date;
        $this->parent_event = $parent_event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $end_time = date('H:i', strtotime($this->parent_event->end_time));

        $event = new Event();
        $event->agent_id = $this->request['agent_id'];
        $event->contact_id = $this->request['contact_id'] ?? null;
        $event->lead_id =  $this->request['lead_id'] ?? null;
        $event->property_id =  $this->request['property_id'] ?? null;
        $event->listing_lead_id = $this->request['listing_lead_id'] ?? null;
        $event->start_time = $this->dateTime;
        $event->end_time = Carbon::parse($this->dateTime)->format('Y-m-d').' '.$end_time;
        $event->event_type_id =  $this->request['event_type_id'] ?? null;
        $event->priority =  $this->request['priority'] ?? null;
        $event->title =  $this->request['title'];
        $event->location =  $this->request['location'];
        $event->description =  $this->request['description'];
        $event->created_by_id = $this?->parent_event?->created_by_id;
        $event->reminder =  $this->request['reminder'];
        $event->send_notification = false;
        $event->reminder_frequency =  $this->request['reminder_frequency'];
        $event->reminder_to_attendees = false;
        $event->save();

        if(isset( $this->request['all_attendees']) &&  $this->request['all_attendees'] == 'on'){
            $all_user = User::where('status','1')->pluck('id');
            $event->attendees()->sync($all_user ?? []);
        }elseif(isset( $this->request['all_team_attendees']) &&  $this->request['all_team_attendees'] == 'on'){
            $childrenIds = getUserChildrenIdsOnly(auth()->user()->id);
            $event->attendees()->sync($childrenIds ?? []);
        }else{
            $event->attendees()->sync( $this->request['attendees'] ?? []);
        }
    }
}
