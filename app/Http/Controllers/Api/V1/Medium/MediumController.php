<?php

namespace App\Http\Controllers\Api\V1\Medium;


use App\Http\Controllers\Api\BaseController;
use App\Models\Medium;

class MediumController extends BaseController
{
    public function getLeadMediumList()
    {
        $data = Medium::where('is_lead_medium', '1')->where('status', '1')->get();
        $result = array('status' => '1', 'msg' => "Lead Medium fetched successfully.", 'data' => $data);
        return response()->json($result);
    }
}