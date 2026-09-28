<?php

namespace App\Http\Tasks\Api\V1\ListingLead;

use App\Http\Requests\Api\V1\ListingLead\GetListingLeadRequest;
use App\Http\Resources\ListingLeadResource;
use App\Http\Tasks\Api\BaseTask;
use App\Models\ListingLead;

class SearchListingLeadTask extends BaseTask
{
    private function isArray($key)
    {
        return gettype(\request($key)) == 'array' && count(\request($key)) > 0;
    }
    public function run(GetListingLeadRequest $request)
    {
        $superAdmin = auth()->user()->hasRole(['Super Admin']);
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        if ($request->all_listing) {
            $all_listings = 'true';
        } else {
            $all_listings = 'false';
        }

        $query = ListingLead::query();

        if (!$request->inactive_agents) {
            $query = $query->where(function ($q) {
                $q->whereNull('agent_id')->orWhereHas('agent', function ($q) {
                    $q->where('status', 1);
                });
            });
        }

        $query = $query->when($notAdmin , function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        });

        if ($request->agent_id) {
            $value = $this->isArray("agent_id") ? [] : [$request->input('agent_id')];
            if ($this->isArray("agent_id")){
                foreach ($request->input('agent_id') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("agent_id", $value);
            }
        }

        if ($request->bed_id) {
            $value = $this->isArray("bed_id") ? [] : [$request->input('bed_id')];
            if ($this->isArray("bed_id")){
                foreach ($request->input('bed_id') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("bed_id", $value);
            }
        }
        if ($request->bath_id) {
            $value = $this->isArray("bath_id") ? [] : [$request->input('bath_id')];
            if ($this->isArray("bath_id")){
                foreach ($request->input('bath_id') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("bath_id", $value);
            }
        }

        if ($request->status_id) {
            $value = $this->isArray("status_id") ? [] : [$request->input('status_id')];
            if ($this->isArray("status_id")){
                foreach ($request->input('status_id') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("status_id", $value);
            }
        }

        if ($request->medium_id) {
            $value = $this->isArray("medium_id") ? [] : [$request->input('medium_id')];
            if ($this->isArray("medium_id")){
                foreach ($request->input('medium_id') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("medium_id", $value);
            }
        }

        if ($request->source_id) {
            $value = $this->isArray("source_id") ? [] : [$request->input('source_id')];
            if ($this->isArray("source_id")){
                foreach ($request->input('source_id') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("source_id", $value);
            }
        }

        if ($request->type_id) {
            $value = $this->isArray("type_id") ? [] : [$request->input('type_id')];
            if ($this->isArray("type_id")){
                foreach ($request->input('type_id') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("type_id", $value);
            }
        }
        if ($request->communities) {
            $value = $this->isArray("communities") ? [] : [$request->input('communities')];
            if ($this->isArray("communities")){
                foreach ($request->input('communities') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("community_id", $value);
            }
        }
        if ($request->sub_communities) {
            $value = $this->isArray("sub_communities") ? [] : [$request->input('sub_communities')];
            if ($this->isArray("sub_communities")){
                foreach ($request->input('sub_communities') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("sub_community_id", $value);
            }
        }
        if ($request->community_properties) {
            $value = $this->isArray("community_properties") ? [] : [$request->input('community_properties')];
            if ($this->isArray("community_properties")){
                foreach ($request->input('community_properties') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("community_property_id", $value);
            }
        }

        if ($request->is_archive) {
            $query = $query->where('is_archive', 1);
        } else {
            $query = $query->whereIn("is_archive", [0, 1]);
        }

        if ($request->input('created_at')) {
            $value = is_array($value) ? $value : explode(' to ', $value);
            if (count($value) === 1) {
                $startOfDay = date('Y-m-d', strtotime($value[0])) . ' 00:00:00';
                $endOfDay = date('Y-m-d', strtotime($value[0])) . ' 23:59:59';
                $query = $query->where('created_at', '>=', $startOfDay)->where('created_at', '<=', $endOfDay);

            } else {
                $startOfDay = date('Y-m-d', strtotime($value[0])) . ' 00:00:00';
                $endOfDay = date('Y-m-d', strtotime($value[1])) . ' 23:59:59';
                $query = $query->where('created_at', '>=', $startOfDay)->where('created_at', '<=', $endOfDay);
            }
        }

        if ($request->input('updated_at')) {
            $value = is_array($value) ? $value : explode(' to ', $value);
            if (count($value) === 1) {
                $startOfDay = date('Y-m-d', strtotime($value[0])) . ' 00:00:00';
                $endOfDay = date('Y-m-d', strtotime($value[0])) . ' 23:59:59';
                $query = $query->where('updated_at', '>=', $startOfDay)->where('updated_at', '<=', $endOfDay);

            } else {
                $startOfDay = date('Y-m-d', strtotime($value[0])) . ' 00:00:00';
                $endOfDay = date('Y-m-d', strtotime($value[1])) . ' 23:59:59';
                $query = $query->where('updated_at', '>=', $startOfDay)->where('updated_at', '<=', $endOfDay);
            }
        }


        $query->orderBy('updated_at', 'desc');

        $query->when(!empty($request->search), function ($q) use ($request) {
            return $q->where('first_name', 'like', '%' . $request->search . '%')
                ->orWhere('last_name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('mobile', 'like', '%' . $request->search . '%');
        });
        $query->with(['agent','propertyCategory','updatedBy','createdBy','source','type','medium','bed','bath','status']);
        $list_per_page = '10';
        $lead_count = $query->count();
        $leads = $query->paginate($list_per_page);

        if ($lead_count > 0) {
            $leads->data = ListingLeadResource::collection($leads);
            $leads->appends($request->query())->links();
        } else {
            return $this->sendResponse(false, 'No Leads found', [], 200);
        }
        return $this->sendResponse(true, 'Lead list', compact('leads'), 200);
    }

}
