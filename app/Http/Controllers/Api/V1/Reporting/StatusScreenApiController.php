<?php

namespace App\Http\Controllers\Api\V1\Reporting;

use App\Http\Controllers\Api\BaseController;
use App\Models\Lead;
use App\Models\Portal;
use App\Models\Property;
use Carbon\Carbon;
use App\Models\Source;
use App\Models\Medium;
use App\Models\Type;
use App\Models\LeadStatus;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class StatusScreenApiController extends BaseController
{


    public function dataForScreen3(Request $request)
    {
        $pf_portal = Portal::where('name', 'Property Finder')->first();
        $bayut_portal = Portal::where('name', 'Bayut')->first();

        $portals = Portal::whereIn('name', ['Property Finder', 'Bayut', 'Houza', 'James Edition'])->pluck('id')->toArray();

        $data['totalPropertyCount'] = Property::where('publish_status', 'PUBLISHED')->whereHas('portals', function ($q) use ($portals) {
            $q->whereIn('portal_id', $portals);
        })->where('completion_status','!=','Off-Plan Primary')->count();
        $data['previousMonthPublished'] = Property::where('publish_status', 'PUBLISHED')->whereBetween('published_date', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->where('completion_status','!=','Off-Plan Primary')->count();

        $data['thisMonthPublished'] = Property::where('publish_status', 'PUBLISHED')->whereBetween('published_date', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()])->whereHas('portals', function ($q) use ($portals) {
            $q->whereIn('portal_id', $portals);
        })->where('completion_status','!=','Off-Plan Primary')->count();

        //  $data['todayPublished'] = Property::where('publish_status', 'PUBLISHED')->whereDate('published_date', Carbon::today())->count();
        $data['thisWeekPublished'] = Property::where('publish_status', 'PUBLISHED')->whereBetween('published_date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()])->whereHas('portals', function ($q) use ($portals) {
            $q->whereIn('portal_id', $portals);
        })->where('completion_status','!=','Off-Plan Primary')->count();


        $data['pf_count'] = Property::whereHas('portals', function ($q) use ($pf_portal) {
            $q->where('portal_id', $pf_portal->id);
        })->where('completion_status','!=','Off-Plan Primary')->where('publish_status', 'PUBLISHED')->count();
        $data['pf_verified_count'] = Property::whereHas('portals', function ($q) use ($pf_portal) {
            $q->where('portal_id', $pf_portal->id);
        })->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'pf_verified')
                ->where('meta_value', '1');
        })->where('publish_status', 'PUBLISHED')->where('completion_status','!=','Off-Plan Primary')->count();

        $data['pf_count_pre_month'] = Property::whereHas('portals', function ($q) use ($pf_portal) {
            $q->where('portal_id', $pf_portal->id);
        })->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'pf_verified')
                ->where('meta_value', '1');
        })->where('publish_status', 'PUBLISHED')->whereBetween('published_date', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->where('completion_status','!=','Off-Plan Primary')->count();
        $data['pf_count_current_month'] = Property::whereHas('portals', function ($q) use ($pf_portal) {
            $q->where('portal_id', $pf_portal->id);
        })->where('publish_status', 'PUBLISHED')->whereBetween('published_date', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()])->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'pf_verified')
                ->where('meta_value', '1');
        })->where('completion_status','!=','Off-Plan Primary')->count();
        $pf_avg_rank = Property::join('property_meta', function ($join) {
            $join->on('properties.id', '=', 'property_meta.property_id')
                ->where('property_meta.meta_key', '=', 'pf_rank');
        })->whereHas('portals', function ($q) use ($pf_portal) {
            $q->where('portal_id', $pf_portal->id);
        })->where('publish_status', 'PUBLISHED')->where('completion_status','!=','Off-Plan Primary')->avg('property_meta.meta_value');;
        $data['pf_quality_avg'] = round($pf_avg_rank);

