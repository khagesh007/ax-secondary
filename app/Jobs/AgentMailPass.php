<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\SendUserCredential;
use Mail;
use App\Models\User;
use Illuminate\Support\Str;

class AgentMailPass implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info($this->user);

        $password = Str::random(12);
        $obj = User::find($this->user->id);
        $obj->password = \Hash::make($password);
        $obj->save();

        $data = ['name'=>$this->user->name,
        'password' => $password,
        'email' => $this->user->email,
    ];
        Mail::to($this->user->email)->send(new SendUserCredential($data));
   
       
    }
}
