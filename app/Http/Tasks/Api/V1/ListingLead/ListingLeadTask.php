<?php

namespace App\Http\Tasks\Api\V1\ListingLead;

use App\Http\Requests\Api\V1\ListingLead\GetListingLeadRequest;
use App\Http\Resources\ListingLeadResource;
use App\Http\Tasks\Api\BaseTask;
use App\Models\ListingLead;

class ListingLeadTask extends BaseTask
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
        // dd($childrenIds);
        $query = $query->when($notAdmin , function ($q) use ($childrenIds) {
            // dd($childrenIds);
            $q->whereIn("agent_id", $childrenIds);
        });

        if ($request->agent_id) {
            $this->isArray("agent_id") ? $query->whereIn('agent_id', \request("agent_id")) : $query->where('agent_id', \request("agent_id"));

            // $query = $query->where('agent_id', $request->agent_id);
        }

        if ($request->bed_id) {
            $this->isArray("bed_id") ? $query->whereIn('bed_id', \request("bed_id")) : $query->where('bed_id', \request("bed_id"));
            // $query = $query->where('agent_id', $request->agent_id);
        }
        if ($request->bath_id) {
            $this->isArray("bath_id") ? $query->whereIn('bath_id', \request("bath_id")) : $query->where('bath_id', \request("bath_id"));
            // $query = $query->where('agent_id', $request->agent_id);
        }

        if ($request->status_id) {
            $this->isArray("status_id") ? $query->whereIn('status_id', \request("status_id")) : $query->where('status_id', \request("status_id"));

            // $query = $query->where('status_id', $request->status_id);
        }

        if ($request->medium_id) {
            $this->isArray("medium_id") ? $query->whereIn('medium_id', \request("medium_id")) : $query->where('medium_id', \request("medium_id"));

            // $query = $query->where('medium_id', $request->medium_id);
        }

        if ($request->source_id) {
            $this->isArray("source_id") ? $query->whereIn('source_id', \request("source_id")) : $query->where('source_id', \request("source_id"));

            // $query = $query->where('source_id', $request->source_id);
        }

        if ($request->type_id) {
            $this->isArray("type_id") ? $query->whereIn('type_id', \request("type_id")) : $query->where('type_id', \request("type_id"));

            // $query = $query->where('type_id', $request->type_id);
        }
        if ($request->communities) {
            $this->isArray("communities") ? $query->whereIn('community_id', \request("communities")) : $query->where('community_id', \request("communities"));

            // $query = $query->where('community_id', $request->community_id);
        }
        if ($request->sub_communities) {
            $this->isArray("sub_communities") ? $query->whereIn('sub_community_id', \request("sub_communities")) : $query->where('sub_community_id', \request("sub_communities"));

            // $query = $query->where('sub_community_id', $request->sub_community_id);
        }
        if ($request->community_properties) {
            $this->isArray("community_properties") ? $query->whereIn('community_property_id', \request("community_properties")) : $query->where('community_property_id', \request("community_properties"));

            // $query = $query->where('community_property_id', $request->community_property_id);
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
        // dd($leads);
        return $this->sendResponse(true, 'Lead list', compact('leads'), 200);
    }

}
