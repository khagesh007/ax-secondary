<?php

namespace App\Services\Dashboard;

use App\Models\Source;
use App\Models\Lead;
use App\Models\User;
use App\Models\Property;
use App\Services\Dashboard\DashboardReportFilterService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use DB;

class LeadByListingReportService
{

    public function summary($request, $params)
    {
        try {
             /*
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
            $childrenIds = [];
            if ($notAdmin) {
                $childrenIds = getUserChildrenIds(auth()->id());
            }
            */
            $childrenIds = $params['user_ids'];

            $start_date = $params['start_date'];
            $end_date = $params['end_date'];

            $startDate = Carbon::parse($start_date)->format("Y-m-d");
            $endDate = Carbon::parse($end_date)->format("Y-m-d");


            // Fetch live listings with necessary relationships and limit the columns
            /* $live_listings = Property::select(['id', 'is_live', 'ref_no', 'title', 'unit_no', 'community_id', 'sub_community_id', 'community_property_id'])
                                    ->where('is_live', true)
                                    ->when($childrenIds, function ($query) use ($childrenIds) {
                                            $query->whereIn('agent_id', $childrenIds);
                                        })
                                        ->whereHas('leads', function ($query) use ($startDate, $endDate) {
                                            if ($startDate && $endDate) {
                                                $query->whereBetween('created_at', [$startDate, $endDate]);
                                            }
                                            $query->groupBy('property_id', 'source_id')
                                                ->selectRaw('count(*) as count, property_id, source_id')
                                                ->havingRaw('count > 0');
                                        }) // ✅ Only properties where leads exist
                                    ->with(['leads' => function ($query) use ($startDate, $endDate) {
                                        if ($startDate && $endDate) {
                                            $query->whereBetween('created_at', [$startDate, $endDate]);
                                        }
                                        $query->select('property_id', 'source_id')
                                            ->groupBy('property_id', 'source_id')
                                            ->selectRaw('count(*) as count, property_id, source_id')
                                            ->havingRaw('count > 0');
                                    }])->get();
                */
                $live_listings = Property::select([
                    'id', 'is_live', 'ref_no', 'title', 'unit_no', 'community_id', 'sub_community_id', 'community_property_id', 'agent_id'
                ])
                ->where('is_live', true)
                ->when($childrenIds, function ($query) use ($childrenIds) {
                    $query->whereIn('agent_id', $childrenIds);
                })
                ->whereHas('leads', function ($query) use ($startDate, $endDate) {
                    $query->whereNull('deleted_at'); // Safe filter
                    if ($startDate && $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    }
                })
                ->with(['leads' => function ($query) use ($startDate, $endDate) {
                    $query->whereNull('deleted_at');
                    if ($startDate && $endDate) {
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    }
                    $query->selectRaw('property_id, source_id, count(*) as count')
                          ->groupBy('property_id', 'source_id');
                }])
                    ->with(['agent:id,name'])
                ->get();


            // Sources data

            $sources = Source::whereIn('name', ['Property Finder', 'Bayut', 'Dubizzle'])->pluck('name', 'id');
            $selected_source_ids = $sources->keys();

            $other_sources = Source::whereNotIn('name', ['Property Finder', 'Bayut', 'Dubizzle'])->pluck('id');
            $listing_leads = [];



            foreach ($live_listings as $listing) {
                $leads = [];
                foreach ($listing->leads as $lead) {
                    if ($selected_source_ids->contains($lead->source_id)) {
                        $source_name = $sources[$lead->source_id];
                    } else {
                        $source_name = 'Other';
                    }
                    $leads[$source_name] = $lead->count;
                }
                // Calculate total leads for the current listing
                $leads['Other'] = Lead::where('property_id', $listing->id)
                                    ->whereNotIn('source_id', $selected_source_ids)
                                    ->whereIn('source_id', $other_sources)
                                    ->count();

                // Prepare the listing data with the leads
                $listing_data = [
                    'total_leads' => array_sum($leads),
                    'ref_no' => $listing->ref_no,
                    'leads' => $leads,
                    'title' => $listing->title,
                    'unit_no' => $listing->unit_no,
                    'image' => $listing->first_image?->thumbnail,
                    'location' => $listing->location,
                    'agent_name' => $listing->agent->name ?? '',
                ];

                $listing_leads[] = $listing_data;
            }

            // Sort listings by total leads
            $listing_leads = collect($listing_leads)->sortByDesc('total_leads')->toArray();

            // Extract ref_no from listing_leads
            $ref_nos = array_column($listing_leads, 'ref_no');

            $data = [
                'ref_nos' => $ref_nos,
                'listing_lead_with_source' => $listing_leads,
                'sources' => ['Property Finder', 'Bayut', 'Dubizzle', 'Other']
            ];
            return $data;
            return response()->json($data, 200);
        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
        }
    }
}
