<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PropertyFinder\V1\WebhookApiController;
use App\Http\Controllers\Api\PropertyFinder\V1\WebhookReceiverController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

//create a throttle middleware
//Route::middleware('throttle:60,1')->group(function () {
//    Route::get('users', function () {
//        return \App\Models\User::all();
//    });
//});

// Route::match(["get", "post"], "/update-user/{id}", function(){
//     return 'Hello';
// });

Route::get('/get-locations', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getLocationList"])->name('getLocationList');
Route::get('/get-beds', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getBedList"])->name('getBedList');
Route::get('/get-baths', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getBathList"])->name('getBathList');
Route::get('/get-categories', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getCategoryList"])->name('getCategoryList');
Route::get('/get-amenities', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getAmenitiesList"])->name('getAmenitiesList');
Route::get('/get-views', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getViews"])->name('getViews');;
Route::get('/get-types', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getTypeList"])->name('getTypeList');
Route::post('/matching-listing', [\App\Http\Controllers\Api\V1\MatchingListing\MatchingListingApiController::class, 'matchingListing'])->name('api.matchingListing');
Route::post('/send-listing-req-into-telegram',  [\App\Http\Controllers\Api\V1\MatchingListing\MatchingListingApiController::class, 'sendListingRequirementIntoTelegram']);
Route::get('/get-listing-details', [\App\Http\Controllers\Api\V1\MatchingListing\MatchingListingApiController::class, 'listingDetail'])->name('api.listingDetail');
Route::get('/search-my-listing', [\App\Http\Controllers\Api\V1\MatchingListing\MatchingListingApiController::class, 'searchMyListing'])->name('api.searchMyListing');
Route::post('/create-listing', [\App\Http\Controllers\Api\V1\MatchingListing\MatchingListingApiController::class, 'createListing'])->name('api.createListing');
Route::post('/send-listings-to-user', [\App\Http\Controllers\Api\V1\MatchingListing\MatchingListingApiController::class, 'sendListingsToUser'])->name('sendListingsToUser');
Route::post('/send-listings-to-telegram', [\App\Http\Controllers\Api\V1\MatchingListing\MatchingListingApiController::class, 'sendListingsToTelegramGroup'])->name('sendListingsToTelegramGroup');
Route::post('/telegram/webhook', [\App\Http\Controllers\Api\TelegramController::class, 'handleWebhook']);
Route::get('/sendTelegramButtonsToGroup', [\App\Http\Controllers\Api\TelegramController::class, 'sendTelegramButtonsToGroup']);


Route::get("/create-lead-on-bitrix/{id}", [\App\Http\Controllers\Api\V1\Lead\LeadApiController::class, "createLeadOnBitrix"]);
Route::get("/sync-lead-bitrix-response", [\App\Http\Controllers\Api\V1\Lead\LeadApiController::class, "syncLeadBitrixResponse"]);
Route::get("/delete-lead-bitrix-response", [\App\Http\Controllers\Api\V1\Lead\LeadApiController::class, "deleteLeadBitrixResponse"]);

Route::post('/update-profile-picture', [\App\Http\Controllers\Api\ExternalApiController::class, 'revertUserProfile']);
Route::get('/regions', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getRegions"]);
Route::get('/communities', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getCommunities"]);
Route::get('/sub-communities', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getSubCommunities"]);
Route::get('/community-properties', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getCommunityProperty"]);
Route::get('/all-parameters', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getAllParameters"]);

Route::post('/property-finder-lead-created', [\App\Http\Controllers\Api\ExternalApiController::class, 'propertyFinderLeadCreated']);

// Route::middleware('auth:sanctum')->get('/user-data', [App\Http\Controllers\Bitrix\BitrixController::class, 'getUserData']);
// Route::post('/bitrix-webhook', [App\Http\Controllers\Bitrix\BitrixWebhookController::class, 'handleWebhook']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/telegram/webhook', [\App\Http\Controllers\TelegramBotController::class, 'webhook']);
Route::get('/update-save-my-call-user', [\App\Http\Controllers\Api\ExternalApiController::class, 'updateSaveMyCallUser']);



Route::prefix('v1')->group(function () {
    Route::post('login', [App\Http\Controllers\Api\V1\Auth\AuthApiController::class, 'login']);
    Route::post('save-login-info', [App\Http\Controllers\Api\V1\Auth\AuthApiController::class, 'saveLoginInfo']);
    Route::post('forget-password', [App\Http\Controllers\Api\V1\Auth\AuthApiController::class, 'forgetPassword']);
    Route::post('verify-otp', [App\Http\Controllers\Api\V1\Auth\AuthApiController::class, 'verifyOtp']);
    Route::post('set-new-password', [App\Http\Controllers\Api\V1\Auth\AuthApiController::class, 'setNewPassword']);

    Route::post('agent-status', [App\Http\Controllers\Api\V1\Auth\AuthApiController::class, 'checkAgentStatus']);
    Route::prefix('firebase')->group(function () {
        Route::post('token-expired', [\App\Http\Controllers\Api\V1\Firebase\FirebaseApiController::class, 'tokenExpiredSendLogin']);
    });
    Route::post('verify-token', [App\Http\Controllers\Api\V1\TwoFactorAuthentication\TelegramAuthController::class, 'verifyToken']);
    Route::post('resend-otp', [App\Http\Controllers\Api\V1\TwoFactorAuthentication\TelegramAuthController::class, 'resentOtp']);
});


Route::post('mobile-app-dump', [\App\Http\Controllers\MobileApplicationDumpController::class, 'store']);
Route::post('store-zapier-webhook', [\App\Http\Controllers\Api\V1\Lead\LeadApiController::class, 'storeZapierWebhook']);

//
Route::prefix('formatter')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\ExternalApiController::class, 'validateNumbersContacts']);
});
Route::prefix('v1')->group(function () {
    Route::prefix('log')->group(function () {
        Route::post('store-backup', [\App\Http\Controllers\Api\V1\App\AppLogApiController::class, 'store']);
    });
    Route::prefix('ax-contacts')->group(function () {
        Route::post('auth/login', [\App\Http\Controllers\Api\V1\AxContacts\AxContactsApiController::class, 'axContactsLogin']);
    });
});


Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:agent_rate'])->group(function () {



    Route::post('firebase/battery-low-notification', [\App\Http\Controllers\Api\V1\Firebase\FirebaseApiController::class, 'BatteryLowNotification']);

    Route::prefix('2-factor')->group(function () {
        Route::post('/', [App\Http\Controllers\Api\V1\TwoFactorAuthentication\TwoFaController::class, 'generateToken']);
    });

    Route::get('logout', [App\Http\Controllers\Api\V1\Auth\AuthApiController::class, 'logout']);
    Route::prefix('leads')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\V1\Lead\LeadApiController::class, 'index']);
        Route::get('show/{id}', [\App\Http\Controllers\Api\V1\Lead\LeadApiController::class, 'show']);
        Route::post('delete', [\App\Http\Controllers\Api\V1\Lead\LeadApiController::class, 'destroy']);
        Route::post('store', [\App\Http\Controllers\Api\V1\Lead\LeadApiController::class, 'store']);
        Route::post('store-webhook', [\App\Http\Controllers\Api\V1\Lead\LeadApiController::class, 'storeWebhook']);
        Route::post('store-zapier-webhook', [\App\Http\Controllers\Api\V1\Lead\LeadApiController::class, 'storeZapierWebhook']);
        Route::post('send-lead-bitrix', [\App\Http\Controllers\Api\V1\Lead\LeadApiController::class, 'sendLeadToBitrix']);
    });

    Route::prefix('mobile/leads')->group(function () {
        Route::post('/', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'index']);
        Route::post('search', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'searchLeads']);
        Route::get('get-statuses', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'getStatuses']);
        Route::get('get-mediums', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'getMediums']);
        Route::get('get-types', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'getTypes']);
        Route::get('get-sources', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'getSources']);
        Route::post('get-properties', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'getProperties']);

        Route::post('store', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'createLead']);
        Route::post('update', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'updateLead']);
        Route::post('saveReason', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'saveReason']);
        Route::post('add-note', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'addNote']);
        Route::post('get-property-criteria', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'propertyCriteria']);
        Route::post('get-communities', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'getCommunities']);
        Route::post('get-sub-communities', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'getSubCommunities']);

        Route::post('bulk-assign-to-myself', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'bulkAssignLeadsToMyself']);
        Route::post('lead-assign-to-myself', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'assignLeadsToMyself']);
        Route::post('get-notes', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'getNotes']);
        Route::post('convert-to-leads', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'leadToListing']);
        Route::post('get-call-logs', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'getCallLog']);
        Route::post('get-call-logs', [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, 'getCallLog']);
        Route::post("get-all-event", [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, "getEventAllLog"]);
        Route::post("make-a-call", [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, "startCall"]);
        Route::post("archive-leads", [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, "archiveLeads"]);
        Route::post("take-archive-lead", [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, "takeArchiveLead"]);
        Route::post("add-qualified-note", [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, "addQualifiedNote"]);
        Route::post("transfer-to-portal-lead-pool", [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, "transferToPortalLeadPool"]);

    });


    Route::prefix('notifications')->group(function () {
        Route::match(["get", "post"], "/check-notification", [\App\Http\Controllers\Api\V1\Notification\NotificationApiController::class, "checkNotification"]);
        Route::match(["get", "post"], "/all-notifications", [\App\Http\Controllers\Api\V1\Notification\NotificationApiController::class, "getAllNotifications"]);
    });
    // ->middleware(['apiAuth'])
    Route::prefix('listings')->group(function () {

        Route::match(["get", "post"], "/", [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, "index"]);
        Route::match(["get", "post"], "agent-data/", [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, "filteredSearch"]);
        Route::post('/post', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'indexPost']);
        Route::post('search', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'searchListings']);

        Route::get('show/{ref_no}', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'show']);
        Route::get('view/{ref_no}', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'view']);
        Route::post('delete', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'destroy']);
        Route::post('/get-all-properties', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'getAllProperties']);
        Route::get('/get-agent-listings', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'getAgentListings']);

        Route::post('/create-listing', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'createListing']);
        Route::post('/update-listing', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'updateListing']);
        Route::post('/add-listing-note', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'addNote']);
        Route::post('/add-listing-media', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'uploadMedia']);
        Route::post('/remove-listing-media', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'removePhoto']);
        Route::post('/remove-listing-from-portal', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'removeFromPortal']);
        Route::post('/unpublish-request', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'sendUnpublishRequest']);

        Route::post('/listing-reorder', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'reOrderPhotos']);
        Route::post('/listing-duplicate', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'duplicateProperty']);

        Route::post('get-notes', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'getNotes']);
        Route::post('get-property-day-count', [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, 'getNoOfDaysListingWasPublished']);
    });

    Route::prefix('dashboard')->group(function () {
        Route::match(["get", "post"], "/", [\App\Http\Controllers\Api\V1\Dashboard\DashboardApiController::class, "dashboard"]);
        Route::match(["get", "post"], "/overview", [\App\Http\Controllers\Api\V1\Dashboard\DashboardApiController::class, "dashboardOverView"]);
        Route::match(["get", "post"], "/pie-data", [\App\Http\Controllers\Api\V1\Dashboard\DashboardApiController::class, "dashboardPieData"]);
        Route::match(["get", "post"], "/pf-data", [\App\Http\Controllers\Api\V1\Dashboard\DashboardApiController::class, "propertyFinderData"]);
        Route::match(["get", "post"], "/bayut-data", [\App\Http\Controllers\Api\V1\Dashboard\DashboardApiController::class, "bayutData"]);
        Route::match(["get", "post"], "/dubizzle-data", [\App\Http\Controllers\Api\V1\Dashboard\DashboardApiController::class, "dubbizleData"]);
        Route::match(["get", "post"], "/get-leads-data", [\App\Http\Controllers\Api\V1\Dashboard\DashboardApiController::class, "getLeads"]);
        Route::match(["get", "post"], "/get-listing-leads-data", [\App\Http\Controllers\Api\V1\Dashboard\DashboardApiController::class, "recentListingLeads"]);
    });

    Route::prefix('calender')->group(function () {
        Route::match(["get", "post"], "/", [\App\Http\Controllers\Api\V1\Calander\CalanderApiController::class, "index"]);
        Route::get('event-types', [\App\Http\Controllers\Api\V1\Calander\CalanderApiController::class, "eventTypes"]);
        Route::get('get-contacts', [\App\Http\Controllers\Api\V1\Calander\CalanderApiController::class, "getContacts"]);
        Route::get('get-leads', [\App\Http\Controllers\Api\V1\Calander\CalanderApiController::class, "getLeads"]);
        Route::get('get-properties', [\App\Http\Controllers\Api\V1\Calander\CalanderApiController::class, "getProperties"]);
        Route::post('create', [\App\Http\Controllers\Api\V1\Calander\CalanderApiController::class, "create"]);
        Route::post('delete', [\App\Http\Controllers\Api\V1\Calander\CalanderApiController::class, "delete"]);

        Route::get('generate-title', [\App\Http\Controllers\Api\V1\Calander\CalanderApiController::class, "generateTitle"]);

        Route::match(["get", "post"], "/show", [\App\Http\Controllers\Api\V1\Calander\CalanderApiController::class, "getEvent"]);
        Route::post('update', [\App\Http\Controllers\Api\V1\Calander\CalanderApiController::class, "update"]);
        Route::post('agents', [\App\Http\Controllers\Api\V1\Calander\CalanderApiController::class, "getAgents"]);


        Route::post("get-all-event", [\App\Http\Controllers\Api\V1\Calander\CalanderApiController::class, "getEventAllLog"]);
    });

    Route::prefix('listing-leads')->group(function () {
        Route::match(["get", "post"], "/", [App\Http\Controllers\Api\V1\ListingLead\ListingLeadApiController::class, "index"]);
        Route::match(["get", "post"], "search", [App\Http\Controllers\Api\V1\ListingLead\ListingLeadApiController::class, "searchListingLeads"]);
        Route::post('store', [App\Http\Controllers\Api\V1\ListingLead\ListingLeadApiController::class, "store"]);
        Route::post('update', [App\Http\Controllers\Api\V1\ListingLead\ListingLeadApiController::class, "update"]);
        Route::get('show/{id}', [App\Http\Controllers\Api\V1\ListingLead\ListingLeadApiController::class, "show"]);
        Route::post('add-note', [App\Http\Controllers\Api\V1\ListingLead\ListingLeadApiController::class, "addNote"]);
        Route::post('convert-to-listing', [App\Http\Controllers\Api\V1\ListingLead\ListingLeadApiController::class, "convertToListing"]);
        Route::post('get-notes', [App\Http\Controllers\Api\V1\ListingLead\ListingLeadApiController::class, 'getNotes']);
        Route::post('call-log', [App\Http\Controllers\Api\V1\ListingLead\ListingLeadApiController::class, 'getCallLog']);
        Route::post('save-unsuccessful-reason', [App\Http\Controllers\Api\V1\ListingLead\ListingLeadApiController::class, 'saveReason']);
    });

    Route::prefix('calls')->group(function () {
        Route::post('store', [\App\Http\Controllers\Api\V1\CallLog\CallApiController::class, 'store']);
        Route::post('uploadRecording', [\App\Http\Controllers\Api\V1\CallLog\CallApiController::class, 'uploadRecording']);
        Route::post('index', [\App\Http\Controllers\Api\V1\CallLog\CallApiController::class, 'index']);
    });



    Route::prefix('chatgpt')->group(function () {
        Route::post('/', [App\Http\Controllers\Api\V1\ChatGpt\ChatGptApiController::class, 'handle']);
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\V1\User\UserApiController::class, 'index']);
        Route::get('/paginations', [\App\Http\Controllers\Api\V1\User\UserApiController::class, 'paginateIndex']);
        Route::get('show/{id}', [\App\Http\Controllers\Api\V1\User\UserApiController::class, 'show']);
        Route::post('create', [\App\Http\Controllers\Api\V1\User\UserApiController::class, 'create']);
        Route::post('create-webhook', [\App\Http\Controllers\Api\V1\User\UserApiController::class, 'createWebhook']);
        Route::post('disable-user', [\App\Http\Controllers\Api\V1\User\UserApiController::class, 'disableUser']);
        Route::post('assign-manager', [App\Http\Controllers\Api\V1\User\UserApiController::class, 'updateManager']);
        Route::post('edit/{email}', [\App\Http\Controllers\Api\V1\User\UserApiController::class, 'editUser']);
        Route::get('roles', [\App\Http\Controllers\Api\V1\User\UserApiController::class, 'roles']);
        Route::get('traccar-client-settings', [\App\Http\Controllers\Api\V1\User\TraccarApiController::class, 'getSettings']);
        Route::get('traccar-user-settings/{id}', [\App\Http\Controllers\Api\V1\User\TraccarApiController::class, 'getUserSettings']);

        Route::post('traccar-coordinates/store', [\App\Http\Controllers\Api\V1\User\UserApiController::class, 'uploadCoordinates']);
        Route::post('store-device-info', [\App\Http\Controllers\Api\V1\User\TraccarApiController::class, 'storeDeviceInfo']);

        Route::get('agents', [\App\Http\Controllers\Api\V1\User\UserApiController::class, 'agents']);
    });

    Route::prefix('datahub')->group(function () {
        Route::get('get-beds', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getBedList"]);
        Route::get('get-baths', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getBathList"]);
        Route::get('get-types', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getTypeList"]);

        Route::post('get-lead-sources', [\App\Http\Controllers\Api\V1\Source\SourceController::class, 'getLeadSourceList']);
        Route::post('get-lead-mediums', [\App\Http\Controllers\Api\V1\Medium\MediumController::class, 'getLeadMediumList']);
        Route::post('get-lead-statuses', [\App\Http\Controllers\Api\V1\Status\StatusController::class, 'getLeadStatuses']);
        Route::post('get-user-via-id/{id}', [\App\Http\Controllers\Api\V1\User\UserApiController::class, 'getUserThroughId']);
        Route::post('get-user/{id}', [\App\Http\Controllers\Api\V1\User\UserApiController::class, 'getUserThroughBitrixId']);
        Route::post('get-leads', [\App\Http\Controllers\Api\V1\Lead\LeadApiController::class, 'getLeads']);
        Route::get('get-leads/{id}', [\App\Http\Controllers\Api\V1\Lead\LeadApiController::class, 'getLead']);
        Route::post('get-lead-types', [\App\Http\Controllers\Api\V1\Lead\LeadApiController::class, 'getLeadTypes']);
    });

    Route::prefix('locations')->group(function () {
        Route::post('regions', [\App\Http\Controllers\Api\V1\Location\LocationApiController::class, 'regions']);
        Route::post('communities', [\App\Http\Controllers\Api\V1\Location\LocationApiController::class, 'communities']);
        Route::post('sub-communities', [\App\Http\Controllers\Api\V1\Location\LocationApiController::class, 'subCommunities']);
        Route::post('community-properties', [\App\Http\Controllers\Api\V1\Location\LocationApiController::class, 'communityProperties']);
    });

    Route::prefix('bitrix')->group(function () {
        Route::post('auth/login', [\App\Http\Controllers\Api\V1\Bitrix\BitrixApiController::class, 'bitrixLogin']);
    });
    Route::prefix('contacts')->group(function () {
        Route::post('/', [\App\Http\Controllers\Api\V1\Contact\ContactsApiController::class, 'index']);
        Route::post('/agent-contacts', [\App\Http\Controllers\Api\V1\Contact\ContactsApiController::class, 'agentContacts']);
        Route::get('/contact-sources', [\App\Http\Controllers\Api\V1\Contact\ContactsApiController::class, 'contactSources']);
        Route::get('/contact-types', [\App\Http\Controllers\Api\V1\Contact\ContactsApiController::class, 'contactTypes']);
        Route::get('/contact-statuses', [\App\Http\Controllers\Api\V1\Contact\ContactsApiController::class, 'contactStatuses']);
        Route::post('/create-contact', [\App\Http\Controllers\Api\V1\Contact\ContactsApiController::class, 'createContact']);
        Route::post('/update-contact', [\App\Http\Controllers\Api\V1\Contact\ContactsApiController::class, 'updateContact']);
        Route::get('/get-contact', [\App\Http\Controllers\Api\V1\Contact\ContactsApiController::class, 'getContact']);
    });

    Route::prefix('parameters')->group(function () {
        Route::get('/nationalities', [\App\Http\Controllers\Api\V1\Parameters\CountryApiController::class, "getNationalities"]);
        Route::post('/owners', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getOwners"]);
        Route::get('/types', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getType"]); // done
        Route::get('/property-categories', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getCategory"]); // done
        Route::get('/beds', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getBeds"]); // done
        Route::get('/baths', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getBaths"]); // done
        Route::get('/payment-methods', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getPaymentMethods"]);
        Route::get('/amenities', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getAmenities"]); // done
        Route::get('/furnishing-types', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getFurnishingType"]); // done
        Route::get('/publish-statuses', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getPublishStatus"]); // done
        Route::get('/completion-statuses', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getCompletionStatus"]); // done
        Route::get('/occupancy', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getOccupancy"]); // done
        Route::get('/exclusives', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getExclusive"]); // done
        Route::get('/portals', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getPortals"]); // done
        Route::get('/views', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getViews"]); // done

        Route::get('/location/regions', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getRegions"]);
        Route::post('/location/communities', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getCommunities"]);
        Route::post('/location/sub-communities', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getSubCommunities"]);
        Route::post('/location/community-properties', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getCommunityProperty"]);
        Route::post('/location/combined-locations', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getCombinedLocation"]);


        Route::get('/listing-leads/statuses', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getListingLeadStatus"]);
        Route::get('/get-villa-types', [\App\Http\Controllers\Api\V1\Parameters\ParameterController::class, "getVillaTypes"]);
    });

    Route::prefix('reporting')->group(function () {
        Route::post('leads', [\App\Http\Controllers\Api\V1\Reporting\LeadReportingApiController::class, 'fetch'])->name('api.v1.reporting.leads');
        Route::post('listing-leads', [\App\Http\Controllers\Api\V1\Reporting\LeadReportingApiController::class, 'fetchListingLeads'])->name('api.v1.reporting.listing-leads');
    });

    Route::prefix('firebase')->group(function () {
        Route::post('store-device-token', [\App\Http\Controllers\Api\V1\Firebase\FirebaseApiController::class, 'storeDeviceToken']);
        Route::post('store-secondary-device-token', [\App\Http\Controllers\Api\V1\Firebase\FcmApiController::class, 'storeDeviceToken']);
        Route::post('dial-number', [\App\Http\Controllers\Api\V1\Firebase\FirebaseApiController::class, 'dialNumber']);
        Route::post('permission-denied', [\App\Http\Controllers\Api\V1\Firebase\FirebaseApiController::class, 'permissionDenied']);
        Route::post('lead', [\App\Http\Controllers\Api\V1\Firebase\FirebaseApiController::class, 'lead']);
    });
});

Route::prefix('xml/v2')->group(function () {
    // Route::get('/user', function (Request $request) {
    //     return 'user';
    // });
    // Portals Xml defined here
    Route::get('property-finder', [App\Http\Controllers\Api\XmlFeedController::class, 'propertyFinder']);
    Route::get('property-finder-abu-dhabi', [App\Http\Controllers\Api\XmlFeedController::class, 'propertyFinderAbuDhabi']);
    Route::get('property-finder-off-plan', [App\Http\Controllers\Api\XmlFeedController::class, 'propertyFinderOffPlan']);
    Route::get('dubizzle', [App\Http\Controllers\Api\XmlFeedController::class, 'dubizzle']);
    // Route::get('dubizzle-hourly', [App\Http\Controllers\Api\XmlFeedController::class, 'dubizzle_hourly'])->name("api.xml.dubizzle.hourly");
    Route::get('bayut', [App\Http\Controllers\Api\XmlFeedController::class, 'bayut']);
    Route::get('bayut_dubizzle', [App\Http\Controllers\Api\XmlFeedController::class, 'bayutDubizzle']);
    Route::get('bayut_dubizzle_abu_dhabi', [App\Http\Controllers\Api\XmlFeedController::class, 'bayutDubizzleAbuDhabi']);
    Route::get('privatesite', [App\Http\Controllers\Api\XmlFeedController::class, 'privatesite']);

    Route::get('genericportal', [App\Http\Controllers\Api\XmlFeedController::class, 'genericPortalUpdated']);
    Route::get('rightmove', [App\Http\Controllers\Api\XmlFeedController::class, 'rightMoveUpdated']);
    Route::get('jamesedition', [App\Http\Controllers\Api\XmlFeedController::class, 'jamesEditionUpdated']);
    Route::get('houza', [App\Http\Controllers\Api\XmlFeedController::class, 'houzaUpdated']);
    Route::get('emiratesestate', [App\Http\Controllers\Api\XmlFeedController::class, 'emiratesEstateUpdated']);
    Route::get('yandex', [App\Http\Controllers\Api\XmlFeedController::class, 'yandex']);
    Route::get('yandex-sample', [App\Http\Controllers\Api\XmlFeedController::class, 'yandex_sample']);

    Route::get('bayut-commercial', [App\Http\Controllers\Api\XmlFeedController::class, 'bayutDubizzleCommercial']);
    Route::get('property-finder-commercial', [App\Http\Controllers\Api\XmlFeedController::class, 'propertyFinderCommercial']);
});

Route::prefix('xml')->group(function () {
    Route::get('property-finder-generate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'generatePropertyFinderXMLFile']);
    Route::get('property-finder', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'getPropertyFinderXMLDataFromFile'])->name("api.xml.pf");

    Route::get('property-finder-abu-dhabi-generate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'generatePropertyFinderAbuDhabiXMLFile']);
    Route::get('property-finder-abu-dhabi', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'getPropertyFinderAbuDhabiXMLDataFromFile'])->name("api.xml.pf.abu.dhabi");

    Route::get('property-finder-off-plan-generate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'generatePropertyFinderOffPlanXMLFile']); // error
    Route::get('property-finder-off-plan', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'getPropertyFinderOffPlanXMLDataFromFile'])->name("api.xml.pf.off.plan");

    Route::get('property-finder-commercial-generate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'generatePropertyFinderCommercialXMLFile']);
    Route::get('property-finder-commercial', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'getPropertyFinderCommercialXMLDataFromFile'])->name("api.xml.pf.commercial");

    Route::get('dubizzle-generate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'generateDubizzleXMLFile']);
    Route::get('dubizzle', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'getDubizzleXMLDataFromFile'])->name("api.xml.dubizzle");

    Route::get('bayut-generate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'generateBayutXMLFile']); // taking too long
    Route::get('bayut', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'getBayutXMLDataFromFile'])->name("api.xml.bayut");

    Route::get('bayut_dubizzle-generate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'generateDubizzleBayutXMLFile']);
    Route::get('bayut_dubizzle', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'getDubizzleBayutXMLDataFromFile'])->name("api.xml.bayut_dubizzle");

    Route::get('bayut_dubizzle_abu_dhabi-generate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'generateBayutDubizzleAbuDhabiXMLFile']);
    Route::get('bayut_dubizzle_abu_dhabi', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'getBayutDubizzleAbuDhabiXMLDataFromFile'])->name("api.xml.bayut_dubizzle_abu_dhabi");

    Route::get('privatesite-generate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'generatePrivateSiteXMLFile']);
    Route::get('privatesite', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'getPrivateSiteXMLDataFromFile'])->name("api.xml.privatesite");

    Route::get('genericportal-generate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'generateGenericPortalXMLFile']);
    Route::get('genericportal', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'getGenericPortalXMLDataFromFile'])->name("api.xml.genericportal");

    Route::get('rightmove-generate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'generateRightMoveXMLFile']);
    Route::get('rightmove', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'getRightMoveXMLDataFromFile'])->name("api.xml.rightmove");

    Route::get('jamesedition-generate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'generateJamesEditionXMLFile']);
    Route::get('jamesedition', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'getJamesEditionXMLDataFromFile'])->name("api.xml.jamesedition");

    Route::get('houza-generate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'generateHouzaXMLFile']); // not working
    Route::get('houza', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'getHouzaXMLDataFromFile'])->name("api.xml.houza");

    Route::get('emiratesestate-generate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'generateEmiratesEstateXMLFile']);
    Route::get('emiratesestate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'getEmiratesEstateXMLDataFromFile'])->name("api.xml.emiratesestate");

    Route::get('yandex-generate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'generateYandexXMLFile']);
    Route::get('yandex', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'getYandexXMLDataFromFile'])->name("api.xml.yandex");

    Route::get('bayut-commercial-generate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'generateBayutDubizzleCommercialXMLFile']);
    Route::get('bayut-commercial', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'getBayutDubizzleCommercialXMLDataFromFile'])->name("api.xml.bayut.commercial");

    Route::get('move-generate', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'generateMoveXMLFile']);
    Route::get('move', [App\Http\Controllers\Api\XMLFeedV2Controller::class, 'getMoveXMLDataFromFile'])->name("api.xml.move");
});

Route::prefix('parser')->group(function () {
    Route::get('lead', [App\Http\Controllers\Api\EmailApiController::class, 'newLead']);
    Route::get('email', [App\Http\Controllers\Api\EmailParserApiController::class, 'fetchEmails']);
});
Route::prefix('call')->group(function () {
    Route::post('start', [App\Http\Controllers\Api\CallApiController::class, 'newEntry']);
    Route::post('finish', [App\Http\Controllers\Api\CallApiController::class, 'callFinish']);
});

Route::prefix('listing')->group(function () {
    Route::get('all-new', [App\Http\Controllers\Api\ListingApiController::class, 'getAll2']);
    Route::get('private-site', [App\Http\Controllers\Api\ListingApiController::class, 'privateSite']);
    Route::get('commercial-site', [App\Http\Controllers\Api\ListingApiController::class, 'commercialSite']);
    Route::get('generic-portal', [App\Http\Controllers\Api\ListingApiController::class, 'genericPortal']);

    Route::middleware('auth:sanctum')->get('generic-portal-ax-map', [App\Http\Controllers\Api\ListingApiController::class, 'genericPortalAxMap']);
    Route::get('user', [App\Http\Controllers\Api\ListingApiController::class, 'getListingUser']);
});
Route::prefix('v2')->group(function () {
    //API's without auth:sanctum. Or using custom tokens
    Route::prefix('users')->group(function () {
        Route::get('agents', [App\Http\Controllers\Api\V2\User\UserApiV2Controller::class, 'getAgents']);
    });
});
Route::prefix('v2')->middleware(['auth:sanctum', 'throttle:agent_rate'])->group(function () {
    Route::prefix('calendar')->group(function () {
        Route::get('get-leads', [\App\Http\Controllers\Api\V2\Calendar\CalendarApiV2Controller::class, "getLeads"]);
        Route::get('get-contacts', [\App\Http\Controllers\Api\V2\Calendar\CalendarApiV2Controller::class, "getContacts"]);
    });

    Route::prefix('lead')->group(function () {
        Route::get('get-properties', [App\Http\Controllers\Api\V2\Lead\LeadPropertyApiV2Controller::class, 'getProperties']);
    });
});


Route::prefix('leads')->group(function () {
    Route::post('create', [App\Http\Controllers\Api\LeadApiController::class, 'createLead']);
});

Route::prefix('thirdparty')->group(function () {
    Route::get('lead_form', [App\Http\Controllers\Api\ExternalApiController::class, 'leadForm']);
    Route::post('submit_lead', [App\Http\Controllers\Api\ExternalApiController::class, 'submitLead'])->name('api.thirdparty.submit_lead');
});
Route::prefix('whatsapp')->group(function () {
    Route::post('submit', [App\Http\Controllers\Api\WhatsappApiController::class, 'new']);
    Route::post('pf/{innerKey}', [App\Http\Controllers\Api\WhatsappApiController::class, 'pf']);
    Route::post('bayut/{innerKey}', [App\Http\Controllers\Api\WhatsappApiController::class, 'bayut']);
    Route::post('bayut/push-story-api/{innerKey}', [App\Http\Controllers\Api\WhatsappApiController::class, 'bayutPushApi']);
    Route::post('dubizzle/{innerKey}', [App\Http\Controllers\Api\WhatsappApiController::class, 'dubizzle']);
    Route::get('sampleResponse/{id}', [App\Http\Controllers\Api\WhatsappApiController::class, 'sampleResponse']);
    Route::get('sendToWebhook', [App\Http\Controllers\Api\WhatsappApiController::class, 'sendToWebhook']);

    Route::post('bayut-abu-dhabi/{innerKey}', [App\Http\Controllers\Api\WhatsappApiController::class, 'bayutAbuDhabi']);
    Route::post('dubizzle-abu-dhabi/{innerKey}', [App\Http\Controllers\Api\WhatsappApiController::class, 'dubizzleAbuDhabi']);

    Route::post('bayut-commercial/{innerKey}', [App\Http\Controllers\Api\WhatsappApiController::class, 'bayutCommercial']);
    Route::post('dubizzle-commercial/{innerKey}', [App\Http\Controllers\Api\WhatsappApiController::class, 'dubizzleCommercial']);
});

Route::prefix('rera')->group(function () {
    Route::post('checkPermit', [App\Http\Controllers\Extra\PropertyReraController::class, 'reraCheckWithQrForApi']);
    Route::post('checkPermitWithoutQr', [App\Http\Controllers\Extra\PropertyReraController::class, 'reraCheckWithoutQrForApi']);
    Route::post('checkPermitRes', [App\Http\Controllers\Extra\PropertyReraController::class, 'reraCheckResPrint']);
});

Route::prefix('testing/rera')->group(function () {
    Route::post('checkPermit', [App\Http\Controllers\Testing\ReraCheckController::class, 'reraCheckWithQrForApi']);
    Route::post('checkPermitWithoutQr', [App\Http\Controllers\Testing\ReraCheckController::class, 'reraCheckWithoutQrForApi']);
    Route::post('checkPermitRes', [App\Http\Controllers\Testing\ReraCheckController::class, 'reraCheckResPrint']);
});

Route::prefix('vue/v1')->group(function () {
    Route::get('commission-types', [\App\Http\Controllers\Api\V1\Deals\CommissionTypeApiV1Controller::class, 'index']);
    Route::get('get-deal', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getDeal'])->name('getDeal');
    Route::post('update-deal', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'updateDeal'])->name('updateDeal');
    Route::post('update-deal-state', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'updateDealState'])->name('updateDealstate');
    Route::post('update-deal-status', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'updateDealStatus'])->name('updateDealStatus');

    //commissions
    Route::post('add-deal-commissions', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'addDealCommission'])->name('deal.add.commission');
    Route::get('get-deal-commissions', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getDealCommissions'])->name('get.deal.getDealCommissions');
    Route::post('delete-deal-commission', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'deleteDealCommission'])->name('get.deal.delete.deal.commissions');
    //owner
    Route::post('update-owner-documents', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'updateContactDocuments'])->name('updateContact.docs');

    //property or Listing
    Route::get('get-property', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getProperty'])->name('get.property');

    Route::post('add-meta-data', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'addMetaData'])->name('add.meta.data');

    //leads
    Route::post('create-deal', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'storeDeal'])->name('createDeal');
    Route::post('update-deal-document', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'updateDealDocument'])->name('update.deal.document');

    Route::get('get-lead-types', [App\Http\Controllers\Admin\Lead\LeadController::class, 'getLeadTypes']);
    Route::post('delete-lead', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'deleteLead'])->name('deleteLead');
    Route::get('lead-source-list', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getLeadSourceList'])->name('vue.api.lead.source.list');
    Route::get('lead-medium-list', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getLeadMediumList'])->name('vue.api.lead.medium.list');
    Route::post('store-lead-ubo-store', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'storeCompanyUbo'])->name('vue.api.lead.company.ubo.store');
    Route::post('get-lead-ubos', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getUboList'])->name('vue.api.lead.company.ubo.list');

    Route::post('store-deal-commission-sources', [\App\Http\Controllers\Api\V1\Deals\DealCommissionSourceApiController::class, 'storeDealCommissionSource'])->name('vue.api.store.deal.commission.sources');

    Route::get('get-deal-activity-logs', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getDealActivityLogs'])->name('getDealActivityLogs');

    Route::get('get-deal-splits', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getDealSplits'])->name('get.deal.splits');
    Route::get('get-deal-split', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getDealSplit'])->name('get.deal.split');
    Route::post('create-deal-split', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'createDealSplit'])->name('deal.createDealSplit');
    Route::post('deal-split-remove', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'removeDealSplit'])->name('deal.dealSplit.remove');
    Route::post('deal-split-commission-remove', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'removeDealSplitCommission'])->name('deal.split.commission.remove');

    // Route::get('get-deal-split-commission', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class,'getDealSplitCommission'])->name('get.deal.split.commission');
    Route::get('get-deal-split-commission', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getDealSplitCommissionById'])->name('get.deal.split.commission');
    // Route::get('get-deal-commissions-calculations', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class,'getDealCommissionCalculations'])->name('get.deal.commission.calculations');
    Route::post('update-deal-split-commission', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'updateDealSplitCommission'])->name('update.deal.split.commission');
    // Route::get('get-deal-commissions-calculations1', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class,'getDealCommissionCalculationsRevamped'])->name('get.deal.commission.calculations1');
    Route::post('update-deal-commissions-calculations', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'updateDealCommissionCalculationsRevamped'])->name('update.deal.commission.calculations');
    Route::post('update-deal-commissions-calculation-manual', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'updateDealSplitCommissionManual'])->name('update.deal.split.commission.manual');
    Route::delete('delete-deal-commissions/{id}', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'deleteDealSplitCommission'])->name('delete.deal.commission');
    Route::get('get-deal-company-earnings', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getDealCommissionCalculationsRevamped'])->name('get.deal.commission.calculations');
    Route::get('get-sales-progression', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getSalesProgressions'])->name('deal.get.sales.progressions');
    Route::get('get-commission-users', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getCommissionUsers'])->name('deal.get.commission.users');
    Route::get('get-deal-progression-data', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getDealSalesProgression'])->name('get.deal.sales.progression');
    //approval
    Route::get('get-deal-split-hierarchy', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getDealSplitHierarchy'])->name('get.deal.split.hierarchy');
    Route::post('update-deal-approval', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'updateDealApproval'])->name('update.deal.approval');
    Route::post('create-deal-approval', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'createDealApproval'])->name('create.deal.approval');
    Route::post('update-deal-approval-admin', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'updateDealApprovalAdmin'])->name('update.deal.approval.admin');

    Route::get('get-deal-type', [App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getDealType']);

    Route::get('get-deal-tasks', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getDealTasks'])->name('getDealTasks');
    Route::post('store-deal-tasks', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'storeDealTask'])->name('deal.task.store');

    Route::get('get-countries', [\App\Http\Controllers\Api\V1\Parameters\CountryApiController::class, 'getCountries'])->name('getCountries');
    Route::match(["get", "post"], "leads-select2", [\App\Http\Controllers\AjaxController::class, "leadsSelect2"])->name("leads.select2");

    Route::get('get-deductible', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getDeductible'])->name('getDeductible');
    Route::get('get-deductible-type', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getDeductibleType'])->name('getDeductible.type');
    Route::post('store-deductible', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'StoreDealDeductible'])->name('StoreDealDeductible');
    Route::delete('deductible-delete/{id}', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'deleteDeductible'])->name('deductible.delete');

    Route::get('get-folder', [\App\Http\Controllers\Api\V1\Folders\FolderApiController::class, 'getFolder'])->name('getFolder');
    Route::get('get-folders', [\App\Http\Controllers\Api\V1\Folders\FolderApiController::class, 'getFolders'])->name('getFolders');
    Route::get('get-folders-list', [\App\Http\Controllers\Api\V1\Folders\FolderApiController::class, 'getFoldersList'])->name('getFoldersList');
    Route::post('store-folder', [\App\Http\Controllers\Api\V1\Folders\FolderApiController::class, 'createFolder'])->name('storeFolder');
    Route::post('update-folder', [\App\Http\Controllers\Api\V1\Folders\FolderApiController::class, 'updateFolder'])->name('updateFolder');
    Route::post('download-folder-zip', [\App\Http\Controllers\Api\V1\Folders\FolderApiController::class, 'downloadFolderZip'])->name('downloadFolderZip');
    Route::delete('delete-folder/{folder}', [\App\Http\Controllers\Api\V1\Folders\FolderApiController::class, 'deleteFolder'])->name('deleteFolder');
    Route::post('upload-files', [\App\Http\Controllers\Api\V1\Folders\FolderApiController::class, 'uploadFiles'])->name('uploadFiles');
    Route::get('get-files-list', [\App\Http\Controllers\Api\V1\Folders\FolderApiController::class, 'getFilesList'])->name('getFilesList');
    Route::get('get-file', [\App\Http\Controllers\Api\V1\Folders\FolderApiController::class, 'getFile'])->name('getFile');
    Route::get('files-list-datatable', [\App\Http\Controllers\Api\V1\Folders\FolderApiController::class, 'filesListDatatable'])->name('filesListDatatable');
    Route::delete('delete-file/{folderMedia}', [\App\Http\Controllers\Api\V1\Folders\FolderApiController::class, 'deleteFile'])->name('deleteFile');
    Route::post('generate-pdf', [\App\Http\Controllers\Api\V1\Folders\FolderApiController::class, 'generatePDF'])->name('generatePDF');
    Route::get('get-property', [\App\Http\Controllers\Api\V1\Folders\FolderApiController::class, 'getProperty'])->name('getProperty');
});
Route::post('update-deal-approval-external', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'updateDealApprovalExternal'])->name('update.deal.approval.external');

Route::prefix('webhook')->group(function () {
    Route::post('save-call-log', [App\Http\Controllers\Webhook\CallStoreWebhookController::class, 'handle']);
    Route::post('dial-a-number', [App\Http\Controllers\Webhook\CallStoreWebhookController::class, 'dialANumber']);

    Route::post('ax-contacts/save-listing-leads', [App\Http\Controllers\Webhook\ListingLeadsWebhookController::class, 'handle']);
    Route::post('ax-contacts/bulk/save-listing-leads', [App\Http\Controllers\Webhook\ListingLeadsWebhookController::class, 'handleBulk']);

    Route::post('offplan-properties-data', [App\Http\Controllers\Webhook\OffplanPropertiesWebhookController::class, 'handle']);
    Route::get('get-offplan-properties-data', [App\Http\Controllers\Webhook\OffplanPropertiesWebhookController::class, 'show']);

    Route::post('marketing-campaign-lead', [App\Http\Controllers\Webhook\LeadWebhookController::class, 'handle']);

    //below webhook is for updating pf token coming from our external server
    Route::post('update/pf/token', [App\Http\Controllers\Api\ExternalApiController::class, 'PfTokenUpdate'])->name('pf.token.update');

    Route::any('bitrix/tour', [App\Http\Controllers\Webhook\BitrixWebhookController::class, 'bitrixTourLog'])->name('bitrix.webhook.dump');
});
Route::prefix('ax')->group(function () {
    Route::match(["get", "post"], "/get-listings", [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, "getAxListings"]);
    Route::match(["get", "post"], "/get-listing/{id}", [\App\Http\Controllers\Api\V1\Listing\ListingApiController::class, "getAxListing"]);
    Route::match(["get", "post"], "/get-leads", [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, "getAxLeads"]);
    Route::match(["get", "post"], "/get-lead/{id}", [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, "getAxLead"]);
    Route::post("/update-lead", [\App\Http\Controllers\Api\V1\Lead\LeadApiController::class, "updateAxLead"]);
    Route::match(["get", "post"], "/get-contacts", [\App\Http\Controllers\Api\V1\Lead\LeadMobileApiController::class, "getAxContacts"]);
    Route::match(["get", "post"], "/get-groups", [\App\Http\Controllers\Api\V1\Group\GroupController::class, "getGroups"]);
    Route::match(["get", "post"], "/update-user/{id}", [\App\Http\Controllers\Api\V1\Group\GroupController::class, "updateUser"]);

});

Route::prefix('pfv1')->group(function () {
//pf new API V1 related routes
    Route::any('/webhooks/propertyfinder', [WebhookReceiverController::class, 'handle']);

    //Route::middleware('pf.secret')->group(function () {
        Route::apiResource('webhook-logs', \App\Http\Controllers\Api\PropertyFinder\V1\WebHookLogApiController::class)
            ->only(['index', 'show', 'update', 'destroy'])
            ->names([
                'index' => 'pf.v1.webhook-logs.index',
                'show' => 'pf.v1.webhook-logs.show',
                'update' => 'pf.v1.webhook-logs.update',
                'destroy' => 'pf.v1.webhook-logs.destroy',
            ]);
        Route::get('/webhooks', [WebhookApiController::class, 'index']);
        Route::get('/subscribe-to-event/{eventId}/{secret?}', [WebhookApiController::class, 'store']);
        Route::get('/delete-event-subscription/{eventId}', [WebhookApiController::class, 'delete']);
        Route::get('/subscribe-to-all-events/{secret?}', [WebhookApiController::class, 'subscribeToAll']);
   // });
});
