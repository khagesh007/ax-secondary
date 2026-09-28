<?php

namespace App\Http\Controllers\Api\V1\Deals;


use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\V1\Lead\GetLeadListRequest;
use App\Http\Requests\Api\V1\Lead\GetLeadRequest;
use App\Http\Requests\Api\V1\Lead\LeadRequest;
use App\Http\Resources\CommissionTypeResource;
use App\Http\Tasks\Api\V1\Lead\LeadListTask;
use App\Http\Tasks\Api\V1\Lead\LeadShow;
use App\Models\CommissionType;
use App\Models\Lead;
use App\Models\LeadNote;
use App\Models\Medium;
use App\Models\Community;
use App\Models\SubCommunity;
use App\Models\CommunityProperty;
use App\Models\LeadCriteriaLocation;
use App\Models\LeadCriteria;
use App\Models\Property;
use App\Models\Source;
use App\Models\Type;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommissionTypeApiV1Controller extends BaseController
{

    public function __construct()
    {

    }

    public function index(){
        $commission_types = CommissionTypeResource::collection(CommissionType::where('status',1)->get());
        return $this->sendResponse(true, 'Commission Types', $commission_types, 200);
    }
    public function show($id, GetLeadRequest $request, LeadShow $task)
    {
        $response = $task->run($request);
        return $this->sendResponse($response['status'], $response['message'], $response['data'], $response['code']);
    }

    public function destroy(LeadRequest $request)
    {
        if (!auth()->user()->can('Leads Delete')) {
            return $this->sendResponse(false, 'You are not authorized to access this resource', [], 403);
        }
        try {
            if (Lead::where('id', $request->id)->doesntExist()) {
                return $this->sendResponse(false, 'Lead not found', null, 200);
            }

            $lead = Lead::find($request->id);
            if ($lead->delete()) {
                return $this->sendResponse(true, 'Lead deleted successfully', null, 200);
            } else {
                return $this->sendResponse(false, 'Lead not deleted', null, 400);
            }
        } catch (\Exception $e) {
            return $this->sendResponse(false, 'Lead not deleted', null, 500);
        }

    }

}
