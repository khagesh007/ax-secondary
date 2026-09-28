<?php

namespace App\Console\Commands;
use App\Models\CallLog;
use Illuminate\Console\Command;
use App\Traits\FileTrait;

class UploadRecordings extends Command
{
    use FileTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:Recordings';

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
        $time_start = microtime(true);

        $callLogs = CallLog::whereNotNull("call_recording_url")->whereNull("call_recording_sid")->limit(200)->get();
        foreach($callLogs ?? [] as $callLog){
            echo "#";
            $old_url = $callLog->call_recording_url;
            $recording_url = $this->uploadDocumentFromUrl(url: $callLog->call_recording_url, path: "callLogs");
            if($recording_url){
              $callLog->call_recording_url = $recording_url;
                $callLog->call_recording_sid = $old_url;
                $callLog->save();  
            }else{
                 $callLog->call_recording_sid = "Not Uploaded";
                 $callLog->save();
            }
            
            
        }
            
        
        $time_end = microtime(true);
        $time = $time_end - $time_start;
        $time = gmdate("H:i:s", $time);

        echo( "$time minutes it takes to upload all the recordings");

        return Command::SUCCESS;
    }
}
