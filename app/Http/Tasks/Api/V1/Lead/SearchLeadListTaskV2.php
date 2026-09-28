<?php

namespace App\Http\Tasks\Api\V1\Lead;

use App\Http\Requests\Api\V1\Lead\GetLeadListRequest;
use App\Http\Resources\LeadResource;
use App\Http\Tasks\Api\BaseTask;
use App\Models\Lead;

class SearchLeadListTaskV2 extends BaseTask
{
    private function isArray($key)
    {
        return gettype(\request($key)) == 'array' && count(\request($key)) > 0;
    }
    public function run(GetLeadListRequest $request)
    {

        $query = Lead::query();

        $query->when($request->boolean('portal_lead'), fn($q) => $q->where('portal_lead', true),
            function ($q) use ($request) {
                $q->when($request->boolean('bitrix_pool_lead'), function ($q1) {
                    $q1->whereNull("agent_id")
                        ->where('portal_lead', false)
                        ->where('need_to_be_destroyed', 0);
                }, fn($q1) => $q1->where('agent_id', auth()->id()));
            })
            ->where('is_archive', 0)
            ->whereNull('converted_to_listing_lead');


        if ($request->lead_status_id) {
            $value = $this->isArray("lead_status_id") ? [] : [$request->input('lead_status_id')];
            if ($this->isArray("lead_status_id")){
                foreach ($request->input('lead_status_id') as $input){
                    if (!empty($input)) {
                        $value[] = $input;
                    }
                }
            }

            if (!empty($value)) {
                $query->whereIn("lead_status_id", $value);
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
                $query->whereIn("lead_medium_id", $value);
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
        if ($request->mobile) {

            $query = $query->where('mobile', 'like', '%' . $request->mobile . '%');
            // $query->whereRaw("REPLACE(`mobile`, ' ', '') LIKE ?", ['%' . trimSpaces(validatePhoneNumber($request->mobile)) . '%']);

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

        if ($request->min_bed_id) {
            $query = $query->whereHas("criteria", function ($q) {
                $this->isArray("min_bed_id") ? $q->whereIn('min_bed_id', \request("min_bed_id")) : $q->where('min_bed_id', \request("min_bed_id"));
            });
        }
        if ($request->max_bed_id) {
            $query = $query->whereHas("criteria", function ($q) {
                $this->isArray("max_bed_id") ? $q->whereIn('max_bed_id', \request("max_bed_id")) : $q->where('max_bed_id', \request("max_bed_id"));
            });
        }

        if ($request->min_bath_id) {
            $query = $query->whereHas("criteria", function ($q) {
                $this->isArray("min_bath_id") ? $q->whereIn('min_bath_id', \request("min_bath_id")) : $q->where('min_bath_id', \request("min_bath_id"));
            });
        }
        if ($request->max_bath_id) {
            $query = $query->whereHas("criteria", function ($q) {
                $this->isArray("max_bath_id") ? $q->whereIn('max_bath_id', \request("max_bath_id")) : $q->where('max_bath_id', \request("max_bath_id"));
            });
        }
        if ($request->furnished) {
            $query = $query->whereHas("criteria", function ($q) use ($request) {
                $value = $this->isArray("furnished") ? [] : [$request->input('furnished')];
                if ($this->isArray("furnished")){
                    foreach ($request->input('furnished') as $input){
                        if (!empty($input)) {
                            $value[] = $input;
                        }
                    }
                }

                if (!empty($value)) {
                    $q->whereIn("furnished", $value);
                }
            });
        }
        if ($request->communities) {
            $query = $query->whereHas("criteria", function ($q) {
                $q->whereHas('locations', function ($q) {
                    $this->isArray("communities") ? $q->whereIn('parentable_id', \request("communities"))->where('parentable_type', 'App\Models\Community') : $q->where('parentable_id', \request("communities"))->where('parentable_type', 'App\Models\Community');
                });
            });
        }

        if ($request->sub_communities) {
            $query = $query->whereHas("criteria", function ($q) {
                $q->whereHas('locations', function ($q) {
                    $this->isArray("sub_communities") ? $q->whereIn('parentable_id', \request("sub_communities"))->where('parentable_type', 'App\Models\SubCommunity') : $q->where('parentable_id', \request("sub_communities"))->where('parentable_type', 'App\Models\SubCommunity');
                });
            });
        }

        if ($request->input('created_at')) {
            $value = $request->input('created_at');
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
            $value = $request->input('updated_at');
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

        // $query->orderBy('id', 'desc');
        // orderbu updated_at
        $query->orderBy('updated_at', 'desc');
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
        // dd($leads);
        return $this->sendResponse(true, 'Lead list', compact('leads'), 200);
    }
}
