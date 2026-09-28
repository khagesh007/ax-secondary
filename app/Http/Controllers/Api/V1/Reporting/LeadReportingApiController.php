<?php

namespace App\Http\Controllers\Api\V1\Reporting;

use App\Http\Controllers\Api\BaseController;
use App\Models\Lead;
use Carbon\Carbon;
use App\Models\Source;
use App\Models\Medium;
use App\Models\Type;
use App\Models\LeadStatus;
use App\Models\ListingLead;
use App\Models\ListingLeadStatus;

use Illuminate\Http\Request;

class LeadReportingApiController extends BaseController
{

    /**
     *
     *
     * Lead Reporting
     *
     * Get a Count of leads.
     * @group Reporting
     * @authenticated
     *
    * @header Accept application/json

     * @bodyParam group_by string Column name for result by Grouping of the leads Count. Example: source_id,lead_medium_id,lead_status_id,agent_id
     * @bodyParam key string Key is Name of any column you want to get count based on it. Example: [first_name, last_name,phone, mobile,whatsapp address,company,dob,gender]
     * @bodyParam key_value string Key_value is value of any colum you want to get count based on it. Example: John Doe
     * @bodyParam agent_id integer Agent Id for the search in Agents. Example: 1
     * @bodyParam bitrix_id integer Bitrix Id for the Search in Bitrix Ids. Example: 1
     * @bodyParam type string Lead Type is used for searching leads. Example: Tenent
     * @bodyParam source string Lead Source is used for searching leads. Example: Bayut
     * @bodyParam medium string Lead Medium is used for searching leads. Example: WhatsApp
     * @bodyParam is_archive boolean Is Archive for the Leads. Example: 1
     * @bodyParam lead_status string Lead Status for the user. Example: New
     * @bodyParam start_date date Start Date for the search. Example: 2021-01-01
     * @bodyParam end_date date End Date for the search. Example: 2021-01-01


     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"

     {
    "success": true,
    "message": "Lead Count Fetched",
    "data": {
        "breakdown": 23608,
        "total_count": 23608
    }

    {
    "success": true,
    "message": "Lead Count Fetched",
    "data": {
        "breakdown": [
            {
                "Name": "Agent External",
                "Count": 1
            },
            {
                "Name": "Bayut",
                "Count": 1
            },
            {
                "Name": "Dubizzle",
                "Count": 6
            },
            {
                "Name": "Open House",
                "Count": 1
            },
            {
                "Name": "Property Finder",
                "Count": 1
            },
            {
                "Name": "Referral",
                "Count": 1
            },
            {
                "Name": "Zoom Property",
                "Count": 1
            },
            {
                "Name": "Unspecified",
                "Count": 9
            },
            {
                "Name": "Houza",
                "Count": 1
            }
        ],
        "total_count": 22
    }
}
}


     */
    public function fetch(Request $request)
    {
        $agentId = $request->input('agent_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $groupBy = $request->input('group_by');
        $groupColumn = $request->input('column');

        $query = Lead::where('status', true);

        if ($request->input('active_agent')) {
            $query->whereHas('agent', function ($q) {
                $q->where('status', true);
            });
        }

        if ($request->filled('key') && $request->filled('key_value')) {
            $key = $request->input('key');
            $keyValue = $request->input('key_value');
            $query->where($key, 'like', '%' . $keyValue . '%');
        }

        if ($request->input('agent_id')) {
            $query->where('agent_id', $request->input('agent_id'));
        }

        if ($request->input('bitrix_id')) {
            $query->where('bitrix_id', $request->input('bitrix_id'));
        }

        if ($request->input('start_date') && $request->input('end_date')) {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->input('type')) {
            $typeId = Type::where('name','like', '%'.$request->input('type').'%')->first();
            $query->where('type_id', $typeId->id);
        }

        if ($request->input('source')) {
            $itemID = Source::where('name','like', '%'.$request->input('source').'%')->first();
            $query->where('source_id', $itemID->id);
        }

        if ($request->input('lead_status')) {
            $itemID = LeadStatus::where('name','like', '%'.$request->input('lead_status').'%')->first();
            $query->where('lead_status_id', $itemID->id);
        }


        if ($request->input('is_archive')) {
            $request->input('is_archive') == 'true' ? $query->where('is_archive', 1) : $query->where('is_archive', 0);
            // $query->where('is_archive', $request->input('is_archive'));
        }
        if ($request->input('medium')) {
            $itemID = Medium::where('name','like', '%'.$request->input('medium').'%')->first();
            $query->where('lead_medium_id', $itemID->id);
        }

        $total = $query->count();

        if ($groupBy && in_array($groupBy, ['source_id', 'type_id', 'created_by', 'updated_by', 'property_id', 'lead_medium_id', 'lead_status_id', 'agent_id'])) {
            $groupColumn = $this->getRelation($groupBy); // Get the name of the column to group by using the getRelation function
            $query->groupBy($groupBy);
            $leadsCount = $query->select($groupBy, \DB::raw('COUNT(*) as count'))->get();
            $leadsCount = $leadsCount->map(function ($item) use ($groupBy) {
                $relation = $this->getRelation($groupBy); // Get the relation using the getRelation function
                $name = $item->$relation->name ?? $item->$relation->title??''; // Get the name from the relation
                return ['Name' => $name, 'Count' => $item->count];
            });
        } else {
            $leadsCount = $query->count();
        }

        return $this->sendResponse(true, 'Lead Count Fetched', ['breakdown' => $leadsCount, 'total_count' => $total], 200);


        return response()->json([
            'success' => true,
            'message' => 'Lead Count Fetched',
            'data' => ['breakdown' => $leadsCount, 'total_count' => $total],
        ], 200);
    }

    private function getRelation($key)
    {
        $relation = [
            'source_id' => 'leadSource',
            'type_id' => 'leadType',
            'created_by' => 'createdBy',
            'updated_by' => 'updatedBy',
            'property_id' => 'property',
            'lead_medium_id' => 'leadMedium',
            'lead_status_id' => 'leadStatus',
            'agent_id' => 'agent',
        ];
        return $relation[$key] ?? null;
    }


    public function fetchListingLeads(Request $request)
    {
        $agentId = $request->input('agent_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $groupBy = $request->input('group_by');
        $groupColumn = $request->input('column');

        $query = ListingLead::where('status_id', true);

        if ($request->input('active_agent')) {
            $query->whereHas('agent', function ($q) {
                $q->where('status', true);
            });
        }

        if ($request->filled('key') && $request->filled('key_value')) {
            $key = $request->input('key');
            $keyValue = $request->input('key_value');
            $query->where($key, 'like', '%' . $keyValue . '%');
        }

        if ($request->input('agent_id')) {
            $query->where('agent_id', $request->input('agent_id'));
        }

        if ($request->input('status')) {
            $itemID = ListingLeadStatus::where('name','like', '%'.$request->input('status').'%')->first();
            $query->where('status_id', $itemID->id);
        }

        if ($request->input('property_category')) {
            $itemID = PropertyCategory::where('title','like', '%'.$request->input('property_category').'%')->first();
            $query->where('property_category_id', $itemID->id);
        }


        if ($request->input('type')) {
            $typeId = Type::where('name','like', '%'.$request->input('type').'%')->first();
            $query->where('type_id', $typeId->id);
        }

        if ($request->input('source')) {
            $itemID = Source::where('name','like', '%'.$request->input('source').'%')->first();
            $query->where('source_id', $itemID->id);
        }

        if ($request->input('medium')) {
            $itemID = Medium::where('name','like', '%'.$request->input('medium').'%')->first();
            $query->where('lead_medium_id', $itemID->id);
        }

        $total = $query->count();

        if ($groupBy && in_array($groupBy, ['source_id', 'type_id', 'created_by', 'updated_by', 'property_id', 'agent_id'])) {
            $groupColumn = $this->getRelationForListingLeads($groupBy); // Get the name of the column to group by using the getRelationForListingLeads function
            $query->groupBy($groupBy);
            $leadsCount = $query->select($groupBy, \DB::raw('COUNT(*) as count'))->get();
            $leadsCount = $leadsCount->map(function ($item) use ($groupBy) {
                $relation = $this->getRelationForListingLeads($groupBy); // Get the relation using the getRelation function
                $name = $item->$relation->name ?? $item->$relation->title??''; // Get the name from the relation
                return ['Name' => $name, 'Count' => $item->count];
            });
        } else {
            $leadsCount = $query->count();
        }

        return $this->sendResponse(true, 'Listing Lead Count Fetched', ['breakdown' => $leadsCount, 'total_count' => $total], 200);


        return response()->json([
            'success' => true,
            'message' => 'Lead Count Fetched',
            'data' => ['breakdown' => $leadsCount, 'total_count' => $total],
        ], 200);
    }


    private function getRelationForListingLeads($key)
    {
        $relation = [
            'source_id' => 'source',
            'medium_id' => 'medium',
            'type_id' => 'type',
            'created_by' => 'createdBy',
            'updated_by' => 'updatedBy',
        ];
        return $relation[$key] ?? null;
    }
}
