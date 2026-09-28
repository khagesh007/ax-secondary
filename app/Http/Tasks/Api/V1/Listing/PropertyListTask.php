<?php

namespace App\Http\Tasks\Api\V1\Listing;

use App\Http\Requests\Api\V1\Listing\GetPropertyListRequest;
use App\Http\Resources\Property\PropertyResourceV2;
use App\Http\Tasks\Api\BaseTask;
use App\Models\Property;
use DB;

class PropertyListTask extends BaseTask
{
    private function isArray($key)
    {
        return gettype(\request($key)) == 'array' && count(\request($key)) > 0;
    }

    public function run(GetPropertyListRequest $request)
    {

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Primary Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $query = Property::query();

        $query->when($notAdmin && !($request->all_listings == "true"), function ($q) use ($childrenIds) {
            $q->whereIn("agent_id", $childrenIds);
        })
            ->when($notAdmin && $request->all_listings == "true", function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn("agent_id", $childrenIds)->orWhere('publish_status', "POCKET")->orWhere('is_live', true);
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi") && $request->all_listings == "false", function ($q) {
                $q->where("region_id", 2);
            });

        if ($request->input('ref_no')) {
            $query->where('ref_no', 'like', '%' . $request->input('ref_no') . '%');
        }
        if ($request->input('agent_id')) {
            $this->isArray("agent_id") ? $query->whereIn("agent_id", \request("agent_id")) : $query->where("agent_id", \request("agent_id"));
        }
        if ($request->input('rera_permit_number')) {
            $query->where('rera_permit_number', $request->input('rera_permit_number'));
        }

        if ($request->input('for')) {
            $this->isArray("for") ? $query->whereIn("for", \request("for")) : $query->where("for", \request("for"));

        }

        if ($request->input('property_category_id')) {
            $this->isArray("property_category_id") ? $query->whereIn("property_category_id", \request("property_category_id")) : $query->where("property_category_id", \request("property_category_id"));

        }

        if ($request->input('bed_id')) {
            $this->isArray("bed_id") ? $query->whereIn("bed_id", \request("bed_id")) : $query->where("bed_id", \request("bed_id"));

        }

        if ($request->input('bath_id')) {
            $this->isArray("bath_id") ? $query->whereIn("bath_id", \request("bath_id")) : $query->where("bath_id", \request("bath_id"));

        }
        if ($request->input('completion_status')) {
            $this->isArray("completion_status") ? $query->whereIn("completion_status", \request("completion_status")) : $query->where("completion_status", \request("completion_status"));
        }

        if ($request->input('furnished')) {
            $this->isArray("furnished") ? $query->whereIn("furnished", \request("furnished")) : $query->where("furnished", \request("furnished"));
        }

        if ($request->input('publish_status')) {
            $this->isArray("publish_status") ? $query->whereIn("publish_status", \request("publish_status")) : $query->where("publish_status", \request("publish_status"));
        }
        if ($request->input('region_id')) {
            $this->isArray("region_id") ? $query->whereIn("region_id", \request("region_id")) : $query->where("region_id", \request("region_id"));
        }
        if ($request->input('community_id')) {
            $this->isArray("community_id") ? $query->whereIn("community_id", \request("community_id")) : $query->where("community_id", \request("community_id"));
        }
        if ($request->input('sub_community_id')) {
            $this->isArray("sub_community_id") ? $query->whereIn("sub_community_id", \request("sub_community_id")) : $query->where("sub_community_id", \request("sub_community_id"));
        }
        if ($request->input('community_property_id')) {
            $this->isArray("community_property_id") ? $query->whereIn("community_property_id", \request("community_property_id")) : $query->where("community_property_id", \request("community_property_id"));
        }

        if ($request->input('price_from') && !$request->input('price_to')) {
            $query->whereHas('price', function ($query) use ($request) {
                $query->whereBetween('price', [$request->price_from, $request->price_to]);
            });
        }

        if ($request->input('price_to') && !$request->input('price_from')) {
            $query->whereHas('price', function ($query) use ($request) {
                $query->where('price', '<=', $request->price_to);
            });
        }

        if ($request->input('price_from') && $request->input('price_to')) {
            $query->whereHas('price', function ($query) use ($request) {
                $query->whereBetween('price', [$request->price_from, $request->price_to]);
            });
        }

        if ($request->input('property_plot_size_from') || $request->input('property_plot_size_to')) {
            $from = $request->input('property_plot_size_from', 0);
            $to = $request->input('property_plot_size_to', 10000);
            $query->whereBetween('plot_size', [$from, $to]);
        }
        // dd($request->all());
        // dd($request->input('property_build_up_area_from') || $request->input('property_build_up_area_to'));
        if ($request->input('property_build_up_area_from') || $request->input('property_build_up_area_to')) {
            $from = (float) $request->input('property_build_up_area_from', 0);
            $to = (float) $request->input('property_build_up_area_to', 10000);
            $query->whereBetween(DB::raw('CAST(built_up_area AS DECIMAL)'), [$from, $to]);
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
        if ($request->input('is_live')) {
            $query->where('is_live', true);
        }
        $query->when($request->ref_no, function ($query, $ref_no) {
            $query = $query->where('ref_no', $ref_no);
        });

        $query->orderBy('updated_at', 'desc');
        $query->when(!empty($request->search), function ($q) use ($request) {
            return $q->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('unit_no', 'like', '%' . $request->search . '%')
                ->orWhere('ref_no', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        });
        $query->with(['bed', 'details',
            'agent',
            'propertyCategory.portals',
            'price',
            'bath.portals',
            'amenities.amenity.portals',
            'community.region',
            'subCommunity',
            'communityProperty',
            'photos',
            'floorPlan']);
        $list_per_page = '10';
        $lead_count = $query->count();
        $leads = $query->paginate($list_per_page);

        if ($lead_count > 0) {
            $leads->data = PropertyResourceV2::collection($leads);
            $leads->appends($request->query())->links();
        } else {
            return $this->sendResponse(false, 'Property not found', [], 200);
        }

        return $this->sendResponse(true, 'Property list', compact('leads'), 200);
    }
}
