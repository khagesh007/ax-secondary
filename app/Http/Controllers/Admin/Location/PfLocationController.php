<?php

namespace App\Http\Controllers\Admin\Location;

use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Region;
use App\Models\SubCommunity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use App\Services\Portal\PropertyFinderService;
class PfLocationController extends Controller
{

    public function searchAtPf(Request $request)
    {
        $term = $request->search_term;
       $pfService = new PropertyFinderService();
        $result = $pfService->searchPfLocations($term);
        if ($result) {
            return response()->json(['status' => '1', 'data' => $result]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'No results found']);
        }

    }

    public function mapPFlocation(Request $request)
    {
        $module = $request->get('module');
        $id = $request->get('id');
        $location_id = $request->get('location_id');

        if($module == 'community'){
            $community = Community::find($id);
            if($community){
                if($community){
                    $portal_ids = json_decode($community->portal_ids, true) ?? [];
                    $portal_ids['property_finder_id'] = $location_id;
                    $community->portal_ids = json_encode($portal_ids);
                    $community->save();
                    return response()->json(['status' => 'success', 'message' => 'Community Location updated successfully']);
                }else{
                    return response()->json(['status' => 'error', 'message' => 'Location not found']);
                }
            }else{
                return response()->json(['status' => 'error', 'message' => 'Community not found']);
            }
        }else if($module == 'sub_community'){
            $sub_community = SubCommunity::find($id);
            if($sub_community){

                if($sub_community){
                    $portal_ids = json_decode($sub_community->portal_ids, true) ?? [];
                    $portal_ids['property_finder_id'] = $location_id;
                    $sub_community->update(['portal_ids' => $portal_ids]);
                    return response()->json(['status' => 'success', 'message' => 'Sub Community Location updated successfully']);
                }else{
                    return response()->json(['status' => 'error', 'message' => 'Location not found']);
                }
            }else{
                return response()->json(['status' => 'error', 'message' => 'Sub Community not found']);
            }
        }else if($module == 'community_property'){
            $community_property = CommunityProperty::find($id);
            if($community_property){
                // $location = $community_property->portals()->where('portal_id', 1)->first();
                if($community_property){
                    $portal_ids = json_decode($community_property->portal_ids, true) ?? [];
                    $portal_ids['property_finder_id'] = $location_id;
                    $community_property->update(['portal_ids' => $portal_ids]);
                    return response()->json(['status' => 'success', 'message' => 'Location updated successfully']);
                }else{
                    return response()->json(['status' => 'error', 'message' => 'Location not found']);
                }
            }else{
                return response()->json(['status' => 'error', 'message' => 'Community Property not found']);
            }
        }else{
            return response()->json(['status' => 'error', 'message' => 'Invalid module']);
        }

    }


}
