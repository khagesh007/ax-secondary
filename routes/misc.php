<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('reporting-of-leads', [App\Http\Controllers\Admin\Report\LeadReportController::class, 'getLeadsStatusReport'])->name('public.getLeadsStatusReport');
// Route::get('reporting-of-agents', [App\Http\Controllers\Admin\Report\LeadReportController::class, 'agentLeadReport'])->name('public.agentLeadReport');
// Route::get('reporting-to-manager', [App\Http\Controllers\Admin\Report\LeadReportController::class, 'managerLeadReport'])->name('public.managerLeadReport');
// Route::get('contact-number-formatter', [App\Http\Controllers\Admin\Contact\ContactController::class, 'contactNumberFormatter'])->name('public.contactNumberFormatter');

// Route::get('bayut-missing-leads', [App\Http\Controllers\Testing\BayutLeadsFetchJobController::class, 'handleAbuDhabi'])->name('public.handleAbuDhabi');
Route::group(["prefix" => "dubizzle", "as" => "dubizzle."], function () {
    Route::get('handle', [App\Http\Controllers\Dubizzle\DubizzleLeadImportController::class, 'handle']);
});
Route::group(["prefix" => "bayut", "as" => "bayut."], function () {
    Route::get('handle', [App\Http\Controllers\Testing\BayutLeadsFetchJobController::class, 'handle']);
});

Route::group(["prefix" => "noti", "as" => "noti."], function () {
    Route::get('send-empty/{token}', [App\Http\Controllers\Api\ExternalApiController::class, 'emptyNoti']);
});

Route::group(["prefix" => "pf", "as" => "pf."], function () {
    Route::get('whatsapp', [App\Http\Controllers\Api\ExternalApiController::class, 'whatsappPFLeads']);
});

Route::group(["prefix" => "portal-testings", "as" => "portals-tesing."], function () {
    Route::get('portals', [App\Http\Controllers\Api\ExternalApiController::class, 'portalTesting']);
});


Route::group(["prefix" => "leads", "as" => "leads."], function () {
    Route::get('filtered-data', [App\Http\Controllers\Admin\Lead\LeadController::class, 'leadExport'])->middleware('auth')->name('leadExport');
});

Route::get('fetch-bitrix-users', [App\Http\Controllers\Testing\BitrixUserController::class, 'fetchBitrixUsers'])->middleware('auth')->name('public.fetchBitrixUsers');
Route::get('update-deals-bitrix-users', [App\Http\Controllers\Testing\BitrixUserController::class, 'updateDealInfoInUsers'])->middleware('auth')->name('public.updateDealInfoInUsers');
Route::get('update-report-exceptions-users', [App\Http\Controllers\Testing\BitrixUserController::class, 'updateExemption'])->middleware('auth')->name('public.updateExemption');

Route::group(["prefix" => "testing", "as" => "testing."], function () {
    Route::get('testing-ax-acdemey', function (Request $request) {
        if ($request->uuid != 'frnbss8ndk9w2nu7ki8yc') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return axAcademyUrl($request->email);
    })->middleware('auth');

    Route::get('testing-property-count', function (Request $request) {
        $user_id = 23;
        $childrenIds = getUserChildrenIds(23);
        $childrenIds[] = 23;
        $property_count = \App\Models\Property::whereIn('agent_id', $childrenIds)
            ->where('community_id', 82)
            ->count();
        return $property_count;

    })->middleware('auth');

    Route::get('testing-property-count-count', function (Request $request) {
        $agent = \App\Models\User::find(23);

        $notAdmin = $agent->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);

        $agentsQuery = \App\Models\User::where('status', true)
            ->whereHas("roles", function ($q) {
                $q->whereIn("name", ["Agent", "Sales Director", "Team Leader", "Line Manager"]);
            });

        if ($notAdmin) {
            $childrenIds = getUserChildrenIdsByID($agent->id);
            $agentsQuery->whereIn("id", $childrenIds);
        }

        $agents = $agentsQuery->get();

        $childrenIds = $agents->pluck('id')->toArray();

        $property_count = \App\Models\Property::

        whereHas('subCommunity', function ($q) {
                $q->where('community_id', 98);
        })
        ->where('community_id',98)
        ->whereIn('agent_id', [26])
        ->count();

        $property_community = \App\Models\Property::
        whereHas('communityProperty', function ($q) {
            $q->where('community_id', 98);
        })
        ->whereHas('subCommunity', function ($q) {
                $q->where('community_id', 98);
        })
        ->
        where('community_id',98)
        ->whereIn('agent_id', [26])
        ->count();

        dd($property_count,$property_community);
    })->middleware('auth');



    Route::get('testing-property-duplicate', function (Request $request){
        $duplicateRecords = \App\Models\CommunityProperty::select('name', 'sub_community_id', DB::raw('COUNT(*) as count'))
    ->groupBy('name', 'sub_community_id')
    ->having('count', '>', 1)
    ->get();

        dd($duplicateRecords);
    });


    Route::get('/ta', function(){

        $properties = App\Models\Property::get();
        foreach($properties as $property){
            $firstPublished = $property->firstpublish()->first()?->created_at?->toDateTimestring() ?? null;
            $property->test_publish_date = $firstPublished;
            $property
            ->withoutApproval()
            ->save();
        }






    });

    Route::get('bayut-data-check', function (Request $request) {
        $curl = curl_init();
        $start = 0;
        $total_records_to_fetch = 1000;
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://fenix-data-es2.dubizzle.com/_msearch?filter_path=took%2C*.took%2C*.suggest.*.options.text%2C*.suggest.*.options._source.*%2C*.hits.total.*%2C*.hits.hits._source.*%2C*.hits.hits._score%2C*.hits.hits.highlight.*%2C*.error%2C*.aggregations.*.buckets.key%2C*.aggregations.*.buckets.doc_count%2C*.aggregations.*.buckets.complex_value.hits.hits._source%2C*.aggregations.*.filtered_agg.facet.buckets.key%2C*.aggregations.*.filtered_agg.facet.buckets.doc_count%2C*.aggregations.*.filtered_agg.facet.buckets.complex_value.hits.hits._source',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"index":"dld_transaction_details_prod_alias"}
{"from":'.$start.',"size":'.($total_records_to_fetch).',"track_total_hits":true,"query":{"bool":{"must":[{"range":{"date_transaction_epoch_nk":{"gte":1272657600}}}]}},"sort":[{"date_transaction_nk":{"order":"asc"}},{"transaction_sk":{"order":"asc"}}]}
',
            CURLOPT_HTTPHEADER => array(
                'authority: fenix-data-es2.dubizzle.com',
                'accept: */*',
                'accept-language: en-US,en;q=0.9',
                'authorization: Basic aW5kZXhfcmVhZF91c2VyX2JheXV0Oj9xIXJ0Mlsyd3prWiFOYyY=',
                'content-type: application/x-ndjson',
                'origin: https://www.bayut.com',
                'referer: https://www.bayut.com/',
                'sec-ch-ua: "Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Linux"',
                'sec-fetch-dest: empty',
                'sec-fetch-mode: cors',
                'sec-fetch-site: cross-site',
                'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

        dd($property_count,$property_community);
    })->middleware('auth');

});
