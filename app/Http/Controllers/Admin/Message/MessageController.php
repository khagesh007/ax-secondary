<?php

namespace App\Http\Controllers\Admin\Message;

use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class MessageController extends Controller
{
    public function inbox()
    {
        //abort_if(Gate::denies("Message Inbox"), 401);
        $data['lists'] = NotificationUser::where('user_id',auth()->user()->id)->orderBy('id','DESC')->with('notification')->limit('100')->get();

        return view('admin.message.inbox')->with($data);
    }
    public function paginateNotification(Request $request)
    {

        $data = NotificationUser::where('user_id',auth()->user()->id)->orderBy('id','DESC')->with('notification')->simplePaginate('50');
       if(count($data)){
           return response()->json(["status"=>"1","msg"=>"Notification fetched successfully","data"=>$data]);
       }else{
        return response()->json(["status"=>"0","msg"=>"No more notification to fetch"]);
       }


    }
    public function getNotificationByNotiUser(Request $request){
        $request->validate([
            'id' => 'required',

        ]);
        $notification = NotificationUser::where('id',$request->id)->with('notification')->first();
        if ($notification) {
            $notification->read = 1;
            $notification->save();
            $result = array('status' => '1', 'msg' => 'Notification fetched successfully','data'=>$notification);
        } else {
            $result = array('status' => '0', 'msg' => 'Data not found !!');
        }


        return json_encode($result);
    }
    public function markAsRead(Request $request){
       $notification_ids = $request->notification ?? [];
       if(is_array($notification_ids) && !empty($notification_ids)){
            if(NotificationUser::whereIn('id',$notification_ids)->where('user_id',auth()->user()->id)->update(['read'=>'1'])){
                $result = array('status' => '1', 'msg' => 'Notification marked as read successfully');
            }else{
                $result = array('status' => '0', 'msg' => 'Unable to perform action!!');
            }
        }else{
           $result = array('status' => '0', 'msg' => 'You have not selected any notification!!');
       }
        return json_encode($result);
    }
    public function markAllAsRead(Request $request){


            if(NotificationUser::where('user_id',auth()->user()->id)->update(['read'=>'1'])){
                $result = array('status' => '1', 'msg' => 'All notification marked as read successfully');
            }else{
                $result = array('status' => '0', 'msg' => 'Unable to perform action!!');
            }

        return json_encode($result);
    }
    public function markAsUnRead(Request $request){
       $notification_ids = $request->notification;
        if(is_array($notification_ids) && !empty($notification_ids)){
            if(NotificationUser::whereIn('id',$notification_ids)->where('user_id',auth()->user()->id)->update(['read'=>'0'])){
                $result = array('status' => '1', 'msg' => 'Notification marked as un-read successfully');
            }else{
                $result = array('status' => '0', 'msg' => 'Unable to perform action!!');
            }
        }else{
            $result = array('status' => '0', 'msg' => 'You have not selected any notification!!');
        }
        return json_encode($result);
    }
    public function readNotification(Request $request){
        $data = NotificationUser::where('id',$request->id)->where('read',0)->update(['read'=>1]);
        return response()->json(['status'=> '1', 'data'=>$data]);
    }

}
