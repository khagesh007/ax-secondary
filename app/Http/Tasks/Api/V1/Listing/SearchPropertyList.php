<?php

namespace App\Http\Tasks\Api\V1\Listing;

use App\Http\Requests\Api\V1\Listing\GetPropertyListRequest;
use App\Http\Resources\Property\PropertyResourceV2;
use App\Http\Tasks\Api\BaseTask;
use App\Models\Property;
use DB;

class SearchPropertyList extends BaseTask
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
                    $q->whereIn("agent_id", $childrenIds)->orWhere(function ($q) {
                        $q->where('publish_status', "POCKET")->orWhere('publish_status', "PUBLISHED");
                    });
                });
            })
            ->when(auth()->user()->hasRole("Admin - Abu Dhabi") && $request->all_listings == "false", function ($q) {
                $q->where("region_id", 2);
            });

        if ($request->input('ref_no')) {
            $query->where('ref_no', 'like', '%' . $request->input('ref_no') . '%');
        }
        if ($request->input('agent_id')) {
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
        if ($request->input('rera_permit_number')) {
            $query->where('rera_permit_number', $request->input('rera_permit_number'));
        }

        if ($request->input('for')) {
            $value = $this->isArray("for") ? [] : [$request->input('for')];
            if ($this->isArray("for")){
                foreach ($request->input('for') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("for", $value);
            }
        }

        if ($request->input('property_category_id')) {
            $value = $this->isArray("property_category_id") ? [] : [$request->input('property_category_id')];
            if ($this->isArray("property_category_id")){
                foreach ($request->input('property_category_id') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("property_category_id", $value);
            }
        }

        if ($request->input('bed_id')) {
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

        if ($request->input('bath_id')) {
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
        if ($request->input('completion_status')) {
            $value = $this->isArray("completion_status") ? [] : [$request->input('completion_status')];
            if ($this->isArray("completion_status")){
                foreach ($request->input('completion_status') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("completion_status", $value);
            }
        }

        if ($request->input('furnished')) {
            $value = $this->isArray("furnished") ? [] : [$request->input('furnished')];
            if ($this->isArray("furnished")){
                foreach ($request->input('furnished') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("furnished", $value);
            }
        }

        if ($request->input('publish_status')) {
            $value = $this->isArray("publish_status") ? [] : [$request->input('publish_status')];
            if ($this->isArray("publish_status")){
                foreach ($request->input('publish_status') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("publish_status", $value);
            }
        }

        if ($request->input('region_id')) {
            $value = $this->isArray("region_id") ? [] : [$request->input('region_id')];
            if ($this->isArray("region_id")){
                foreach ($request->input('region_id') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("region_id", $value);
            }
        }

        if ($request->input('community_id')) {
            $value = $this->isArray("community_id") ? [] : [$request->input('community_id')];
            if ($this->isArray("community_id")){
                foreach ($request->input('community_id') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("community_id", $value);
            }
        }

        if ($request->input('sub_community_id')) {
            $value = $this->isArray("sub_community_id") ? [] : [$request->input('sub_community_id')];
            if ($this->isArray("sub_community_id")){
                foreach ($request->input('sub_community_id') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("sub_community_id", $value);
            }
        }

        if ($request->input('community_property_id')) {
            $value = $this->isArray("community_property_id") ? [] : [$request->input('community_property_id')];
            if ($this->isArray("community_property_id")){
                foreach ($request->input('community_property_id') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("community_property_id", $value);
            }
        }
        if ($request->input('community_property_id')) {
            $value = $this->isArray("community_property_id") ? [] : [$request->input('community_property_id')];
            if ($this->isArray("community_property_id")){
                foreach ($request->input('community_property_id') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("community_property_id", $value);
            }
        }

        if ($request->input('views')) {
            $value = $this->isArray("views") ? [] : [$request->input('views')];
            if ($this->isArray("views")){
                foreach ($request->input('views') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereNotNull('view') // skip SQL NULLs
                ->whereRaw('JSON_VALID(view)') // skip invalid JSON
                ->where(function ($q) use ($value) {
                    foreach ($value as $v) {
                        $q->orWhereJsonContains('view', $v);
                    }
                });
            }
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
