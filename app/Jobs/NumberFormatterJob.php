<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Contact;
use App\Models\Lead;

class NumberFormatterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $contacts = Lead::where('status', true)->get();
        // foreach ($contacts as $contact) {
        //     $contact->old_mobile = $contact->mobile;
        //     $old = $contact->mobile;
        //     $old_status = checkformat($contact->mobile);
        //     $contact->mobile = validatePhoneNumber($contact->mobile);
        //     info('Old: '.$old.'------------'.$old_status.'----------- New: '.$contact->mobile.'-----------'.checkformat($contact->mobile));
        //     $contact->save();
        //     sleep(0.1);
        // }

    }
}
