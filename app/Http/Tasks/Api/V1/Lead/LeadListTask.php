<?php

namespace App\Http\Tasks\Api\V1\Lead;

use App\Http\Requests\Api\V1\Lead\GetLeadListRequest;
use App\Http\Resources\LeadResource;
use App\Http\Tasks\Api\BaseTask;
use App\Models\Lead;

class LeadListTask extends BaseTask
{
    public function run(GetLeadListRequest $request)
    {
        // $user = $this->authUser();

        $query = Lead::query();

        $query = $query->where('status', 1);

        if ($request->agent_id) {
            $query = $query->where('agent_id', $request->agent_id);
        }

        if ($request->lead_status_id) {
            $query = $query->where('lead_status_id', $request->lead_status_id);
        }

        if ($request->medium_id) {
            $query = $query->where('lead_medium_id', $request->medium_id);
        }

        if ($request->source_id) {
            $query = $query->where('source_id', $request->source_id);
        }

        if ($request->type_id) {
            $query = $query->where('type_id', $request->type_id);
        }
        if ($request->region_id) {
            $query = $query->where('region_id', $request->region_id);
        }

        if ($request->input('created_at')) {
            $value = is_array($value) ? $value : explode(' to ', $value);
            if (count($value) === 1) {
                $startOfDay = date('Y-m-d', strtotime($value[0])) . ' 00:00:00';
                $endOfDay = date('Y-m-d', strtotime($value[0])) . ' 23:59:59';
                $query =  $query->where('created_at', '>=', $startOfDay)->where('created_at', '<=', $endOfDay);

            } else {
                $startOfDay = date('Y-m-d', strtotime($value[0])) . ' 00:00:00';
                $endOfDay = date('Y-m-d', strtotime($value[1])) . ' 23:59:59';
                $query =  $query->where('created_at', '>=', $startOfDay)->where('created_at', '<=', $endOfDay);
            }
        }

        if ($request->input('updated_at')) {
            $value = is_array($value) ? $value : explode(' to ', $value);
            if (count($value) === 1) {
                $startOfDay = date('Y-m-d', strtotime($value[0])) . ' 00:00:00';
                $endOfDay = date('Y-m-d', strtotime($value[0])) . ' 23:59:59';
                $query =  $query->where('updated_at', '>=', $startOfDay)->where('updated_at', '<=', $endOfDay);

            } else {
                $startOfDay = date('Y-m-d', strtotime($value[0])) . ' 00:00:00';
                $endOfDay = date('Y-m-d', strtotime($value[1])) . ' 23:59:59';
                $query =  $query->where('updated_at', '>=', $startOfDay)->where('updated_at', '<=', $endOfDay);
            }
        }

        $query->orderBy('id', 'desc');
        $query->when(!empty($request->search), function ($q) use ($request) {
            return $q->where('first_name', 'like', '%' . $request->search . '%')
                ->orWhere('last_name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('mobile', 'like', '%' . $request->search . '%');
        });
        $query->with(['criteria', 'criteria.locations.parentable', 'leadAgents.agent:id,first_name,last_name,email,mobile,image']);
        $list_per_page = '10';
        $lead_count = $query->count();
        $leads = $query->paginate($list_per_page);

        if ($lead_count > 0) {
            $leads->data = LeadResource::collection($leads);
            $leads->appends($request->query())->links();
        } else {
            return $this->sendResponse(false, 'No Leads found', [], 200);
        }

        return $this->sendResponse(true, 'Lead list', compact('leads'), 200);
    }
}
