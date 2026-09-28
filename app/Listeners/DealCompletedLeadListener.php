<?php

namespace App\Listeners;

use App\Models\Contact;
use App\Models\DealLead;
use App\Models\Lead;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DealCompletedLeadListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $deal_lead = DealLead::where('deal_id', $event->deal->id)->where('lead_type','Primary')->first();

        if($event->deal->type == 'Secondary Sale' || $event->deal->type == 'Primary Resale'){
    $lead = Lead::where('id', $deal_lead->lead_id)->first();
    //convert lead to contact now
            $contact = new Contact();
            $contact->first_name = $lead->first_name;
            $contact->last_name = $lead->last_name;
            $contact->gender = $lead->gender;
            $contact->mobile = $lead->mobile;
            $contact->email = $lead->email;
            $contact->phone = $lead->phone;
            $contact->address = $lead->address;
            $contact->company = $lead->company;
            $contact->dob = $lead->dob;
            $contact->country_id = $lead->country_id;
            $contact->nationality_id = $lead->nationality_id;
            $contact->region_id = $lead->region_id;
            if($contact->save()){
                $note = new \App\Models\ContactNote();
                $note->contact_id = $contact->id;
                $note->note = 'Lead converted to contact
                From deal '.$event->deal->id.' with lead id '.$lead->id;
                $note->save();
            }
        }
    }
}