//        $data['totalPocketCount'] = Property::where('publish_status', 'POCKET')->count();
//        $data['todayPocket'] = Property::where('publish_status', 'POCKET')->whereDate('published_date', Carbon::today())->count();
//        $data['thisWeekPocket'] = Property::where('publish_status', 'POCKET')->whereBetween('published_date', [
//            Carbon::now()->startOfWeek(),
//            Carbon::now()->endOfWeek()])->count();
//        $data['pocketPreviousMonth'] = Property::where('publish_status', 'POCKET')->whereBetween('published_date', [
//            Carbon::now()->subMonth()->startOfMonth(),
//            Carbon::now()->subMonth()->endOfMonth()
//        ])->count();
//        $data['pocketCurrentMonth'] = Property::where('publish_status', 'POCKET')->whereBetween('published_date', [
//            Carbon::now()->startOfMonth(),
//            Carbon::now()->endOfMonth()])->count();

        $data['bayut_count'] = Property::whereHas('portals', function ($q) use ($bayut_portal) {
            $q->where('portal_id', $bayut_portal->id);
        })->where('publish_status', 'PUBLISHED')->where('completion_status','!=','Off-Plan Primary')->count();
        $data['bayut_trucheck_count'] = Property::whereHas('portals', function ($q) use ($bayut_portal) {
            $q->where('portal_id', $bayut_portal->id);
        })->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_trucheck_status')
                ->where('meta_value', 'Truchecked');
        })->where('publish_status', 'PUBLISHED')->where('completion_status','!=','Off-Plan Primary')->count();

        $data['bayut_count_pre_month'] = Property::whereHas('portals', function ($q) use ($bayut_portal) {
            $q->where('portal_id', $bayut_portal->id);
        })->where('publish_status', 'PUBLISHED')->whereBetween('published_date', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth()
        ])->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_trucheck_status')
                ->where('meta_value', 'Truchecked');
        })->where('completion_status','!=','Off-Plan Primary')->count();
        $data['bayut_count_current_month'] = Property::whereHas('portals', function ($q) use ($bayut_portal) {
            $q->where('portal_id', $bayut_portal->id);
        })->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_trucheck_status')
                ->where('meta_value', 'Truchecked');
        })->where('publish_status', 'PUBLISHED')->whereBetween('published_date', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])->where('completion_status','!=','Off-Plan Primary')->count();


        return $this->sendResponse(true, 'Lead Count Fetched', $data, 200);


    }

    public function dataForScreen2(Request $request)
    {
        $data['totalPropertyCount'] = Property::where('publish_status', 'PUBLISHED')->count();

        $pf_portal = Portal::where('name', 'Property Finder')->first();
        $bayut_portal = Portal::where('name', 'Bayut')->first();
        $dubizzle_portal = Portal::where('name', 'Bayut')->first();
        $data['pf_count'] = Property::whereHas('portals', function ($q) use ($pf_portal) {
            $q->where('portal_id', $pf_portal->id);
        })->where('publish_status', 'PUBLISHED')->count();

        $data['pf_verfified_count'] = Property::whereHas('portals', function ($q) use ($pf_portal) {
            $q->where('portal_id', $pf_portal->id);
        })->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'pf_verified')
                ->where('meta_value', '1');
        })->where('publish_status', 'PUBLISHED')->count();

        $data['bayut_count'] = Property::whereHas('portals', function ($q) use ($bayut_portal) {
            $q->where('portal_id', $bayut_portal->id);
        })->where('publish_status', 'PUBLISHED')->count();
        $data['bayut_trucheck_count'] = Property::whereHas('portals', function ($q) use ($bayut_portal) {
            $q->where('portal_id', $bayut_portal->id);
        })->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_trucheck_status')
                ->where('meta_value', 'Truchecked');
        })->where('publish_status', 'PUBLISHED')->count();
        $data['bayut_verified_count'] = Property::whereHas('portals', function ($q) use ($bayut_portal) {
            $q->where('portal_id', $bayut_portal->id);
        })->whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'bayut_review_status')
                ->where('meta_value', 'Approved');
        })->where('publish_status', 'PUBLISHED')->count();

        $data['dubizzle_count'] = Property::whereHas('portals', function ($q) use ($dubizzle_portal) {
            $q->where('portal_id', $dubizzle_portal->id);
        })->where('publish_status', 'PUBLISHED')->count();

        $data['dubizzle_trucheck'] = Property::whereHas('propertyMeta', function ($query) {
            $query->where('meta_key', 'dubizzle_trucheck_status')
                ->where('meta_value', 'Truchecked');
        })->whereHas('portals', function ($q) use ($dubizzle_portal) {
            $q->where('portal_id', $dubizzle_portal->id);
        })->where('publish_status', 'PUBLISHED')->count() ?? 0;

        return $this->sendResponse(true, 'Lead Count Fetched', $data, 200);


    }

    public function propertyFinderAuth($refresh_token = null)
    {

        try {

            if ($refresh_token != null || $refresh_token != '') {

                $res = Http::post('https://api-v2.mycrm.com/token', [
                    "grant_type" => "refresh_token",
                    "domain" => "axc",
                    "scope" => "offline",
                    "refresh_token" => $refresh_token,
                ]);
                $data = $res->json();
                $response_code = $res->status();
                if ($response_code == 200 || $response_code == 201) {
                    return $data;
                } else {
                    return [];
                }
            }else {

                $res = Http::post('https://api-v2.mycrm.com/token', [
                    "grant_type" => "password",
                    "domain" => "axc",
                    "username" => "l.gapunuan@axcapital.ae",
                    "password" => "Liz@12345!",
                    "scope" => "offline"
                ]);
                $data = $res->json();
                $response_code = $res->status();
                if ($response_code == 200 || $response_code == 201) {
                    Cache::put('pf_refresh_token_mycrm', $data['refresh_token'], now()->addMinutes(60));
                    return $data;
                } else {
                    return [];
                }
            }
        } catch (\Exception $e) {

            return [];
        }

    }

    private function getPfAgentData()
    {
        try {
            $refresh_token = Cache::get('pf_refresh_token_mycrm') ?? '';
            $tokens = $this->propertyFinderAuth($refresh_token);

            if (isset($tokens['access_token'])) {
                $access_token = $tokens['access_token'];
                $res = Http::withHeaders([
                    'authorization' => 'Bearer ' . $access_token
                ])->get('https://api-v2.mycrm.com/stats/agents/breakdown-statistics');
                $data = $res->json();
                Cache::put('pf_agent_status', $data, now()->addMinutes(120));
                return $data;
            } else {
                return [];
            }
        }catch (\Exception $e){
            print_r($e->getMessage());
            exit();
            return [];
        }
    }

    public function pfAgentStatus()
    {

        if(Cache::has('pf_agent_status')){
            $data = Cache::get('pf_agent_status');
            return $this->sendResponse(true, 'Agent Status Fetched from cache', $data, 200);
        }
        $data = $this->getPfAgentData();
        if(!isset($data['error']) || $data == []){
            return $this->sendResponse(false, 'something went wrong', [], 400);
        }
        Cache::put('pf_agent_status', $data, now()->addMinutes(120));
        return $this->sendResponse(true, 'Agent Status Fetched from live', $data, 200);
    }

    private function getBayutAgencyTruCheckRank()
    {
        $url = 'https://www.bayut.com/agentportal/';
        $dom = new \DOMDocument();
        @$dom->loadHTMLFile($url);

        $names = [];


        $elements = $dom->getElementsByTagName('*'); // Get all elements

        $namesFound = 0;

        foreach ($elements as $element) {
            if ($element->getAttribute('class') == 'agency-name') {
                $names[] = trim($element->nodeValue);
                $namesFound++;

                //get only dubai Enterprise
                if ($namesFound == 5) break;
            }
        }

        $countFound = 0;
        $counts = [];

        foreach ($elements as $element) {
            if ($element->getAttribute('class') == 'agency-monthly-activations') {
                $counts[] = $element->getAttribute('data-count');
                $countFound++;

                //get only dubai Enterprise
                if ($countFound == 5) break;
            }
        }

        $logoFound = 0;
        $logos = [];

        $imageTags = $elements = $dom->getElementsByTagName('*'); // Get all elements

        foreach ($elements as $element) {
            if ($element->getAttribute('class') == 'agnecy-logo') {
                $dom2 = new \DOMDocument();
                @$dom2->loadHTML($element->ownerDocument->saveHTML($element));

                $imageElements = $dom2->getElementsByTagName('img');

                foreach ($imageElements as $eleImg) {
                    $logos[] = $eleImg->getAttribute('data-src') == '' ? $eleImg->getAttribute('src') : $eleImg->getAttribute('data-src');
                    break;
                }

            }
        }

        $result = [];

        for ($i = 0; $i < count($names); $i++) {
            $result[] = [
                'name' => $names[$i],
                'count' => $counts[$i],
                'logo' => $logos[$i],

            ];
        }


//        $data = [
//            'agency_name' => $names,
//            'agency_count' => $counts,
//            'agency_logo' => $logos
//        ];
        return $result;


    }

    public function bayutAgentStatus()
    {
        //Cache::forget('bayut_agency_status');
        if (Cache::has('bayut_agency_status')) {
            $data = Cache::get('bayut_agency_status');
            return $this->sendResponse(true, 'Agent Status Fetched from cache', $data, 200);
        } else {
            $data = $this->getBayutAgencyTruCheckRank();
            Cache::put('bayut_agency_status', $data, now()->addMinutes(60));
            return $this->sendResponse(true, 'Agent Status Fetched from live', $data, 200);
        }

    }
}
