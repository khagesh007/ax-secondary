<?php

namespace App\Http\Controllers\Admin\Configuration;

use App\Http\Controllers\Controller;
use App\Models\EmailSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class EmailSettingController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies("Email Settings List"), 401);
        $roles = Role::get();
        return view("admin.configuration.emailSettings.index", compact("roles"));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "title" => "required|string",
            "cc" => "nullable|string",
            "bcc" => "nullable|string",
            "reply_to" => "nullable|string",
            "status" => "nullable",
            "roles" => "nullable|array",
            "roles.*" => "nullable|exists:roles,id",
        ]);

        try {
            $validatedData["cc"] = $request->cc ? explode(",", $request->cc) : null;
            $validatedData["bcc"] = $request->bcc ? explode(",", $request->bcc) : null;
            $validatedData["status"] = $request->boolean("status");
            $emailSetting = EmailSetting::create($validatedData);
            $emailSetting->roles()->sync($request->roles ?? []);
            $result = array('status' => '1', 'msg' => 'Email setting added successfully');
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
            3 => 'cc',
            4 => 'bcc',
            5 => 'reply_to',
            6 => 'status',
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = EmailSetting::count();
        $totalFiltered = $totalData;

      


        if (!empty($searchKeyWord)) {
            $data_filtered = EmailSetting::where(function ($q) use ($searchKeyWord) {
                $q->where('title', 'LIKE', '%' . $searchKeyWord . '%');
            });

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = EmailSetting::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"]);
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

    public function getSetting(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:email_settings,id',
        ]);
        try {
            $cat = EmailSetting::find($request->id);
            $cat->load(["roles"]);
            $result = array('status' => '1', 'msg' => 'Email setting details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:email_settings,id",
            "title" => "required|string",
            "cc" => "nullable|string",
            "bcc" => "nullable|string",
            "reply_to" => "nullable|string",
            "status" => "nullable",
            "roles" => "nullable|array",
            "roles.*" => "nullable|exists:roles,id",
        ]);

        try {
            $validatedData["cc"] = $request->cc ? explode(",", $request->cc) : null;
            $validatedData["bcc"] = $request->bcc ? explode(",", $request->bcc) : null;
            $validatedData["status"] = $request->boolean("status");
            $setting = EmailSetting::find($request->id);
            $setting->update($validatedData);
            $setting->roles()->sync($request->roles ?? []);
            $result = array('status' => '1', 'msg' => 'Email setting updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:email_settings',
            'action' => 'required',
        ]);

        $type = EmailSetting::find($request->id);
        $type->status = $request->boolean('action');
        if ($type->save()) {
            $result = array('status' => '1', 'msg' => 'Email setting status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:email_settings',

        ]);

        $type = EmailSetting::find($request->id);
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
