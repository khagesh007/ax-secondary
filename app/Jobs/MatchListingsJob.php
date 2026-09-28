<?php

namespace App\Jobs;

use App\Mail\SendMatchedListings;
use Illuminate\Bus\Queueable;
use App\Models\User;
use App\Traits\TelegramTrait;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MatchListingsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TelegramTrait;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public $user, public $properties)
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
        Log::info('MatchListingsJob for '. $this->user->name);
        if ($this->user && $this->user->email && $this->properties && count($this->properties) > 0) {
            Mail::to($this->user->email)->send(new SendMatchedListings($this->properties, $this->user));
           // Mail::to("hishmat@axcapital.ae")->send(new SendMatchedListings($this->properties, $this->user));
        }
    }
}
