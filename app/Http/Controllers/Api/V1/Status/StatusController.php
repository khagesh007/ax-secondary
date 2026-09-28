<?php

namespace App\Http\Controllers\Api\V1\Status;


use App\Http\Controllers\Api\BaseController;
use App\Models\LeadStatus;

class StatusController extends BaseController
{
    public function getLeadStatuses()
    {
        $statuses = LeadStatus::where('id', '!=', 3)->where('status', true)->orderBy('order', 'asc')->get();
        return $this->sendResponse(200, 'Data found', $statuses, 200);

    }
}