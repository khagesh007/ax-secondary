<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Traits\FileTrait;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Events\UserCreated;
use App\Events\UserUpdated;
use Log;

class UploadUserData extends Command
{
    use FileTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uploadUser:data';

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
        $response = Http::get('https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/user.get?active=true&sort=ID');
        $res = $response->json();
        $total = $res['total'];
        for ($i = 0; $i <= $total; $i += 50) {
            $response = Http::get('https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/user.get?active=true&sort=ID&start=' . $i);
            $res = $response->json();
            echo "\nstart is " . $i;
            foreach ($res['result'] as $val) {
                try {
                    $user = User::withTrashed()->where('bitrix_id', $val['ID'])->where('email', $val['EMAIL'])->first();
                    if (!$user) {
                        $user = User::withTrashed()->where('bitrix_id', $val['ID'])->first();
                    }
                    if (!$user) {
                        $user = User::withTrashed()->where('email', $val['EMAIL'])->first();
                    }
                    if (!$user) {
                        $obj = new User();
                        $obj->bitrix_id = $val['ID'];
                        $obj->telegram_chatid = $val['UF_SKYPE'] ?? null;
                        $obj->first_name = $val['NAME'];
                        $obj->last_name = $val['LAST_NAME'];
                        $obj->name = $val['NAME'] . " " . $val['LAST_NAME'];
                        $obj->email = $val['EMAIL'];
                        $obj->mobile = $val['PERSONAL_MOBILE'];
                        $obj->position = $val['PERSONAL_PROFESSION'];
                        $obj->password = Hash::make(Str::random(10));
                        $obj->status = 0;

                        if (isset($val['PERSONAL_PHOTO']) && $val['PERSONAL_PHOTO']) {
                            $uploadedImage = $this->uploadImageAndThumbFromUrl($val['PERSONAL_PHOTO'], "avatar", "s3");
                            if ($uploadedImage) {
                                $obj->image = $uploadedImage['image'];
                                $obj->thumbnail = $uploadedImage['thumbnail'];
                            }
                        }
                        $obj->created_at = now();
                        $obj->updated_at = now();
                        $obj->save();

                        // if(env('APP_ENV')=='production'){
                        //     event(new UserCreated($obj));
                        // }

                        if ($val['UF_DEPARTMENT'][0] == 19216) {
                            $obj->syncRoles([8]);
                        }
                    } else {
                        $updated = false;
                        if (!$user->bitrix_id) {
                            $user->bitrix_id = $val['ID'];
                            $updated = true;
                        }
                        if (!$user->telegram_chatid &&  (isset($val['UF_SKYPE']) && $val['UF_SKYPE'])) {
                            if (!$user->email != "c.habib@axcapital.ae") {
                                $user->telegram_chatid = $val['UF_SKYPE'];
                                $updated = true;
                            }
                        }
                        if (!$user->mobile && !$user->email != "c.habib@axcapital.ae") {
                            $user->mobile = $val['PERSONAL_MOBILE'];
                            $updated = true;
                        }
                        if ($user->email != $val['EMAIL']) {
                            $user->email = $val['EMAIL'];
                            $updated = true;
                        }

                        if ($user->image == null) {
                            if (isset($val['PERSONAL_PHOTO']) && $val['PERSONAL_PHOTO']) {
                                $uploadedImage = $this->uploadImageAndThumbFromUrl($val['PERSONAL_PHOTO'], "avatar", "s3");
                                if ($uploadedImage) {
                                    $user->image = $uploadedImage['image'];
                                    $user->thumbnail = $uploadedImage['thumbnail'];
                                    $updated = true;
                                }
                            }
                        }
                        if ($updated) {
                            $user->updated_at = now();
                            $user->save();
                        }

                        // if(env('APP_ENV')=='production'){
                        //     event(new UserUpdated($user));
                        // }

                    }
                } catch (\Throwable $th) {
                    continue;
                    //throw $th;
                }
            }
        }

        $allUsers = User::where('status', 1)->get();

        foreach ($allUsers as $user) {
            if ($user->email == "anastasia_agent@axcapital.ae") {
                continue;
            }
            $response = Http::timeout(10)->get('https://crm.axcap.ae/rest/1/8cd3l0kbdlndvtqf/user.get?email=' . $user->email);
            $res = $response->json();
            echo "Email is " . $user->email . "\n";
            $result = $res['result'][0] ?? [];
            if ($result) {
                if ($result['ACTIVE'] == false) {
                    echo "User deactivated $user->email\n";
                    $user->status = $result['ACTIVE'];
                    $user->save();
                }
                $user->save();
            }
        }

        $url = 'https://crm.axcap.ae/company-info/js/orgchart.json';
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: BITRIX_SM_SALE_UID=0; PHPSESSID=hjkkOXcPrgiRmKbZvtr76dtfkwr1kzJx',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode($response);
        $counter = 0;
        foreach ($response as $user) {
            $user = (array) $user;
            $bitrix_email = $user['Email'];
            $find_user = \App\Models\User::where('email', $bitrix_email)->first();
            if ($find_user) {
                if ($find_user->bitrix_role != $user['Department']) {
                    $find_user->bitrix_role = $user['Department'];
                    $find_user->save();
                    $counter++;
                }
            }
        }
        Log::channel('discord')->info("Bitrix Role  updated of " . $counter . ' users.');
    }
}
