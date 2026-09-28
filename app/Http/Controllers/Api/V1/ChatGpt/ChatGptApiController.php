<?php

namespace App\Http\Controllers\Api\V1\ChatGpt;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Property;

class ChatGptApiController extends Controller
{

    public function handle(Request $request)
    {
        $apiKey = env('CHAT_API');
        $apiUrl = 'https://api.openai.com/v1/chat/completions';

        $property = Property::with('propertyCategory', 'community', 'subCommunity', 'communityProperty', 'propertyCategory',
                'agent', 'createdBy', 'updatedBy', 'portals.portal', 'propertyStatus', 'bed')->where('ref_no', $request->input('ref_no'))->first();

        $location = $property->subCommunity->path_name;
        $unit_no = $property->unit_no;
        $community = $property->community_id;
        $community_name = $property->community->name;

        $sub_community = $property->sub_community_id;
        $sub_community_name = $property->subCommunity->name;

        $type = $property->property_category_id;
        $type_name = $property->propertyCategory->title;


        $message = 'Write  description for the property that AX CAPITAL want to sell. with the following keywords. location : ' . $location . ' unit no : 816, community : ' . $community_name . ' sub community : ' . $sub_community_name . ' type : ' . $type_name . '. description limit MAX 500-750 characters. write in HTMl format and use <br> for new line. and <b> for bold text. and also use bullet points.';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post($apiUrl, [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $message],
            ],
        ]);
        $responseData = $response->json();
        return response()->json(['response' => $responseData]);
    }

}
