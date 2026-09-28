<?php

namespace App\Http\Controllers\Admin\Listing;


use App\Models\Property;
use Illuminate\Support\Facades\Response;


use Cjmellor\Approval\Enums\ApprovalStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Auth;

class ListingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function checkDuplicateScore($property, $value)
    {
        $score = 0;
        if ($property?->unit_no == $value->unit_no) {
            $score += 20;
        }
        if ($property->community_id == $value->community_id) {
            $score += 20;
        }
        if ($property->sub_community_id == $value->sub_community_id) {
            $score += 20;
        }
        if ($property->community_property_id == $value->community_property_id) {
            $score += 20;
        }
        if ($property->for == $value->for) {
            $score += 20;
        }
        //$titleSimilarity = wordSimilarity($property->title, $value->title);
        //$score += round($titleSimilarity) * 10;
        // if ($value->publish_status == 'ARCHIVED' && $score > 80) {
        //     $score = 80;
        // }
        if ($score > 100) {
            $score = 100;
        }
        return $score;

    }


    public function checkDuplicateListings(Request $request)
    {
        //add permit, remove community,
        $duplicate = [];
        $property = Property::where('id', $request->property_id)->with('agent', 'community', 'subCommunity', 'communityProperty')->first();
        $datetime = Carbon::parse($property->created_at);
        $properties1 = Property::where('id', '!=', $request->property_id)
            ->where('unit_no', $property->unit_no)
           // ->where('rera_permit_number', $property->rera_permit_number)
            ->where('for', $property->for)
            // ->when($property->sub_community_id, function ($q) use ($property) {
            //     $q->where('sub_community_id', $property->sub_community_id);
            // })
            ->when($property->community_property_id, function ($q) use ($property) {
                $q->where('community_property_id', $property->community_property_id);
            })
            //->where('created_at', '<', $datetime)
            // ->whereIn('publish_status', ['PUBLISHED', 'POCKET','PENDING','ARCHIVED','DRAFT'])
            ->with('agent', 'community', 'subCommunity', 'communityProperty')
            ->get();
        $properties2 = Property::where('id', '!=', $request->property_id)
            ->where('unit_no', $property->unit_no)
            ->where('for', $property->for)
            ->when($property->sub_community_id, function ($q) use ($property) {
                $q->where('sub_community_id', $property->sub_community_id);
            })
            // ->when($property->community_property_id, function ($q) use ($property) {
            //     $q->where('community_property_id', $property->community_property_id);
            // })
            ->where('created_at', '<', $datetime)
          // ->whereIn('publish_status', ['PUBLISHED', 'POCKET','PENDING','ARCHIVED','DRAFT'])
            ->with('agent', 'community', 'subCommunity', 'communityProperty')
            ->get();

       
        $unPublishedArchived = Property::where('id', '!=', $request->property_id)
            ->when($property->unit_no, function ($q) use ($property) {
                $q->where('unit_no', $property->unit_no);
            })
            ->where('for', $property->for)
            ->when($property->community_id, function ($q) use ($property) {
                $q->where('community_id', $property->community_id);
            })
            ->when($property->sub_community_id, function ($q) use ($property) {
                $q->where('sub_community_id', $property->sub_community_id);
            })
            ->when($property->community_property_id, function ($q) use ($property) {
                $q->where('community_property_id', $property->community_property_id);
            })
            // ->whereIn('publish_status', ['UNPUBLISHED', 'ARCHIVED', 'ARCHIVED','DRAFT'])
            ->where('created_at', '<', $datetime)
            //   ->where('community_property_id',$property->community_property_id)
            ->with('agent', 'community', 'subCommunity', 'communityProperty')->get();

        foreach ($properties1 as $key => $value) {
            $value->duplicate_score = $this->checkDuplicateScore($property, $value);
            $duplicate[] = $value;
        }
        foreach ($properties2 as $key => $value) {
            $value->duplicate_score = $this->checkDuplicateScore($property, $value);
            $duplicate[] = $value;
        }
      
        // foreach ($unPublishedArchived as $key => $value) {
        //     $value->duplicate_score = $this->checkDuplicateScore($property, $value);
        //     $duplicate[] = $value;
        // }

        $duplicate = collect($duplicate)->sortByDesc('duplicate_score')->unique('id')->values()->all();

        //check if duplicate is more than 0
        if (count($duplicate) > 0) {
            return Response::json(['status' => 1, 'property' => $property, 'data' => $duplicate]);
        }else{
            return Response::json(['status' => 0, 'property' => $property, 'data' => [], 'message' => 'No duplicate found']);
        }

    }
}
