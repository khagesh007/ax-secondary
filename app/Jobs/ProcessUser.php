<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

use App\Models\User;

use App\Events\UserCreated;

use App\Traits\FileTrait;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Str;

class ProcessUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, FileTrait;

    public $bitrixId;
    public $password;
    public $managerBitrixId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->bitrixId = $data['bitrix_id'];
        $this->password = $data['password'];
        $this->managerBitrixId = $data['manager_bitrix_id'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try
        {
            $response = Http::timeout(10)->get('https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/user.get?id='.$this->bitrixId);
            $data = json_decode($response, true);
            $userData = $data['result'][0];
            $user = User::where('email', $userData['EMAIL'])->first();
            if(!$user){
                $obj = new User();
                $obj->bitrix_id = $userData['ID'];
                $obj->telegram_chatid = $userData['UF_SKYPE'];
                $obj->first_name = $userData['NAME'];
                $obj->last_name = $userData['LAST_NAME'];
                $obj->name = $userData['NAME']." ".$userData['LAST_NAME'];
                $obj->email = $userData['EMAIL'];
                $obj->mobile = $userData['PERSONAL_MOBILE'];
                $obj->position = $userData['PERSONAL_PROFESSION'];
                if($this->password ?? ''){
                    $obj->password = Hash::make($this->password);
                }
                $manager = User::where('bitrix_id', $this->managerBitrixId)->first();

                $obj->parent_id = $manager->id ?? null;
                $obj->zone_id = $manager->zone_id ?? null;
                $obj->status = 1;

                if($userData['PERSONAL_PHOTO']){
                    $uploadedImage = $this->uploadImageAndThumbFromUrl($userData['PERSONAL_PHOTO'], "avatar", "s3");
                    if($uploadedImage){
                        $obj->image = $uploadedImage['image'];
                        $obj->thumbnail = $uploadedImage['thumbnail'];
                    }
                }
                $obj->created_at = now();
                $obj->updated_at = now();
                $obj->save();

                $managerRoles = $manager->roles ?? [];

                $primaryRolesToCheck = ['Primary Sales Director', 'Primary Sales Manager'];

                $hasPrimaryRoles = $managerRoles->whereIn('name', $primaryRolesToCheck)->isNotEmpty();

                $secondaryRolesToCheck = ['Sales Director', 'Line Manager', 'Team Leader'];

                $hasSecondaryRoles = $managerRoles->whereIn('name', $secondaryRolesToCheck)->isNotEmpty();

                if ($hasPrimaryRoles) {
                    $obj->assignRole('Primary Agent');
                } elseif ($hasSecondaryRoles) {
                    $obj->assignRole('Agent');
                }

                if(env('APP_ENV')=='production'){
                    event(new UserCreated($obj));
                }
            }

        } catch (\Throwable $th) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => "Process User Cron Job " . $th->getMessage());
            info($result);
        }
    }
}
