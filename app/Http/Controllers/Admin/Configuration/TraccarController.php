<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Models\TraccarClientSetting;
use App\Models\User;
use App\Traits\FirebaseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class TraccarController extends Controller
{
    use FirebaseTrait;
    public function client_settings()
    {

        $record= TraccarClientSetting::find(1);
        return view("admin.configuration.traccar.client_settings", compact("record"));
    }


    public function update_client_settings(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|integer",
            "server_url" => "required|url",
            "port" => "nullable|numeric",
            "location_accuracy" => "nullable|string",
            "distance" => "nullable|numeric|min:0",
            //"angle" => "nullable|numeric|min:0",
            "angle" => "nullable",
            "frequency" => "nullable|numeric|min:0",
            "track_from" => "nullable",
            "track_to" => "nullable",
            'password' => 'nullable|string',
            'username' => 'nullable|string',
            'coordinates_accuracy' => 'nullable|numeric|min:0',
        ]);


        try {
            $store = TraccarClientSetting::updateOrCreate(['id'=>$validatedData['id']],$validatedData);
            $store->updated_by_id = auth()->user()->id;

            $store->offline_buffering = $request->boolean("offline_buffering") ? 1: 0;
            $store->status = $request->boolean("status") ? 1: 0;
            $store->track_from = $request->track_from ? date("H:i:s",strtotime($request->track_from)) : null;
            $store->track_to = $request->track_to ? date("H:i:s",strtotime($request->track_to)) : null;
            if($store->save()){

                if($store->wasChanged('offline_buffering') || $store->wasChanged('status') || $store->wasChanged('track_from') || $store->wasChanged('track_to') || $store->wasChanged('location_accuracy') || $store->wasChanged('distance') || $store->wasChanged('angle') || $store->wasChanged('frequency') || $store->wasChanged('server_url') || $store->wasChanged('port') || $store->wasChanged('coordinates_accuracy') ){
                    $data = array(
                        'flag' => 'traccar_client_setting_updated',
                    );
                    $res = $this->traccarGloablPush($data);
                    $store->version =  $store->version + 0.1;
                    $store->save();
                }

            }



            $result = array('status' => '1', 'msg' => 'Setting saved successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }
}
