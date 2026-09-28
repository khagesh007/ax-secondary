<?php

use App\Http\Controllers\Api\V1\Folders\FolderApiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Route::prefix('api/thirdparty')->group(function () {

    Route::get('get-mediums', [App\Http\Controllers\Api\ExternalApiController::class, 'getMediums']);
    Route::get('get-sources', [App\Http\Controllers\Api\ExternalApiController::class, 'getSources']);
  

    Route::get('lead_form', [App\Http\Controllers\Api\ExternalApiController::class, 'leadForm']);
    Route::post('submit_lead', [App\Http\Controllers\Api\ExternalApiController::class, 'submitLead'])->name('api.thirdparty.submit_lead');



    Route::get('encryption', [App\Http\Controllers\Api\ExternalApiController::class, 'encryption']);
    Route::get('decryption', [App\Http\Controllers\Api\ExternalApiController::class, 'decryption']);
    Route::post('archive-lead', [App\Http\Controllers\Api\ExternalApiController::class, 'archiveLead'])->name('api.thirdparty.archiveLead');


    Route::get('ajax-get-lead', [App\Http\Controllers\Api\ExternalApiController::class, 'getAjaxLead'])->name('getAjaxLead');
    Route::get('lead/assignment/{lead}/{manager}', [App\Http\Controllers\Api\ExternalApiController::class, 'leadAssignment'])->name('api.thirdparty.leadAssignment');
    Route::post('lead/assignment', [App\Http\Controllers\Api\ExternalApiController::class, 'leadAssignmentPost'])->name('api.thirdparty.leadAssignmentPost');
    Route::get('lead/update', [App\Http\Controllers\Api\ExternalApiController::class, 'leadEdit'])->name('api.thirdparty.lead_update');
    Route::post('ajax/lead/update', [App\Http\Controllers\Api\ExternalApiController::class, 'leadUpdate'])->name('api.thirdparty.update.lead');
    Route::post('ajax/lead/status/update', [App\Http\Controllers\Api\ExternalApiController::class, 'leadUpdateStatus'])->name('api.thirdparty.update.lead.status');

    //Match Listing Vue
    Route::get('matching', [App\Http\Controllers\Api\ExternalApiController::class, 'matching']);
    Route::get('match-listings', [App\Http\Controllers\Api\ExternalApiController::class, 'matchListings']);
    Route::get('search-listings', [App\Http\Controllers\Api\ExternalApiController::class, 'searchListings']);
    Route::get('match-listings-form', [App\Http\Controllers\Api\ExternalApiController::class, 'showMatchListingsForm'])->name('api.thirdparty.match-listings-form');
    Route::get('search-listings-form', [App\Http\Controllers\Api\ExternalApiController::class, 'showSearchListingsForm'])->name('api.thirdparty.search-listings-form');

    Route::get('listing-lead/assignment/{lead}/{manager}', [App\Http\Controllers\Api\ExternalApiController::class, 'listingLeadAssignment'])->name('api.thirdparty.listingLeadAssignment');
    Route::post('listing-lead/assignment', [App\Http\Controllers\Api\ExternalApiController::class, 'listingLeadAssignmentPost'])->name('api.thirdparty.listingLeadAssignmentPost');
    Route::post('listings-lead/store', [App\Http\Controllers\Api\ExternalApiController::class, 'storeListingLead'])->name('api.thirdparty.lead.listing.store');
    Route::get('listings-lead/create', [App\Http\Controllers\Api\ExternalApiController::class, 'ListingLeadCreate'])->name('rawListing');


    Route::get('fetch_pf', [App\Http\Controllers\Api\ExternalApiController::class, 'fetchPFData'])->name('api.thirdparty.fetch_pf');
    Route::get('fetch_bayut', [App\Http\Controllers\Api\ExternalApiController::class, 'fetchBayutData'])->name('api.thirdparty.fetch_bayut');


    Route::group(["prefix" => "dashboard", "as" => "admin.dashboard."], function () {
        Route::get('dashboard-decider', [App\Http\Controllers\Admin\Dashboard\DashboardController::class, 'dashboardDecider'])->name('dashboard.decider');
    });


    Route::get('import/bayut/agent-insight', [App\Http\Controllers\Api\ExternalApiController::class, 'bayutAgentInsight']);
    Route::post('import/bayut/agent-insight-file', [App\Http\Controllers\Extra\ExcelimportController::class, 'importBayutAgentInsight'])->name('import.bayut.agent.insight');

    Route::get('update/pf/token', [App\Http\Controllers\Api\ExternalApiController::class, 'PfToken']);
    Route::POST('update/pf/token/update', [App\Http\Controllers\Api\ExternalApiController::class, 'PfTokenUpdate'])->name('pf.token.update');

    // Route::get('update/bayut/token', [App\Http\Controllers\Api\ExternalApiController::class, 'bayutToken']);
    // Route::post('update/bayut/token/post', [App\Http\Controllers\Api\ExternalApiController::class, 'bayutTokenUpdate'])->name('bayut.token.update');
    // Route::get('bayut/', [App\Http\Controllers\Api\ExternalApiController::class, 'getMetaData']);
    // Route::get('delete/bayut/', [App\Http\Controllers\Api\ExternalApiController::class, 'deleteMetaData']);

    // for fetching use meta
    // Route::get('bayut/user-meta', [App\Http\Controllers\Api\ExternalApiController::class, 'bayutSecondaryUsersMeta']);
    // Route::get('dubizzle/user-meta', [App\Http\Controllers\Api\ExternalApiController::class, 'dubizzleSecondaryUsersMeta']);

    // Route::get('bayut/broker-meta', [App\Http\Controllers\Api\ExternalApiController::class, 'bayutTruBrokerApi']);

    // Route::get('get-bayut-data', [App\Http\Controllers\Api\ExternalApiController::class, 'getBayutData']);
    // Route::get('dashboard-test', [App\Http\Controllers\Api\ExternalApiController::class, 'dashboardTest']);
    // Route::get('dubizzle-dashboard-test', [App\Http\Controllers\Api\ExternalApiController::class, 'dubizzledashboardTest']);
    // Route::get('show-pf-data', [App\Http\Controllers\Api\ExternalApiController::class, 'fetchPFData']);
    // Route::get('show-bayut-data', [App\Http\Controllers\Api\ExternalApiController::class, 'fetchBayutData']);
    // Route::get('show-dubizzle-data', [App\Http\Controllers\Api\ExternalApiController::class, 'fetchDubizzleData']);
    // Route::get('check-dubizzle-data', [App\Http\Controllers\Api\ExternalApiController::class, 'checkDubizzleData']);

    // Route::get('pftest', [App\Http\Controllers\Api\ExternalApiController::class, 'pftest']);
    // Route::get('dubizzleApi', [App\Http\Controllers\Api\ExternalApiController::class, 'dubizzleApi']);



});
