<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Models\EmailSetting;
use App\Models\NotificationEvent;
use App\Models\NotificationEventSubscriber;
use App\Models\NotificationUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class NotificationController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies("Email Settings List"), 401);
        $roles = Role::get();
        $userAgents = [];
        $allAgents = User::whereHas("roles", fn($q) => $q->whereIn("name",["Sales Director","Line Manager", "Team Leader"]))->with("roles:id,name")->get(["id", "name", "email"]);
        //dd($allAgents);
        foreach ($allAgents as $agent){
            if (!empty($agent->roles[0]->name)){
                $userAgents[$agent->roles[0]->name][] = [
                    "id" => $agent->id,
                    "name" => $agent->name,
                ];
            }
        }
        return view("admin.configuration.notification.setting", compact("roles","userAgents"));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "title" => "required|string",
            "medium" => "required|array",
            "event" => "required|string",
            "roles" => "nullable|array",
            "parent" => "nullable|array",
            "child_account" => "nullable|array",
            "individuals" => "nullable|array",
            "excluded_individuals" => "nullable|array",
            "status" => "nullable",
            "roles" => "nullable|array",
            "roles.*" => "nullable|exists:roles,id",
        ]);

        try {
            $individuals = $request->individuals ?? [];
            $child_account = $request->child_account ?? [];
            $parent = $request->parent ?? [];
            $users = array_merge($individuals, $child_account);
            $users = array_merge($users, $parent);
            $excluded = $request->excluded_individuals ?? [];


            if(NotificationEvent::where('class_name',$request->event)->where('status','1')->exists()){
                $result = array('status' => '0', 'msg' => 'Event notification already exists');
                return json_encode($result);
            }
            $obj = new NotificationEvent();
            $obj->title = $request->title;
            $obj->medium = json_encode($request->medium);
            $obj->class_name = $request->event;
            $obj->roles = json_encode($request->roles);
            $obj->excluded = json_encode($request->excluded_individuals);
            $obj->status = $request->boolean("status") ?? 0;
            if($obj->save()){
                $users = array_unique($users);
                foreach ($users as $user) {
                    if(!in_array($user,$excluded)) {
                        $subscribe = new NotificationEventSubscriber();
                        $subscribe->notification_event_id = $obj->id;
                        $subscribe->user_id = $user;
                        $subscribe->save();
                    }
                }
            }


            $result = array('status' => '1', 'msg' => 'Notification setting added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function settingsForDatatable(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'id',
            2 => 'title',

        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = NotificationEvent::count();
        $totalFiltered = $totalData;

       
        if (!empty($searchKeyWord)) {
            $data_filtered = NotificationEvent::where(function ($q) use ($searchKeyWord) {
                $q->where('title', 'LIKE', '%' . $searchKeyWord . '%');
            });

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = NotificationEvent::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"]);
            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->offset($request['start'])->get();
        }

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);
    }

    public function getNotificationEvent(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:notification_events,id',
        ]);
        try {
            $data['event'] = NotificationEvent::find($request->id);

            $excluded = json_decode($data['event']->excluded) ?? [];
            $data['excluded'] = User::whereIn('id',$excluded)->pluck('name','id');
            $users = NotificationEventSubscriber::where('notification_event_id',$request->id)->pluck('user_id');
            $data['users'] = User::whereIn('id',$users)->pluck('name','id');
            $result = array('status' => '1', 'msg' => 'Notification setting details fetched successfully', 'data' => $data);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:notification_events,id",
            "title" => "required|string",
            "medium" => "required|array",
            "event" => "required|string",
            "roles" => "nullable|array",
            "parent" => "nullable|array",
            "child_account" => "nullable|array",
            "individuals" => "nullable|array",
            "excluded_individuals" => "nullable|array",
            "status" => "nullable",
            "roles" => "nullable|array",
            "roles.*" => "nullable|exists:roles,id",
        ]);

        try {
            $individuals = $request->individuals ?? [];
            $child_account = $request->child_account ?? [];
            $parent = $request->parent ?? [];
            $users = array_merge($individuals, $child_account);
            $users = array_merge($users, $parent);
            $excluded = $request->excluded_individuals ?? [];


            $obj = NotificationEvent::find($request->id);
            $obj->title = $request->title;
            $obj->medium = json_encode($request->medium);
            $obj->class_name = $request->event;
            $obj->roles = json_encode($request->roles);
            $obj->excluded = json_encode($request->excluded_individuals);
            $obj->status = $request->boolean("status") ?? 0;
            if($obj->save()){
                $users = array_unique($users);
                NotificationEventSubscriber::where('notification_event_id',$obj->id)->delete();
                foreach ($users as $user) {
                    if(!in_array($user,$excluded)) {
                        $subscribe = new NotificationEventSubscriber();
                        $subscribe->notification_event_id = $obj->id;
                        $subscribe->user_id = $user;
                        $subscribe->save();
                    }
                }
            }
            $result = array('status' => '1', 'msg' => 'Email setting updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:notification_events',
            'action' => 'required',
        ]);

        $type = NotificationEvent::find($request->id);
        $type->status = $request->boolean('action');
        if ($type->save()) {
            $result = array('status' => '1', 'msg' => 'Notification setting status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:notification_events',

        ]);
        NotificationEventSubscriber::where('notification_event_id',$request->id)->delete();
        $type = NotificationEvent::find($request->id);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => 'Email setting deleted successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',

        ]);
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            if (EmailSetting::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Email settings deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }

        return json_encode($result);
    }

}
