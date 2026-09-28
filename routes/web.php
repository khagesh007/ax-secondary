<?php

use App\Http\Controllers\Admin\Listing\ListingVerificationController;
use App\Http\Controllers\Api\V1\Folders\FolderApiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Http\Controllers\Admin\Pipeline\PipelineController;
use App\Http\Controllers\Admin\Pipeline\PipelineDashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

//  Route::get('get-contacts', [App\Http\Controllers\Api\ExternalApiController::class, 'getContacts']);
//  Route::get('get-contacts-ids', [App\Http\Controllers\Api\ExternalApiController::class, 'getContactsId']);


Route::post('/bot/webhook', function (Request $request) {
    $update = json_decode($request->getContent(), true);

    if (isset($update['message'])) {
        $chatId = $update['message']['chat']['id'];                    // ✅ Supergroup chat ID
        $groupTitle = $update['message']['chat']['title'] ?? 'N/A';
        $threadId = $update['message']['message_thread_id'] ?? null;  // ✅ Topic ID (optional)

        // Log::info('Telegram Webhook Received:', [
        //     'chat_id' => $chatId,
        //     'group_title' => $groupTitle,
        //     'thread_id' => $threadId,
        //     'full_update' => $update
        // ]);


    } else {
        // Log::warning('Telegram webhook received but no message found.', ['update' => $update]);
    }

    return response('OK');
});



Route::any('/telegram/webhook', function () {
    $update = Telegram::getWebhookUpdate();
    // Log::channel('bitrix_alert')->info('Telegram Webhook Hit:', $update->toArray());

    if ($update->isType('callback_query')) {
        $callback = $update->getCallbackQuery();
        $user = $callback->getFrom();
        $chatId = $callback->getMessage()->getChat()->getId();
        $data = $callback->getData();

        // Log::channel('bitrix_alert')->info("User: {$user->getUsername()} clicked: $data");

        Telegram::answerCallbackQuery([
            'callback_query_id' => $callback->getId(),
            'text' => 'Thanks for clicking!',
        ]);
    }

    return 'ok';
});

Route::get('/docusign/send-template', [App\Http\Controllers\DocuSignController::class, 'sendDocumentWithTemplate']);
Route::get('/docusign/send', [App\Http\Controllers\DocuSignController::class, 'sendDocument']);
Route::get('/docusign/envolope-data', [App\Http\Controllers\DocuSignController::class, 'getEnvolopeFormData']);
Route::get('/docusign/refresh', [App\Http\Controllers\DocuSignController::class, 'refreshAccessToken']);
Route::match(["get", "post"],'docusign/callback', [App\Http\Controllers\DocuSignController::class, 'callback']);
Route::get('docusign/create-auth-url', [App\Http\Controllers\DocuSignController::class, 'createOauthUrl']);
Route::get('docusign/signDocument', [App\Http\Controllers\DocuSignController::class, 'signDocument'])->name('sign.document');
Route::get('docusign', [App\Http\Controllers\DocuSignController::class, 'docusignPage'])->name('docusign');
Route::get('get-envolope', [App\Http\Controllers\DocuSignController::class, 'getEnvolopeFormData'])->name('docusign.get.envolope');
Route::match(["get", "post"],'docusign/webhook', [App\Http\Controllers\DocuSignController::class, 'webhook'])->name('docusign.webhook');

Route::get('/regenerate-authenticator-codes', [App\Http\Controllers\Auth\LoginHelperController::class, 'regenerateQr'])->name('regenerateQr');


Route::post('/telegram-login', [App\Http\Controllers\Auth\LoginHelperController::class, 'telegramLogin'])->name('telegramLogin');
// Route::post('/telegram-login', function(Request $request){
// dd('dasdsad');
// })->name('telegramLogin');



Route::get('/complete-registration', [App\Http\Controllers\Auth\LoginController::class, 'completeRegistration'])->name('complete.registration');
Route::get('/skip-registration', [App\Http\Controllers\Auth\LoginHelperController::class, 'skipRegistration'])->name('skip.registration');
Route::get('/login-with-telegram', [App\Http\Controllers\Auth\LoginHelperController::class, 'loginWithTelegram'])->name('2fa.telegram');
Route::get('/login-with-telegram-resend-otp', [App\Http\Controllers\Auth\LoginHelperController::class, 'regenerateOtp'])->name('2fa.telegram.regenerateOtp');
Route::get('/return-back', [App\Http\Controllers\Auth\LoginHelperController::class, 'goBack'])->name('2fa.telegram.goBack');



Route::get('view/pdf/generation/{id}', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'generatePDF'])->name('generatePDF');
Route::get('view/post/generation/{id}', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'generatePOST'])->name('generatePOST');
Route::get('preview-brochure/{id}', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'generateBrochure'])->name('generateBrochure');
Route::get('brochure/{id}', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'downloadBrochure'])->name('downloadBrochure');


Route::get('fetch_property_data', [App\Http\Controllers\Api\ExternalApiController::class, 'fetchPropertyData'])->name('fetchPropertyData');


Route::get('fetch_location_data', [App\Http\Controllers\Api\ExternalApiController::class, 'fetchLocationData'])->name('fetchLocationData');

Route::get("sync-permission", [\App\Http\Controllers\TestController::class, "syncPermissions"]);
Route::impersonate();

Auth::routes([
    'register' => false,
]);
//Language Translation
Route::get('index/{locale}', [App\Http\Controllers\HomeController::class, 'lang']);
//Extra routes
// Route::get('/agent-cred', [App\Http\Controllers\ExtraController::class, 'sendCredentialsToAgent']);
Route::get('contactsLogin', [App\Http\Controllers\HomeController::class, 'contactLogin'])->name('contactLogin');
Route::get('dealLogin', [App\Http\Controllers\HomeController::class, 'dealLogin'])->name('dealLogin');
Route::get('telelogLogin', [App\Http\Controllers\HomeController::class, 'telelogLogin'])->name('telelogLogin');
Route::get('chatLogin', [App\Http\Controllers\HomeController::class, 'chatLogin'])->name('chatLogin');



Route::middleware(['2fa','tOuth','auth'])->group(function () {
    // Route::get('/', [App\Http\Controllers\HomeController::class, 'root'])->name('root');

    Route::get("/", [\App\Http\Controllers\Admin\Dashboard\DashboardV3Controller::class, "index"])->name("root");
    Route::get("/v3/agent", [\App\Http\Controllers\Admin\Dashboard\DashboardV3Controller::class, "agentDashboard"])->name("agent.root");


    Route::get("/v2", [\App\Http\Controllers\Admin\Dashboard\DashboardV2Controller::class, "index"])->name("root.v2");

    Route::get('/primary', [App\Http\Controllers\HomeController::class, 'rootPrimary'])->name('rootPrimary');
    Route::get('/commercial', [App\Http\Controllers\HomeController::class, 'rootCommercial'])->name('rootCommercial');
    Route::get('/secondary', [App\Http\Controllers\HomeController::class, 'rootSecondary'])->name('rootSecondary');
    Route::get('/abu-dhabi', [App\Http\Controllers\HomeController::class, 'rootAbuDhabi'])->name('rootAbuDhabi');
    Route::get('/chinese-team', [App\Http\Controllers\HomeController::class, 'rootChinese'])->name('rootChinese');
    Route::post('/2fa', [App\Http\Controllers\Auth\LoginController::class, 'verification'])->name('2fa');

    Route::post('/get-dashboard-supporting-data', [App\Http\Controllers\HomeController::class, 'agentGroupReporting'])->name('agentGroupReporting');

    Route::get("v2", [\App\Http\Controllers\Admin\Dashboard\DashboardV2Controller::class, "index"])->name("root.v2");
    Route::post("v2-get-view", [\App\Http\Controllers\Admin\Dashboard\DashboardV2Controller::class, "getView"])->name("root.v2.get-view");
    Route::post("v2-get-data", [\App\Http\Controllers\Admin\Dashboard\DashboardV2Controller::class, "getData"])->name("root.v2.get-data");

    Route::post('v2-agent-listing-report', [\App\Http\Controllers\Admin\Dashboard\DashboardV2Controller::class, 'agentListingReport'])->name('root.v2.agent-listing-report');
    Route::post('v2-agent-sub-communities-details', [\App\Http\Controllers\Admin\Dashboard\DashboardV2Controller::class, 'agentListingReportSubCommunity'])->name('root.v2.agent-sub-communities-details');
});

Route::get('/index2', [App\Http\Controllers\HomeController::class, 'index2'])->name('index2');
Route::get('/telegram', [App\Http\Controllers\Api\TelegramController::class, 'sendChat']);
Route::get('/telegram-get', [App\Http\Controllers\Api\TelegramController::class, 'getUpdates']);
Route::get('forget', [App\Http\Controllers\FrontController::class, 'forget'])->name('forget.password');
Route::get('code-test', [App\Http\Controllers\TestController::class, 'codeTest']);
Route::get('pdf-gent', [App\Http\Controllers\TestController::class, 'pdfgen']);
Route::get('approve-data', [App\Http\Controllers\TestController::class, 'approvalTest']);
Route::get('compare-property', [App\Http\Controllers\TestController::class, 'compareProperty']);
Route::get('download-zip-file-test', [App\Http\Controllers\TestController::class, 'downloadZipFileTest']);

//Route::get('sync-property', [App\Http\Controllers\Extra\MyCrmController::class, 'syncProperty']);
//Route::get('sync-property-single', [App\Http\Controllers\Extra\MyCrmController::class, 'syncSingleProperty']);
//Route::get('sync-users', [App\Http\Controllers\Extra\MyCrmController::class, 'syncUsers']);
Route::get('sync-leads', [App\Http\Controllers\Extra\MyCrmController::class, 'syncLeads']);
//Route::get('property-img', [App\Http\Controllers\Extra\MyCrmController::class, 'propertySaveImage']);

Route::group(["prefix" => "properties", "as" => "admin.listing.properties.", "middleware" => ["web", "checkUserStatus"]], function () {
    Route::get('view/{property:ref_no}', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'view'])->name('view');
    Route::get('print/{property:ref_no}', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'print'])->name('print');
    Route::get('listing-advertisement/{property:ref_no}', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'listingAdvertisement'])->name('listing-advertisement');
    Route::get('print-new/{property:ref_no}', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'printNew'])->name('printNew');

    Route::get('print/{property:ref_no}/new-brocher', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'newDownloadBrochure'])->name('newDownloadBrochure');


});

Route::group(["prefix" => "front", "as" => "front.", "middleware" => ["web"]], function () {
    Route::post('submit-inquiry', [App\Http\Controllers\Front\Lead\LeadController::class, 'leadFromPreview'])->name('store.inquiry');
    Route::get('status/screen1', [App\Http\Controllers\Front\Status\StatusController::class, 'screen1'])->middleware('xframe')->name('status.screen1');
    Route::get('status/screen2', [App\Http\Controllers\Front\Status\StatusController::class, 'screen2'])->middleware('xframe')->name('status.screen2');
    Route::get('status/screen3', [App\Http\Controllers\Front\Status\StatusController::class, 'screen3'])->middleware('xframe')->name('status.screen3');

    Route::get('status/screen4', [App\Http\Controllers\Front\Status\StatusController::class, 'screen4'])->name('status.screen4');
    Route::get('status/screen5', [App\Http\Controllers\Front\Status\StatusController::class, 'screen5'])->name('status.screen5');

    Route::group(["prefix" => "api", "as" => "api.", "middleware" => ["web"]], function () {
        Route::get('status/screen3', [App\Http\Controllers\Api\V1\Reporting\StatusScreenApiController::class, 'dataForScreen3'])->name('screen3');
        Route::get('status/screen2', [App\Http\Controllers\Api\V1\Reporting\StatusScreenApiController::class, 'dataForScreen2'])->name('screen2');
        Route::get('status/pf-agents', [App\Http\Controllers\Api\V1\Reporting\StatusScreenApiController::class, 'pfAgentStatus'])->name('pf.agents.status');
        Route::get('status/bayut-agents', [App\Http\Controllers\Api\V1\Reporting\StatusScreenApiController::class, 'bayutAgentStatus'])->name('bayut.agency.status');

    });
});

Route::group(["prefix" => "temp", "middleware" => ["web"]], function () {
    Route::get('bitrix/import-source', [App\Http\Controllers\Extra\BitrixController::class, 'importSource']);
    Route::get('bitrix/import-lead-sys', [App\Http\Controllers\Extra\BitrixController::class, 'importLeadFromDump']);
    Route::get('bitrix/import-lead', [App\Http\Controllers\Extra\BitrixController::class, 'importLead']);
    Route::get('bitrix/lead-delete', [App\Http\Controllers\Extra\BitrixController::class, 'deleteExisting']);
    Route::get('bitrix/source-map', [App\Http\Controllers\Extra\BitrixController::class, 'createMapOfSource']);
    Route::get('bitrix/nationality-map', [App\Http\Controllers\Extra\BitrixController::class, 'createMapNationality']);
    Route::get('bitrix/import-contact', [App\Http\Controllers\Extra\BitrixController::class, 'importContact']);
});


Route::group(["prefix" => "front", "as" => "front.", "middleware" => ["web"]], function () {


    Route::group(["prefix" => "auth", "as" => "auth.", "middleware" => ["web"]], function () {

        Route::get('listing', [App\Http\Controllers\Front\FrontListingController::class, 'listing'])->name('listing');
        Route::get('listing-details/{id}', [App\Http\Controllers\Front\FrontListingController::class, 'listingDetails'])->name('listing.details');
        Route::get('search_location', [App\Http\Controllers\Front\FrontListingController::class, 'search_location'])->name('search_location');

    });
});



//check session
Route::get('check-auth', [App\Http\Controllers\Auth\SessionController::class, 'checkSession'])->name('check.session');
Route::post('ajax-login', [App\Http\Controllers\Auth\SessionController::class, 'ajaxlogin'])->name('ajax.login');

Route::group(['middleware' => ['web', 'auth', "checkUserStatus"]], function () {
    Route::group(["prefix" => "jobs", "as" => "jobs."], function () {
        Route::get('start-jobs', [App\Http\Controllers\EmaarPropertyController::class, 'startJobs'])->name('public.startJobs');
        Route::get('import-properties', [App\Http\Controllers\EmaarPropertyController::class, 'importProperties'])->name('public.importProperties');
        Route::post('import-properties/post', [App\Http\Controllers\EmaarPropertyController::class, 'importPropertiesPOST'])->name('public.importPropertiesPOST');
        Route::post('fetch-projects', [App\Http\Controllers\EmaarPropertyController::class, 'developerProjects'])->name('public.fetchProjects');
        Route::post('fetch-listing', [App\Http\Controllers\EmaarPropertyController::class, 'fetchOffPlanListings'])->name('public.fetchListing');
        Route::get('terminate/{id}', [App\Http\Controllers\EmaarPropertyController::class, 'terminateJob'])->name('public.terminateJob');
        Route::get('emaar/create', [App\Http\Controllers\EmaarPropertyController::class, 'createOffPlanListing'])->name('public.createOffPlanListing');
        Route::get('emaar-properties', [App\Http\Controllers\EmaarPropertyController::class, 'emaar'])->name('public.emaar');
        Route::get('azizi-properties', [App\Http\Controllers\EmaarPropertyController::class, 'azizi'])->name('public.azizi');
    });
});

Route::group(['middleware' => ['web', 'auth', "checkUserStatus"]], function () {
    Route::group(["prefix" => "offplan-import", "as" => "offplan.import."], function () {
        Route::get('import', [App\Http\Controllers\Admin\Offplan\PropertyImportController::class, 'index'])->name('index');
        Route::post('import/post', [App\Http\Controllers\Admin\Offplan\PropertyImportController::class, 'handle'])->name('handle');
    });
});

Route::group(['middleware' => ['web', 'auth', "checkUserStatus","2fa",'tOuth']], function () { //only authenticated user can access this
    //Update User Details
    Route::post('/update-profile/{id}', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('updateProfile');
    Route::post('/update-password/{id}', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatePassword');

    //testing urls

    Route::get('rera-check', [App\Http\Controllers\TestController::class, 'reraCheck']);
    Route::get('rera-checkQr', [App\Http\Controllers\TestController::class, 'reraCheckQr']);
    Route::get('rera-check-json', [App\Http\Controllers\TestController::class, 'reraCheckJson']);
    //import related routes
    /*
    Route::get('import', [App\Http\Controllers\Extra\ExcelimportController::class, 'index'])->name('import.index');
    Route::post('import-landlord', [App\Http\Controllers\Extra\ExcelimportController::class, 'importLandlord'])->name('admin.import.landlord');
    Route::post('import-leads', [App\Http\Controllers\Extra\ExcelimportController::class, 'importLeads'])->name('admin.import.leads');
    Route::post('import-property', [App\Http\Controllers\Extra\ExcelimportController::class, 'importProperty'])->name('admin.import.property'); */
    Route::group(["prefix" => "import", "as" => "admin.import."], function () {
        Route::get('search-mycrm-archive', [App\Http\Controllers\Extra\MyCrmController::class, 'archiveSearch'])->name('archive.search');
        Route::post('search-mycrm-archive', [App\Http\Controllers\Extra\MyCrmController::class, 'viewSingleProperty'])->name('archive.search.mycrm');
        Route::post('mycrm-archive-import', [App\Http\Controllers\Extra\MyCrmController::class, 'importArchive'])->name('archive.mycrm.import');

    });
    //User related routes
    Route::group(["prefix" => "dashboard", "as" => "admin.dashboard."], function () {
        Route::get('pie-data', [App\Http\Controllers\Admin\Dashboard\DashboardController::class, 'dashboardPieData'])->name('pie.data');
        Route::get('pf-data', [App\Http\Controllers\Admin\Dashboard\DashboardController::class, 'propertyFinderData'])->name('pf.data');
        Route::get('listing-not-updated', [App\Http\Controllers\Admin\Dashboard\DashboardController::class, 'propertiesNotUpdatedIn'])->name('listing.not.updated');
        Route::get('overview', [App\Http\Controllers\Admin\Dashboard\DashboardController::class, 'dashboardOverView'])->name('overview');
        Route::get('get-lead-dashboard', [App\Http\Controllers\Admin\Dashboard\DashboardController::class, 'getLeads'])->name('leads');
        Route::get('notifications', [App\Http\Controllers\Admin\Dashboard\DashboardController::class, 'getNotification'])->name('notifications');
        Route::get('bayut-data', [App\Http\Controllers\Admin\Dashboard\DashboardController::class, 'bayutData'])->name('bayut.data');
        Route::get('dubizzle-data', [App\Http\Controllers\Admin\Dashboard\DashboardController::class, 'dubbizleData'])->name('dubizzle.data');
        Route::get('agent-overview-data', [App\Http\Controllers\Admin\Dashboard\DashboardController::class, 'agentListingOverview'])->name('agent.overview.data');
        Route::get('agent-lead-data', [App\Http\Controllers\Admin\Dashboard\DashboardController::class, 'agentLeadsData'])->name('agent.leads.data');
        Route::get('recent-lead-data', [App\Http\Controllers\Admin\Dashboard\DashboardController::class, 'recentLeads'])->name('recent.leads');
        Route::get('recent-lead-listing-data', [App\Http\Controllers\Admin\Dashboard\DashboardController::class, 'recentListingLeads'])->name('recent.listing.leads');
    });

    Route::group(["prefix" => "dashboard/primary", "as" => "admin.dashboard.primary."], function () {
        Route::get('pie-data', [App\Http\Controllers\Admin\Dashboard\PrimaryDashboardController::class, 'dashboardPieData'])->name('pie.data');
        Route::get('pf-data', [App\Http\Controllers\Admin\Dashboard\PrimaryDashboardController::class, 'propertyFinderData'])->name('pf.data');
        Route::get('listing-not-updated', [App\Http\Controllers\Admin\Dashboard\PrimaryDashboardController::class, 'propertiesNotUpdatedIn'])->name('listing.not.updated');
        Route::get('overview', [App\Http\Controllers\Admin\Dashboard\PrimaryDashboardController::class, 'dashboardOverView'])->name('overview');
        Route::get('get-lead-dashboard', [App\Http\Controllers\Admin\Dashboard\PrimaryDashboardController::class, 'getLeads'])->name('leads');
        Route::get('notifications', [App\Http\Controllers\Admin\Dashboard\PrimaryDashboardController::class, 'getNotification'])->name('notifications');
        Route::get('bayut-data', [App\Http\Controllers\Admin\Dashboard\PrimaryDashboardController::class, 'bayutData'])->name('bayut.data');
        Route::get('dubizzle-data', [App\Http\Controllers\Admin\Dashboard\PrimaryDashboardController::class, 'dubbizleData'])->name('dubizzle.data');
        Route::get('agent-overview-data', [App\Http\Controllers\Admin\Dashboard\PrimaryDashboardController::class, 'agentListingOverview'])->name('agent.overview.data');
        Route::get('agent-lead-data', [App\Http\Controllers\Admin\Dashboard\PrimaryDashboardController::class, 'agentLeadsData'])->name('agent.leads.data');
        Route::get('recent-lead-data', [App\Http\Controllers\Admin\Dashboard\PrimaryDashboardController::class, 'recentLeads'])->name('recent.leads');
        Route::get('recent-lead-listing-data', [App\Http\Controllers\Admin\Dashboard\PrimaryDashboardController::class, 'recentListingLeads'])->name('recent.listing.leads');
    });

    Route::group(["prefix" => "dashboard/commercial", "as" => "admin.dashboard.commercial."], function () {
        Route::get('pie-data', [App\Http\Controllers\Admin\Dashboard\CommercialDashboardController::class, 'dashboardPieData'])->name('pie.data');
        Route::get('pf-data', [App\Http\Controllers\Admin\Dashboard\CommercialDashboardController::class, 'propertyFinderData'])->name('pf.data');
        Route::get('listing-not-updated', [App\Http\Controllers\Admin\Dashboard\CommercialDashboardController::class, 'propertiesNotUpdatedIn'])->name('listing.not.updated');
        Route::get('overview', [App\Http\Controllers\Admin\Dashboard\CommercialDashboardController::class, 'dashboardOverView'])->name('overview');
        Route::get('get-lead-dashboard', [App\Http\Controllers\Admin\Dashboard\CommercialDashboardController::class, 'getLeads'])->name('leads');
        Route::get('notifications', [App\Http\Controllers\Admin\Dashboard\CommercialDashboardController::class, 'getNotification'])->name('notifications');
        Route::get('bayut-data', [App\Http\Controllers\Admin\Dashboard\CommercialDashboardController::class, 'bayutData'])->name('bayut.data');
        Route::get('dubizzle-data', [App\Http\Controllers\Admin\Dashboard\CommercialDashboardController::class, 'dubbizleData'])->name('dubizzle.data');
        Route::get('agent-overview-data', [App\Http\Controllers\Admin\Dashboard\CommercialDashboardController::class, 'agentListingOverview'])->name('agent.overview.data');
        Route::get('agent-lead-data', [App\Http\Controllers\Admin\Dashboard\CommercialDashboardController::class, 'agentLeadsData'])->name('agent.leads.data');
        Route::get('recent-lead-data', [App\Http\Controllers\Admin\Dashboard\CommercialDashboardController::class, 'recentLeads'])->name('recent.leads');
        Route::get('recent-lead-listing-data', [App\Http\Controllers\Admin\Dashboard\CommercialDashboardController::class, 'recentListingLeads'])->name('recent.listing.leads');
    });

    Route::group(["prefix" => "dashboard/secondary", "as" => "admin.dashboard.secondary."], function () {
        Route::get('pie-data', [App\Http\Controllers\Admin\Dashboard\SecondaryDashboardController::class, 'dashboardPieData'])->name('pie.data');
        Route::get('pf-data', [App\Http\Controllers\Admin\Dashboard\SecondaryDashboardController::class, 'propertyFinderData'])->name('pf.data');
        Route::get('listing-not-updated', [App\Http\Controllers\Admin\Dashboard\SecondaryDashboardController::class, 'propertiesNotUpdatedIn'])->name('listing.not.updated');
        Route::get('overview', [App\Http\Controllers\Admin\Dashboard\SecondaryDashboardController::class, 'dashboardOverView'])->name('overview');
        Route::get('get-lead-dashboard', [App\Http\Controllers\Admin\Dashboard\SecondaryDashboardController::class, 'getLeads'])->name('leads');
        Route::get('notifications', [App\Http\Controllers\Admin\Dashboard\SecondaryDashboardController::class, 'getNotification'])->name('notifications');
        Route::get('bayut-data', [App\Http\Controllers\Admin\Dashboard\SecondaryDashboardController::class, 'bayutData'])->name('bayut.data');
        Route::get('dubizzle-data', [App\Http\Controllers\Admin\Dashboard\SecondaryDashboardController::class, 'dubbizleData'])->name('dubizzle.data');
        Route::get('agent-overview-data', [App\Http\Controllers\Admin\Dashboard\SecondaryDashboardController::class, 'agentListingOverview'])->name('agent.overview.data');
        Route::get('agent-lead-data', [App\Http\Controllers\Admin\Dashboard\SecondaryDashboardController::class, 'agentLeadsData'])->name('agent.leads.data');
        Route::get('recent-lead-data', [App\Http\Controllers\Admin\Dashboard\SecondaryDashboardController::class, 'recentLeads'])->name('recent.leads');
        Route::get('recent-lead-listing-data', [App\Http\Controllers\Admin\Dashboard\SecondaryDashboardController::class, 'recentListingLeads'])->name('recent.listing.leads');
    });

    Route::group(["prefix" => "dashboard/chinese", "as" => "admin.dashboard.chinese."], function () {
        Route::get('pie-data', [App\Http\Controllers\Admin\Dashboard\ChineseDashboardController::class, 'dashboardPieData'])->name('pie.data');
        Route::get('pf-data', [App\Http\Controllers\Admin\Dashboard\ChineseDashboardController::class, 'propertyFinderData'])->name('pf.data');
        Route::get('listing-not-updated', [App\Http\Controllers\Admin\Dashboard\ChineseDashboardController::class, 'propertiesNotUpdatedIn'])->name('listing.not.updated');
        Route::get('overview', [App\Http\Controllers\Admin\Dashboard\ChineseDashboardController::class, 'dashboardOverView'])->name('overview');
        Route::get('get-lead-dashboard', [App\Http\Controllers\Admin\Dashboard\ChineseDashboardController::class, 'getLeads'])->name('leads');
        Route::get('notifications', [App\Http\Controllers\Admin\Dashboard\ChineseDashboardController::class, 'getNotification'])->name('notifications');
        Route::get('bayut-data', [App\Http\Controllers\Admin\Dashboard\ChineseDashboardController::class, 'bayutData'])->name('bayut.data');
        Route::get('dubizzle-data', [App\Http\Controllers\Admin\Dashboard\ChineseDashboardController::class, 'dubbizleData'])->name('dubizzle.data');
        Route::get('agent-overview-data', [App\Http\Controllers\Admin\Dashboard\ChineseDashboardController::class, 'agentListingOverview'])->name('agent.overview.data');
        Route::get('agent-lead-data', [App\Http\Controllers\Admin\Dashboard\ChineseDashboardController::class, 'agentLeadsData'])->name('agent.leads.data');
        Route::get('recent-lead-data', [App\Http\Controllers\Admin\Dashboard\ChineseDashboardController::class, 'recentLeads'])->name('recent.leads');
        Route::get('recent-lead-listing-data', [App\Http\Controllers\Admin\Dashboard\ChineseDashboardController::class, 'recentListingLeads'])->name('recent.listing.leads');
    });

    Route::group(["prefix" => "dashboard/abu-dhabi", "as" => "admin.dashboard.abudhabi."], function () {
        Route::get('pie-data', [App\Http\Controllers\Admin\Dashboard\AbuDhabiDashboardController::class, 'dashboardPieData'])->name('pie.data');
        Route::get('pf-data', [App\Http\Controllers\Admin\Dashboard\AbuDhabiDashboardController::class, 'propertyFinderData'])->name('pf.data');
        Route::get('listing-not-updated', [App\Http\Controllers\Admin\Dashboard\AbuDhabiDashboardController::class, 'propertiesNotUpdatedIn'])->name('listing.not.updated');
        Route::get('overview', [App\Http\Controllers\Admin\Dashboard\AbuDhabiDashboardController::class, 'dashboardOverView'])->name('overview');
        Route::get('get-lead-dashboard', [App\Http\Controllers\Admin\Dashboard\AbuDhabiDashboardController::class, 'getLeads'])->name('leads');
        Route::get('notifications', [App\Http\Controllers\Admin\Dashboard\AbuDhabiDashboardController::class, 'getNotification'])->name('notifications');
        Route::get('bayut-data', [App\Http\Controllers\Admin\Dashboard\AbuDhabiDashboardController::class, 'bayutData'])->name('bayut.data');
        Route::get('dubizzle-data', [App\Http\Controllers\Admin\Dashboard\AbuDhabiDashboardController::class, 'dubbizleData'])->name('dubizzle.data');
        Route::get('agent-overview-data', [App\Http\Controllers\Admin\Dashboard\AbuDhabiDashboardController::class, 'agentListingOverview'])->name('agent.overview.data');
        Route::get('agent-lead-data', [App\Http\Controllers\Admin\Dashboard\AbuDhabiDashboardController::class, 'agentLeadsData'])->name('agent.leads.data');
        Route::get('recent-lead-data', [App\Http\Controllers\Admin\Dashboard\AbuDhabiDashboardController::class, 'recentLeads'])->name('recent.leads');
        Route::get('recent-lead-listing-data', [App\Http\Controllers\Admin\Dashboard\AbuDhabiDashboardController::class, 'recentListingLeads'])->name('recent.listing.leads');
    });


    Route::group(["prefix" => "user", "as" => "admin.user."], function () {
        //Users routes
        Route::post('change_user_password', [\App\Http\Controllers\AjaxController::class, "changeUserPassword"])->name("change_user_password");
        Route::group(["prefix" => "users", "as" => "users."], function () {

            Route::get('user-with-no-device', [App\Http\Controllers\Admin\User\UserController::class, 'UserWithOutDevices'])->name('UserWithOutDevices');
            Route::get('user-with-no-auth-token', [App\Http\Controllers\Admin\User\UserController::class, 'UserWithOutAuthToken'])->name('UserWithOutAuthToken');
            Route::get('/', [App\Http\Controllers\Admin\User\UserController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\User\UserController::class, 'store'])->name('store');
            Route::get('view/{user}', [App\Http\Controllers\Admin\User\UserController::class, 'view'])->name('view');
            Route::get('view2/{user}', [App\Http\Controllers\Admin\User\UserController::class, 'view2'])->name('view2');
            Route::match(["get", "post"], 'dt', [App\Http\Controllers\Admin\User\UserController::class, 'usersForDatatable'])->name('dt');
            Route::get('get', [App\Http\Controllers\Admin\User\UserController::class, 'getUser'])->name('get');
            Route::post('update', [App\Http\Controllers\Admin\User\UserController::class, 'update'])->name('update');
            Route::post('update-profile', [App\Http\Controllers\Admin\User\UserController::class, 'updateProfile'])->name('update-profile');
            Route::post('update-social-media', [App\Http\Controllers\Admin\User\UserController::class, 'updateSocialMedia'])->name('update-social-media');
            Route::post('allocate-portals', [App\Http\Controllers\Admin\User\UserController::class, 'allocatePortals'])->name('allocate-portals');
            Route::post('allocate-sub-communities', [App\Http\Controllers\Admin\User\UserController::class, 'allocateSubCommunities'])->name('allocate-sub-communities');
            Route::post('assign-public-profile', [App\Http\Controllers\Admin\User\UserController::class, 'assignPublicProfile'])->name('assign-public-profile');
            Route::post('status', [App\Http\Controllers\Admin\User\UserController::class, 'updateStatus'])->name('status');
            Route::post('delete', [App\Http\Controllers\Admin\User\UserController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\User\UserController::class, 'deleteMultiple'])->name('multi.delete');
            Route::post('change-parent-id', [App\Http\Controllers\Admin\User\UserController::class, 'changeParentId'])->name('change-parent-id');
            Route::post('save-and-send-credentials', [App\Http\Controllers\Admin\User\UserController::class, 'saveAndSendCredentails'])->name('save-and-send-credentials');
            Route::post('telegram-otp-send', [App\Http\Controllers\Admin\User\UserController::class, 'sendTelegramOtp'])->name('telegram.send.otp');
            Route::post('telegram-verify-otp', [App\Http\Controllers\Admin\User\UserController::class, 'verifyTelegramOtp'])->name('telegram.verify.otp');
            Route::get('export-users-excel', [App\Http\Controllers\Admin\User\UserController::class, 'exportExcel'])->name('export-users-excel');
            Route::get('get-user-data-count', [App\Http\Controllers\Admin\User\UserController::class, 'getUserDataCount'])->name('data.count');
            Route::post('user-data-assign', [App\Http\Controllers\Admin\User\UserController::class, 'assignDataToUser'])->name('data.assign');
            Route::post('user-data-assign-deactivation', [App\Http\Controllers\Admin\User\UserController::class, 'assignDataToUserDeactivation'])->name('data.assign.deactivate');
            Route::post('update-api-access', [App\Http\Controllers\Admin\User\UserController::class, 'setApiAccess'])->name('api.access');
            Route::post('update-traccar', [App\Http\Controllers\Admin\User\UserController::class, 'setTraccarDetails'])->name('api.traccar.update');
            Route::post('update-traccar-location', [App\Http\Controllers\Admin\User\UserController::class, 'setUserLocationCordinates'])->name('traccar.location.update');
            Route::get('get-traccar-location', [App\Http\Controllers\Admin\User\UserController::class, 'getUserLocationCorrdinates'])->name('traccar.location.get');
            Route::get('get-traccar-view', [App\Http\Controllers\Admin\User\UserController::class, 'getUserLocationCordinates'])->name('traccar.location.view');
            Route::post('delete-traccar-location', [App\Http\Controllers\Admin\User\UserController::class, 'deleteUserCordinates'])->name('traccar.location.delete');
            Route::get('get-user-activity-log', [App\Http\Controllers\Admin\User\UserController::class, 'getActivityLog'])->name('activity.log');

            Route::get('permissions', [App\Http\Controllers\Admin\User\UserPermissionController::class, 'index'])->name('permissions');
            Route::post('role-permissions', [App\Http\Controllers\Admin\User\UserPermissionController::class, 'getRolePermissions'])->name('role.permissions');
            Route::post('users-permissions', [App\Http\Controllers\Admin\User\UserPermissionController::class, 'getUserPermissions'])->name('get.permissions');
            Route::post('update-permission', [App\Http\Controllers\Admin\User\UserPermissionController::class, 'updatePermissions'])->name('update-permissions');

            // view/activity/
            Route::get('view/activity/{id}', [App\Http\Controllers\Admin\User\UserController::class, 'getAllActivityLog'])->name('all.activity.log');
            Route::post('check-mobile-app-status', [App\Http\Controllers\Admin\User\UserController::class, 'checkMobileAppStatus'])->name('checkMobileAppStatus');
            Route::post('check-traccar-status', [App\Http\Controllers\Admin\User\UserController::class, 'checkTraccarStatus'])->name('checkTraccarStatus');
            Route::post('logout-from-mobile', [App\Http\Controllers\Admin\User\UserController::class, 'logoutFromMobileApp'])->name('logoutFromMobileApp');

            Route::post('user/devices', [App\Http\Controllers\Admin\User\UserController::class, 'getUserDevices'])->name('getUserDevices');
            Route::post('user/suspensions', [App\Http\Controllers\Admin\User\UserController::class, 'getUserSuspensions'])->name('getUserSuspensions');

        });

        Route::get('users-tree', [App\Http\Controllers\Admin\User\UserController::class, 'treeView'])->name('users.tree');

        Route::group(["prefix" => "roles", "as" => "roles."], function () {
            Route::get('/', [App\Http\Controllers\Admin\User\RoleController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\User\RoleController::class, 'store'])->name('store');
            Route::match(["get", "post"], 'dt', [App\Http\Controllers\Admin\User\RoleController::class, 'datatable'])->name('dt');
            Route::get('get', [App\Http\Controllers\Admin\User\RoleController::class, 'getRole'])->name('get');
            Route::post('update', [App\Http\Controllers\Admin\User\RoleController::class, 'update'])->name('update');
            Route::post('delete', [App\Http\Controllers\Admin\User\RoleController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\User\RoleController::class, 'deleteMultiple'])->name('multi.delete');

            Route::get('get-role-activity-log', [App\Http\Controllers\Admin\User\RoleController::class, 'getActivityLog'])->name('activity.log');

        });

        Route::group(["prefix" => "permissions", "as" => "permissions."], function () {
            Route::get('/', [App\Http\Controllers\Admin\User\PermissionController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\User\PermissionController::class, 'store'])->name('store');
            Route::match(["get", "post"], 'dt', [App\Http\Controllers\Admin\User\PermissionController::class, 'datatable'])->name('dt');
            Route::get('get', [App\Http\Controllers\Admin\User\PermissionController::class, 'getPermission'])->name('get');
            Route::post('update', [App\Http\Controllers\Admin\User\PermissionController::class, 'update'])->name('update');
            Route::post('delete', [App\Http\Controllers\Admin\User\PermissionController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\User\PermissionController::class, 'deleteMultiple'])->name('multi.delete');
        });

        Route::get('view', [App\Http\Controllers\Admin\User\UserController::class, 'view'])->name('view');
    });

    /*************** DEALS REVAMED WORK *********************/

    Route::middleware(['web', 'auth'])
        ->prefix('admin/deals-revamped')
        ->as('admin.deals_revamped.')
        ->namespace('App\Http\Controllers\Admin\Deals\Revamped')
        ->group(function () {
            Route::get('/', 'DealController@index')->name('index');
        });

    //  dealWithInviteLink

    Route::middleware(['web', 'auth'])
        ->prefix('admin/deals-revamped')
        ->as('admin.deals_revamped.')
        ->namespace('App\Http\Controllers\Admin\Deals\Revamped')
        ->group(function () {
            Route::get('/{invite_code}', 'DealController@dealWithInviteLink')->name('dealWithInviteLink');
        });
        Route::middleware(['web', 'auth'])
        ->as('admin.deals_revamped.')
        ->group(function () {
        Route::get('deals-revamped/deal-referral-list', [App\Http\Controllers\Admin\Deals\DealV2Controller::class,'getDealReferral'])->name('referral.all');
        });
        Route::middleware(['web', 'auth'])->get('listing-for-property-tab', [App\Http\Controllers\Admin\Deals\DealV2Controller::class,'getPropertyDetails'])->name('admin.deals_revamped.listing.deatils');

        Route::group(["prefix" => "admin/deals-revamped", "as" => "admin.deals_revamped.","middleware"=>['web', 'auth']], function () {
          //  Route::get('/listing-for-property-tab', [App\Http\Controllers\Admin\Deals\DealV2Controller::class,'getPropertyDetails'])->name('listing.deatils');
            Route::post('save-deal-commission', [App\Http\Controllers\Admin\Deals\DealV2Controller::class,'addDealCommission'])->name('save.deal.commission');
            Route::post('get-user-parent', [App\Http\Controllers\Admin\Deals\DealV2Controller::class,'getParentUsers'])->name('get.parent.users');
            Route::post('get-financial-data', [App\Http\Controllers\Admin\Deals\DealV2Controller::class,'getDealFinancialData'])->name('get.financial.data');
            Route::post('deal-split-save', [App\Http\Controllers\Admin\Deals\DealV2Controller::class,'saveDealSplit'])->name('split.save');
            Route::post('deal-lead-invite-form', [App\Http\Controllers\Admin\Deals\DealV2Controller::class,'inviteLead'])->name('lead.invite.form');

            // Route::get('deal-referral-list', [App\Http\Controllers\Admin\Deals\DealV2Controller::class,'getDealReferral'])->name('referral.all');
        });
    Route::middleware(['web', 'auth'])
        ->prefix('admin/deals-revamped')
        ->as('admin.deals_revamped.')
        ->group(function () {


            Route::get('bitrix/deals', [App\Http\Controllers\Admin\Deals\BitrixDealController::class, 'index'])->name('bitrix.index');
            Route::get('bitrix/fetch-deal', [App\Http\Controllers\Admin\Deals\BitrixDealController::class, 'fetchDeals'])->name('fetchDeals');
            //Route::get('bitrix/fetch-deal', [App\Http\Controllers\Admin\Deals\BitrixDealController::class, 'fetchDeals'])->name('fetchDeals');
            Route::get('bitrix/get-deal', [App\Http\Controllers\Admin\Deals\BitrixDealController::class, 'getBitrixDeal'])->name('bitrix.get.deal');
            Route::post('bitrix/deal/datatable', [App\Http\Controllers\Admin\Deals\BitrixDealController::class, 'dataTable'])->name('bitrix.dataTable');
            Route::post('bitrix/deal/crate', [App\Http\Controllers\Admin\Deals\Revamped\DealController::class, 'createDealFromBitrixData'])->name('bitrix.deal.create');


            Route::get('bitrix/deal/agent-report', [App\Http\Controllers\Admin\Deals\BitrixDealController::class, 'agentReport'])->name('bitrix.report.agent');
            Route::get('agent-report/summary', [App\Http\Controllers\Admin\Deals\Report\AgentReportController::class, 'summary'])->name('report.agent.summary');
            Route::get('agent-report/lead-summary', [App\Http\Controllers\Admin\Deals\Report\AgentReportController::class, 'leadSummary'])->name('report.agent.lead.summary');
            Route::post('agent-report/bitrix/dt', [App\Http\Controllers\Admin\Deals\Report\AgentReportController::class, 'dataTableBitrix'])->name('report.agent.bitrix.dt');
            Route::get('agent-report/recent-deals', [App\Http\Controllers\Admin\Deals\Report\AgentReportController::class, 'recentDeals'])->name('report.agent.recent.deals');
            Route::get('agent-report/bitrix/deal/status', [App\Http\Controllers\Admin\Deals\Report\AgentReportController::class, 'bitrixDealStatusData'])->name('report.agent.bitrix.deal.status');
        });

        /*************** END DEALS REVAMED WORK *********************/

    Route::group(["prefix" => "work", "as" => "admin.work."], function () {

        Route::group(["prefix" => "telesales", "as" => "telesales."], function () {
            Route::get('', [App\Http\Controllers\Admin\Telesale\TelesaleController::class, 'index'])->name('index');
            Route::get('view/{lead}', [App\Http\Controllers\Admin\Telesale\TelesaleController::class, 'view'])->name('view');
            Route::get('get-lead', [App\Http\Controllers\Admin\Telesale\TelesaleController::class, 'getLead'])->name('get');
            Route::post('store', [App\Http\Controllers\Admin\Telesale\TelesaleController::class, 'store'])->name('store');
            Route::post('update', [App\Http\Controllers\Admin\Telesale\TelesaleController::class, 'update'])->name('update');
            Route::post('datatable', [App\Http\Controllers\Admin\Telesale\TelesaleController::class, 'dataTable'])->name('dt');
            Route::post('delete', [App\Http\Controllers\Admin\Lead\LeadController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\Lead\LeadController::class, 'deleteMultiple'])->name('delete.multi');
            Route::post('save-lead-criteria', [App\Http\Controllers\Admin\Telesale\TelesaleController::class, 'storeLeadCriteria'])->name('store.criteria');

            Route::get('get-lead-agents', [App\Http\Controllers\Admin\Telesale\TelesaleController::class, 'getAgents'])->name('get.agents');
            Route::get('get-lead-activity-log', [App\Http\Controllers\Admin\Telesale\TelesaleController::class, 'getActivityLog'])->name('activity.log');
            Route::get('get-lead-response-time-log', [App\Http\Controllers\Admin\Telesale\TelesaleController::class, 'getResponseTime'])->name('response.time');
            Route::post('meeting-status', [App\Http\Controllers\Admin\Telesale\TelesaleController::class, 'meetingStatus'])->name('meeting.status');
            Route::post('task-status', [App\Http\Controllers\Admin\Telesale\TelesaleController::class, 'taskStatus'])->name('task.status');
            Route::get('get-call-log', [App\Http\Controllers\Admin\Lead\FollowUpController::class, 'getCallLog'])->name('call.log');
            // Route::get('property/autocomplete', [App\Http\Controllers\Admin\Telesale\FollowUpController::class, 'autocomplete'])->name('autocomplete');

        });

        Route::group(["prefix" => "leads", "as" => "leads."], function () {

            Route::get('', [App\Http\Controllers\Admin\Lead\LeadController::class, 'index'])->name('index');
            Route::get('view/{lead}', [App\Http\Controllers\Admin\Lead\LeadController::class, 'view'])->name('view');
            Route::get('get-lead', [App\Http\Controllers\Admin\Lead\LeadController::class, 'getLead'])->name('get');
            Route::post('store', [App\Http\Controllers\Admin\Lead\LeadController::class, 'store'])->name('store');
            Route::post('update', [App\Http\Controllers\Admin\Lead\LeadController::class, 'update'])->name('update');
            Route::post('datatable', [App\Http\Controllers\Admin\Lead\LeadController::class, 'dataTable'])->name('dt');
            Route::post('delete', [App\Http\Controllers\Admin\Lead\LeadController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\Lead\LeadController::class, 'deleteMultiple'])->name('delete.multi');
            Route::post('bulk-assign-leads', [App\Http\Controllers\Admin\Lead\LeadController::class, 'bulkAssignLeads'])->name('bulk.assign.leads');
            Route::post('bulk-assign-telesales', [App\Http\Controllers\Admin\Lead\LeadController::class, 'bulkAssignTelesales'])->name('bulk.assign.telesales');
            Route::post('bulk-assign-telesales-leader', [App\Http\Controllers\Admin\Lead\LeadController::class, 'bulkAssignTelesalesLeader'])->name('bulk.assign.telesales.leader');
            Route::post('bulk-assign-general', [App\Http\Controllers\Admin\Lead\LeadController::class, 'generalBulk'])->name('general.bulk.assign.leads');
            Route::post('bulk-assign-to-myself', [App\Http\Controllers\Admin\Lead\LeadController::class, 'bulkAssignLeadsToMyself'])->name('bulk.assign.leads.myself');
            Route::post('lead-assign-to-myself', [App\Http\Controllers\Admin\Lead\LeadController::class, 'assignLeadsToMyself'])->name('assign.leads.myself');

            // take archived leads
            Route::post('archived-lead-assign-to-myself', [App\Http\Controllers\Admin\Lead\LeadController::class, 'assignArchivedLeadsToMyself'])->name('assign.archived.leads.myself');
            Route::post('archived-bulk-assign-to-myself', [App\Http\Controllers\Admin\Lead\LeadController::class, 'bulkAssignArchivedLeadsToMyself'])->name('bulk.assign.archived.leads.myself');

            Route::get('check-duplicate/leads', [App\Http\Controllers\Admin\Lead\LeadController::class, 'checkDuplicate'])->name('check.duplicate');

            Route::get('export-excel', [App\Http\Controllers\Admin\Lead\LeadController::class, 'exportExcel'])->name('export-excel');


            Route::post('add-note', [App\Http\Controllers\Admin\Lead\LeadController::class, 'addNote'])->name('add-note');
            Route::post('request-access', [App\Http\Controllers\Admin\Lead\LeadController::class, 'requestAccess'])->name('request-access');
            Route::post('add-lead-agent', [App\Http\Controllers\Admin\Lead\LeadController::class, 'addAgent'])->name('add.more.agent');
            Route::get('get-lead-agents', [App\Http\Controllers\Admin\Lead\LeadController::class, 'getAgents'])->name('get.agents');
            Route::post('save-lead-criteria', [App\Http\Controllers\Admin\Lead\LeadController::class, 'storeLeadCriteria'])->name('store.criteria');
            Route::get('get-lead-activity-log', [App\Http\Controllers\Admin\Lead\LeadController::class, 'getActivityLog'])->name('activity.log');
            Route::get('get-lead-response-time-log', [App\Http\Controllers\Admin\Lead\LeadController::class, 'getResponseTime'])->name('response.time');

            Route::get('lead-agent-delete', [App\Http\Controllers\Admin\Lead\LeadController::class, 'removeAgent'])->name('remove.agent');
            Route::post('assign-agents', [App\Http\Controllers\Admin\Lead\LeadController::class, 'assignAgents'])->name('assign-agents');

            Route::get('get-call-log', [App\Http\Controllers\Admin\Lead\FollowUpController::class, 'getCallLog'])->name('call.log');
            //Route::post('create-new-meeting', [App\Http\Controllers\Admin\Lead\FollowUpController::class, 'storeMeeting'])->name('meeting.store');
            //Route::post('update-meeting', [App\Http\Controllers\Admin\Lead\FollowUpController::class, 'updateMeeting'])->name('meeting.update');
            //  Route::post('meeting-status', [App\Http\Controllers\Admin\Lead\FollowUpController::class, 'meetingStatus'])->name('meeting.status');
            //Route::get('meeting-get', [App\Http\Controllers\Admin\Lead\FollowUpController::class, 'getMeeting'])->name('meeting.get');
            //Route::get('get-meeting-log', [App\Http\Controllers\Admin\Lead\FollowUpController::class, 'getMeetingLog'])->name('meeting.log');
            Route::post('meeting-status', [App\Http\Controllers\Admin\Event\LeadEventController::class, 'meetingStatus'])->name('meeting.status');

            Route::post('task-status', [App\Http\Controllers\Admin\Event\LeadEventController::class, 'taskStatus'])->name('task.status');

            //Route::post('create-task', [App\Http\Controllers\Admin\Lead\FollowUpController::class, 'storeTask'])->name('task.store');
            //Route::get('get-task', [App\Http\Controllers\Admin\Lead\FollowUpController::class, 'getTask'])->name('task.get');
            //Route::post('update-task', [App\Http\Controllers\Admin\Lead\FollowUpController::class, 'updateTask'])->name('task.update');
            //Route::post('task-status', [App\Http\Controllers\Admin\Lead\FollowUpController::class, 'taskStatus'])->name('task.status');
            //Route::get('get-task-log', [App\Http\Controllers\Admin\Lead\FollowUpController::class, 'getTasks'])->name('task.log');
            Route::post('start-call', [App\Http\Controllers\Admin\Lead\LeadController::class, 'startCall'])->name('start-call');

            Route::post('archive', [App\Http\Controllers\Admin\Lead\LeadController::class, 'archive'])->name('archive');
            Route::post('transfer-to-leadpool', [App\Http\Controllers\Admin\Lead\LeadController::class, 'transferToLeadpool'])->name('transferToLeadpool');

            Route::post('saveReason', [App\Http\Controllers\Admin\Lead\LeadController::class, 'saveReason'])->name('saveReason');

            Route::get('property/autocomplete', [App\Http\Controllers\Admin\Lead\LeadController::class, 'autocomplete'])->name('autocomplete');
            Route::get('property/agents/autocomplete', [App\Http\Controllers\Admin\Lead\LeadController::class, 'autocompleteAgents'])->name('autocompleteAgents');
            Route::get('property/managers/autocomplete', [App\Http\Controllers\Admin\Lead\LeadController::class, 'autocompleteManager'])->name('autocompleteManager');


            Route::get('lead-status-update', [App\Http\Controllers\Admin\Lead\LeadController::class, 'statusUpadte'])->name('statusUpadte');

            Route::post('leads/duplicate', [App\Http\Controllers\Admin\Lead\LeadController::class, 'duplicateLeads'])->name('duplicateLeads');

            Route::get('filtered-data', [App\Http\Controllers\Admin\Lead\LeadController::class, 'leadExport'])->name('leadExport');
            Route::get('filtered-helper', [App\Http\Controllers\Admin\Lead\LeadController::class, 'filterHelper'])->name('filterHelper');

            Route::post('create-lead-offer', [App\Http\Controllers\Admin\Lead\LeadOfferController::class, 'storeOffer'])->name('offer.store');

            Route::post('add-qualified-note', [App\Http\Controllers\Admin\Lead\LeadController::class, 'addQualifiedNote'])->name('add-qualified-note');
            Route::post('transfer-to-portal-lead-pool', [App\Http\Controllers\Admin\Lead\LeadController::class, 'transferToPortalLeadPool'])->name('transfer-to-portal-lead-pool');

        });

        Route::group(["prefix" => "meta", "as" => "meta."], function () {
            Route::get('meta-data', [App\Http\Controllers\Api\ExternalApiController::class, 'getMetaData']);
            Route::get('meta-data/delete', [App\Http\Controllers\Api\ExternalApiController::class, 'deleteMetaData']);
            Route::get('lead-message', [App\Http\Controllers\Api\ExternalApiController::class, 'getLeadMessage']);
            Route::post('lead-message/update', [App\Http\Controllers\Api\ExternalApiController::class, 'getLeadMessageUpdate'])->name('updateLeadMessage');
            Route::post('lead-message/delete', [App\Http\Controllers\Api\ExternalApiController::class, 'getLeadMessageDelete'])->name('deleteLeadMessage');

        });
        Route::group(["prefix" => "contacts", "as" => "contacts.", "middleware" => ['throttle:60,1']], function () {
            Route::get('/', [App\Http\Controllers\Admin\Contact\ContactController::class, 'index'])->name('index');
            Route::get('get-contact', [App\Http\Controllers\Admin\Contact\ContactController::class, 'getContact'])->name('view');
            Route::post('store', [App\Http\Controllers\Admin\Contact\ContactController::class, 'store'])->name('store');
            Route::post('update', [App\Http\Controllers\Admin\Contact\ContactController::class, 'update'])->name('update');
            Route::post('datatable', [App\Http\Controllers\Admin\Contact\ContactController::class, 'dataTable'])->name('dt');
            Route::post('datatable-for-profile-view', [App\Http\Controllers\Admin\Contact\ContactController::class, 'dataTableForProfileView'])->name('datatable-for-profile-view');
            Route::post('updateStatus', [App\Http\Controllers\Admin\Contact\ContactController::class, 'updateStatus'])->name('status');
            Route::post('delete', [App\Http\Controllers\Admin\Contact\ContactController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\Contact\ContactController::class, 'deleteMultiple'])->name('delete.multi');
            Route::post('add-note', [App\Http\Controllers\Admin\Contact\ContactController::class, 'addNote'])->name('add-note');
            Route::post('bulk-assign-contact', [App\Http\Controllers\Admin\Contact\ContactController::class, 'bulkAssignToAgent'])->name('bulk.assign');
            Route::get('get-contact-activity-log', [App\Http\Controllers\Admin\Contact\ContactController::class, 'getActivityLog'])->name('activity.log');

            Route::post('add-contact-agent', [App\Http\Controllers\Admin\Contact\ContactController::class, 'addAgent'])->name('add.more.agent');
            Route::get('get-contact-agents', [App\Http\Controllers\Admin\Contact\ContactController::class, 'getAgents'])->name('get.agents');
            Route::post('save-lead-criteria', [App\Http\Controllers\Admin\Contact\ContactController::class, 'storeLeadCriteria'])->name('store.criteria');
            Route::post('assign-agents', [App\Http\Controllers\Admin\Contact\ContactController::class, 'assignAgents'])->name('assign-agents');
            Route::get('lead-contact-delete', [App\Http\Controllers\Admin\Contact\ContactController::class, 'removeAgent'])->name('remove.agent');

            //  Route::get('get-call-log', [App\Http\Controllers\Admin\Contact\FollowUpController::class, 'getCallLog'])->name('call.log');

            //Route::post('create-new-meeting', [App\Http\Controllers\Admin\Contact\FollowUpController::class, 'storeMeeting'])->name('meeting.store');
            //Route::get('get-meeting-log', [App\Http\Controllers\Admin\Contact\FollowUpController::class, 'getMeetingLog'])->name('meeting.log');
            //Route::get('meeting-get', [App\Http\Controllers\Admin\Contact\FollowUpController::class, 'getMeeting'])->name('meeting.get');
            //Route::post('update-meeting', [App\Http\Controllers\Admin\Contact\FollowUpController::class, 'updateMeeting'])->name('meeting.update');
            //Route::post('meeting-status', [App\Http\Controllers\Admin\Contact\FollowUpController::class, 'meetingStatus'])->name('meeting.status');
            Route::post('meeting-status', [App\Http\Controllers\Admin\Event\ContactEventController::class, 'meetingStatus'])->name('meeting.status');

            //Route::post('create-task', [App\Http\Controllers\Admin\Contact\FollowUpController::class, 'storeTask'])->name('task.store');
            //Route::get('get-task', [App\Http\Controllers\Admin\Contact\FollowUpController::class, 'getTask'])->name('task.get');
            //Route::post('update-task', [App\Http\Controllers\Admin\Contact\FollowUpController::class, 'updateTask'])->name('task.update');
            //Route::post('task-status', [App\Http\Controllers\Admin\Contact\FollowUpController::class, 'taskStatus'])->name('task.status');
            Route::post('task-status', [App\Http\Controllers\Admin\Event\ContactEventController::class, 'taskStatus'])->name('task.status');
            //Route::get('get-task-log', [App\Http\Controllers\Admin\Event\ContactEventController::class, 'getTasks'])->name('task.log');
            Route::post('start-call', [App\Http\Controllers\Admin\Contact\ContactController::class, 'startCall'])->name('start-call');
            Route::get('filtered-helper', [App\Http\Controllers\Admin\Contact\ContactController::class, 'filterHelper'])->name('filterHelper');

            Route::get('/duplicate-contacts', [App\Http\Controllers\Admin\Contact\ContactController::class, 'duplicateContacts'])->name('index.duplicate');


        });
        Route::group(["prefix" => "deals", "as" => "deals."], function () {
            Route::get('/dashboard', [App\Http\Controllers\Admin\Deals\DealDashboardController::class, 'dashboard'])->name('dashboard');
            Route::get('/deal-status-data', [App\Http\Controllers\Admin\Deals\DealDashboardController::class, 'getDealStatusData'])->name('dashboard.status.data');
            Route::get('/deal-agent-data', [App\Http\Controllers\Admin\Deals\DealDashboardController::class, 'agentDealOverview'])->name('dashboard.agent.data');
            Route::get('/deal-bitrix-data', [App\Http\Controllers\Admin\Deals\DealDashboardController::class, 'getBitrixDashboardData'])->name('dashboard.bitrix.data');

            Route::get('/', [App\Http\Controllers\Admin\Deals\DealsController::class, 'index'])->name('index');
            Route::get('get-deal', [App\Http\Controllers\Admin\Deals\DealsController::class, 'getDeal'])->name('get');
            Route::post('store', [App\Http\Controllers\Admin\Deals\DealsController::class, 'store'])->name('store');
            Route::post('initial-store', [App\Http\Controllers\Admin\Deals\DealsController::class, 'initialDataStore'])->name('initStore');
            Route::post('update', [App\Http\Controllers\Admin\Deals\DealsController::class, 'update'])->name('update');
            Route::post('datatable', [App\Http\Controllers\Admin\Deals\DealsController::class, 'dataTable'])->name('dt');
            Route::post('delete', [App\Http\Controllers\Admin\Deals\DealsController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\Deals\DealsController::class, 'deleteMultiple'])->name('multi.delete');
            Route::post('add-note', [App\Http\Controllers\Admin\Deals\DealsController::class, 'addNote'])->name('add-note');

            Route::get('create/{id}', [App\Http\Controllers\Admin\Deals\DealsController::class, 'create'])->name('create');

            Route::get('commission-type', [App\Http\Controllers\Admin\Deals\CommissionTypeController::class, 'index'])->name('commissionType.index');
            Route::get('step', [App\Http\Controllers\Admin\Deals\DealsController::class, 'wizard'])->name('step.wizard');

            //NEW ROUTES RELATED TO DEAL REVAMPED
            Route::post('add-new-lead', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'addDealLead'])->name('addDealLead');
            Route::get('get-owner', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getContact'])->name('get.contact');

            Route::post('delete-media', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'deleteMedia'])->name('deleteMedia');
            Route::get('get-deal-notes', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getDealNotes'])->name('getDealNotes');
            Route::post('add-deal-note', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'addNote'])->name('add-note');

            Route::get('get-lead', [App\Http\Controllers\Admin\Lead\LeadController::class, 'getLead'])->name('getLead.api');
            Route::get('get-deal-leads', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getDealLeads'])->name('getDealLeads');
            Route::post('update-lead', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'updateLead'])->name('updateLead');

            Route::post('update-owner', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'updateContact'])->name('updateContact');

            Route::post('add-deal-doc', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'addDealDoc'])->name('add.deal.doc');
            Route::post('update-deal-doc', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'updateDealDoc'])->name('update.deal.doc');

            Route::get('get-deal-media', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'getDealMedia'])->name('getDealMedia');
            Route::post('delete-deal-media', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'deleteDealdoc'])->name('deal.delete.doc');

            Route::post('generate-invite-link', [\App\Http\Controllers\Api\V1\Deals\DealInvitationApiController::class, 'create'])->name('generateInviteLink');
            //routes related to listing page deal creation
            Route::post('create-deal-listing', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'storeFromListing'])->name('create.from.listing');
            Route::post('create-deal-from-lead', [\App\Http\Controllers\Api\V1\Deals\DealApiController::class, 'createDealFromLead'])->name('create.from.lead');

            Route::get('check-deal-before-submit', [\App\Http\Controllers\Admin\Deals\Revamped\DealController::class, 'checkDealBeforeSubmit'])->name('data.check');

        });

        Route::group(["prefix" => "commission", "as" => "commission."], function () {
            Route::group(["prefix" => "slab", "as" => "slab."], function () {
                Route::get('/', [App\Http\Controllers\Admin\Commission\CommissionSlabController::class, 'index'])->name('index');
                Route::post('datatable', [App\Http\Controllers\Admin\Commission\CommissionSlabController::class, 'datatable'])->name('datatable');
                Route::post('store', [App\Http\Controllers\Admin\Commission\CommissionSlabController::class, 'store'])->name('store');
                Route::post('update', [App\Http\Controllers\Admin\Commission\CommissionSlabController::class, 'update'])->name('update');
                Route::post('delete', [App\Http\Controllers\Admin\Commission\CommissionSlabController::class, 'delete'])->name('delete');
                Route::get('get', [App\Http\Controllers\Admin\Commission\CommissionSlabController::class, 'edit'])->name('get');
                Route::post('status', [App\Http\Controllers\Admin\Commission\CommissionSlabController::class, 'updateStatus'])->name('status.update');
                Route::post('slab-item', [App\Http\Controllers\Admin\Commission\CommissionSlabController::class, 'createSlabItem'])->name('slab.item.create');
                Route::post('slab-item/update', [App\Http\Controllers\Admin\Commission\CommissionSlabController::class, 'updateSlabItem'])->name('slab.item.update');
                Route::get('get-limits', [App\Http\Controllers\Admin\Commission\CommissionSlabController::class, 'getSlabLimits'])->name('getSlabLimits');
                Route::post('delete-slab-limits', [App\Http\Controllers\Admin\Commission\CommissionSlabController::class, 'deleteSlabLimits'])->name('slab.limits.delete');

                Route::post('type/store', [App\Http\Controllers\Admin\Deals\CommissionTypeController::class, 'store'])->name('type.store');
                Route::post('type/datatable', [App\Http\Controllers\Admin\Deals\CommissionTypeController::class, 'dataTable'])->name('type.dt');

            });
            Route::group(["prefix" => "type", "as" => "type."], function () {
                Route::post('type/store', [App\Http\Controllers\Admin\Deals\CommissionTypeController::class, 'store'])->name('store');
                Route::post('type/update', [App\Http\Controllers\Admin\Deals\CommissionTypeController::class, 'update'])->name('update');
                Route::post('type/datatable', [App\Http\Controllers\Admin\Deals\CommissionTypeController::class, 'dataTable'])->name('dt');
                Route::get('type/get', [App\Http\Controllers\Admin\Deals\CommissionTypeController::class, 'get'])->name('get');
                Route::post('type/status', [App\Http\Controllers\Admin\Deals\CommissionTypeController::class, 'updateStatus'])->name('status.update');
                Route::post('type/delete', [App\Http\Controllers\Admin\Deals\CommissionTypeController::class, 'delete'])->name('delete');
                Route::post('type/delete-multi', [App\Http\Controllers\Admin\Deals\CommissionTypeController::class, 'deleteMultiple'])->name('delete.multi');

            });
        });

    });

    Route::group(["prefix" => "contact", "as" => "admin.contact."], function () {
        Route::get('source', [App\Http\Controllers\Admin\Contact\SourcesController::class, 'index'])->name('source');
        Route::post('source/store', [App\Http\Controllers\Admin\Contact\SourcesController::class, 'store'])->name('source.store');
        Route::post('source/dt/leads', [App\Http\Controllers\Admin\Contact\SourcesController::class, 'sourceForLeadsDatatable'])->name('source.dt.leads');
        Route::post('source/dt/contacts', [App\Http\Controllers\Admin\Contact\SourcesController::class, 'sourceForContactsDatatable'])->name('source.dt.contacts');
        Route::post('source/dt', [App\Http\Controllers\Admin\Contact\SourcesController::class, 'sourceForDatatable'])->name('source.dt');
        Route::post('source/status', [App\Http\Controllers\Admin\Contact\SourcesController::class, 'updateStatus'])->name('source.status');
        Route::post('source/update', [App\Http\Controllers\Admin\Contact\SourcesController::class, 'update'])->name('source.update');
        Route::get('source/get', [App\Http\Controllers\Admin\Contact\SourcesController::class, 'getSource'])->name('source.get');
        Route::post('source/delete', [App\Http\Controllers\Admin\Contact\SourcesController::class, 'delete'])->name('source.delete');
        Route::post('source/delete-multiple', [App\Http\Controllers\Admin\Contact\SourcesController::class, 'deleteMultiple'])->name('source.multi.delete');

        // Route::get('source/leads', [App\Http\Controllers\Admin\Contact\SourcesController::class, 'leads'])->name('source.leads');
    });

    Route::group(["prefix" => "general", "as" => "admin.general."], function () {
        Route::get('source/', [App\Http\Controllers\Admin\Source\SourceController::class, 'index'])->name('source.contacts');
        Route::post('source/dt', [App\Http\Controllers\Admin\Source\SourceController::class, 'sourceForDatatable'])->name('source.dt');
        Route::get('source/get', [App\Http\Controllers\Admin\Source\SourceController::class, 'getSource'])->name('source.get');
        Route::post('source/update/', [App\Http\Controllers\Admin\Source\SourceController::class, 'update'])->name('source.update');
        Route::post('source/store/', [App\Http\Controllers\Admin\Source\SourceController::class, 'store'])->name('source.store');
        Route::get('source/get/', [App\Http\Controllers\Admin\Source\SourceController::class, 'getSource'])->name('source.get');
        Route::post('source/delete-multiple/', [App\Http\Controllers\Admin\Source\SourceController::class, 'deleteMultiple'])->name('source.multi.delete');
        Route::post('source/delete/single', [App\Http\Controllers\Admin\Source\SourceController::class, 'delete'])->name('source.delete');
        Route::post('source/status/', [App\Http\Controllers\Admin\Source\SourceController::class, 'updateStatus'])->name('source.status');
        Route::post('source/lead/status', [App\Http\Controllers\Admin\Source\SourceController::class, 'updateLeadStatus'])->name('source.lead');
        Route::post('source/contact/status', [App\Http\Controllers\Admin\Source\SourceController::class, 'updateContactStatus'])->name('source.contact');
        Route::post('source/delete/thumbnail', [App\Http\Controllers\Admin\Source\SourceController::class, 'deleteThumbnail'])->name('source.delete.thumbnail');

        Route::get('medium/', [App\Http\Controllers\Admin\Contact\MediumController::class, 'index'])->name('source.medium');
        Route::post('medium/status', [App\Http\Controllers\Admin\Contact\MediumController::class, 'updateStatus'])->name('source.status.medium');
        Route::get('medium/get', [App\Http\Controllers\Admin\Contact\MediumController::class, 'getSource'])->name('source.get.medium');
        Route::post('medium/delete', [App\Http\Controllers\Admin\Contact\MediumController::class, 'delete'])->name('source.delete.medium');
        Route::post('medium/store', [App\Http\Controllers\Admin\Contact\MediumController::class, 'store'])->name('source.store.medium');
        Route::post('medium/update', [App\Http\Controllers\Admin\Contact\MediumController::class, 'update'])->name('source.update.medium');
        Route::post('medium/delete-multiple', [App\Http\Controllers\Admin\Contact\MediumController::class, 'deleteMultiple'])->name('source.multi.delete.medium');
        Route::post('medium/dt', [App\Http\Controllers\Admin\Contact\MediumController::class, 'sourceForDatatable'])->name('source.dt.medium');
        Route::post('medium/lead/status', [App\Http\Controllers\Admin\Contact\MediumController::class, 'updateLeadStatus'])->name('medium.status.lead');
        Route::post('medium/contact/status', [App\Http\Controllers\Admin\Contact\MediumController::class, 'updateContactStatus'])->name('medium.status.contact');
        Route::post('medium/delete/thumbnail', [App\Http\Controllers\Admin\Contact\MediumController::class, 'deleteThumbnail'])->name('medium.delete.thumbnail');

        Route::get('types/', [App\Http\Controllers\Admin\Type\TypeController::class, 'index'])->name('type.all');
        Route::post('type/dt', [App\Http\Controllers\Admin\Type\TypeController::class, 'sourceForDatatable'])->name('type.dt');
        Route::get('type/get', [App\Http\Controllers\Admin\Type\TypeController::class, 'getSource'])->name('type.get');
        Route::post('type/update/', [App\Http\Controllers\Admin\Type\TypeController::class, 'update'])->name('type.update');
        Route::post('type/store/', [App\Http\Controllers\Admin\Type\TypeController::class, 'store'])->name('type.store');
        Route::get('type/get/', [App\Http\Controllers\Admin\Type\TypeController::class, 'getSource'])->name('type.get');
        Route::post('type/delete-multiple/', [App\Http\Controllers\Admin\Type\TypeController::class, 'deleteMultiple'])->name('type.multi.delete');
        Route::post('type/delete/single', [App\Http\Controllers\Admin\Type\TypeController::class, 'delete'])->name('type.delete');
        Route::post('type/status/', [App\Http\Controllers\Admin\Type\TypeController::class, 'updateStatus'])->name('type.status');
        Route::post('type/lead/status', [App\Http\Controllers\Admin\Type\TypeController::class, 'updateLeadStatus'])->name('type.lead');
        Route::post('type/contact/status', [App\Http\Controllers\Admin\Type\TypeController::class, 'updateContactStatus'])->name('type.contact');

        Route::post('delete_general', [App\Http\Controllers\Admin\Deletion\DeleteController::class, 'delete_general'])->name('delete.delete_general');
        Route::post('delete_listing_hard', [App\Http\Controllers\Admin\Deletion\DeleteController::class, 'delete_listing_hard'])->name('delete.delete_listing_hard');

        Route::post('paremeter/general/delete', [App\Http\Controllers\Admin\Deletion\DeleteController::class, 'deleteGeneral'])->name('paramerter.delete.general');

        // Route::post('delete_listing_hard/medium', [App\Http\Controllers\Admin\Contact\MediumController::class, 'delete_listing_hard'])->name('medium.delete.delete_listing_hard');
        // Route::post('delete_listing_hard/source', [App\Http\Controllers\Admin\Source\SourceController::class, 'delete_listing_hard'])->name('source.delete.delete_listing_hard');

        Route::get('deals-deductable-type/', [App\Http\Controllers\Admin\Deals\DealDeductableTypeController::class, 'index'])->name('dealsDeductableType.index');
        Route::post('deals-deductable-type/dt', [App\Http\Controllers\Admin\Deals\DealDeductableTypeController::class, 'dealsDeductableTypeForDatatable'])->name('dealsDeductableType.dt');
        Route::get('deals-deductable-type/get', [App\Http\Controllers\Admin\Deals\DealDeductableTypeController::class, 'getdealsDeductableType'])->name('dealsDeductableType.get');
        Route::post('deals-deductable-type/update/', [App\Http\Controllers\Admin\Deals\DealDeductableTypeController::class, 'update'])->name('dealsDeductableType.update');
        Route::post('deals-deductable-type/store/', [App\Http\Controllers\Admin\Deals\DealDeductableTypeController::class, 'store'])->name('dealsDeductableType.store');
        Route::post('deals-deductable-type/delete-multiple/', [App\Http\Controllers\Admin\Deals\DealDeductableTypeController::class, 'deleteMultiple'])->name('dealsDeductableType.multi.delete');
        Route::post('deals-deductable-type/delete/single', [App\Http\Controllers\Admin\Deals\DealDeductableTypeController::class, 'delete'])->name('dealsDeductableType.delete');
        Route::post('deals-deductable-type/status/', [App\Http\Controllers\Admin\Deals\DealDeductableTypeController::class, 'updateStatus'])->name('dealsDeductableType.status');
        Route::post('deals-deductable-type/lead/status', [App\Http\Controllers\Admin\Deals\DealDeductableTypeController::class, 'updateLeadStatus'])->name('dealsDeductableType.lead');
        Route::post('deals-deductable-type/contact/status', [App\Http\Controllers\Admin\Deals\DealDeductableTypeController::class, 'updateContactStatus'])->name('dealsDeductableType.contact');
        Route::post('deals-deductable-type/delete/thumbnail', [App\Http\Controllers\Admin\Deals\DealDeductableTypeController::class, 'deleteThumbnail'])->name('dealsDeductableType.delete.thumbnail');

    });

    Route::group(["prefix" => "calls", "as" => "admin.calls."], function () {
        Route::get('/', [App\Http\Controllers\Admin\Calls\CallLogController::class, 'index'])->name('index');
        Route::post('dt', [App\Http\Controllers\Admin\Calls\CallLogController::class, 'datatable'])->name('dt');
        Route::get('get', [App\Http\Controllers\Admin\Calls\CallLogController::class, 'getLog'])->name('get');
        Route::get('get', [App\Http\Controllers\Admin\Calls\CallLogController::class, 'getLog'])->name('get');
        Route::post('updateLead', [App\Http\Controllers\Admin\Calls\CallLogController::class, 'updateLead'])->name('updateLead');
        Route::post('archive', [App\Http\Controllers\Admin\Calls\CallLogController::class, 'archive'])->name('archive');
        Route::post('delete/multiple', [App\Http\Controllers\Admin\Calls\CallLogController::class, 'multidelete'])->name('multidelete');

    });

    Route::group(["prefix" => "property", "as" => "admin.property."], function () {
        Route::get('category', [App\Http\Controllers\Admin\Property\CategoryController::class, 'index'])->name('category');
        Route::post('category/store', [App\Http\Controllers\Admin\Property\CategoryController::class, 'store'])->name('category.store');
        Route::post('category/dt', [App\Http\Controllers\Admin\Property\CategoryController::class, 'categoriesForDatatable'])->name('category.dt');
        Route::post('category/status', [App\Http\Controllers\Admin\Property\CategoryController::class, 'updateStatus'])->name('category.status');
        Route::post('category/update', [App\Http\Controllers\Admin\Property\CategoryController::class, 'update'])->name('category.update');
        Route::get('category/get', [App\Http\Controllers\Admin\Property\CategoryController::class, 'getCategory'])->name('category.get');
        Route::post('category/delete', [App\Http\Controllers\Admin\Property\CategoryController::class, 'delete'])->name('category.delete');
        Route::post('category/delete-multiple', [App\Http\Controllers\Admin\Property\CategoryController::class, 'deleteMultiple'])->name('category.multi.delete');

        Route::get('type', [App\Http\Controllers\Admin\Property\PropertyTypeController::class, 'index'])->name('type');
        Route::post('type/store', [App\Http\Controllers\Admin\Property\PropertyTypeController::class, 'store'])->name('type.store');
        Route::post('type/dt', [App\Http\Controllers\Admin\Property\PropertyTypeController::class, 'typesForDatatable'])->name('type.dt');
        Route::post('type/status', [App\Http\Controllers\Admin\Property\PropertyTypeController::class, 'updateStatus'])->name('type.status');
        Route::post('type/update', [App\Http\Controllers\Admin\Property\PropertyTypeController::class, 'update'])->name('type.update');
        Route::get('type/get', [App\Http\Controllers\Admin\Property\PropertyTypeController::class, 'getType'])->name('type.get');
        Route::post('type/delete', [App\Http\Controllers\Admin\Property\PropertyTypeController::class, 'delete'])->name('type.delete');
        Route::post('type/delete-multiple', [App\Http\Controllers\Admin\Property\PropertyTypeController::class, 'deleteMultiple'])->name('type.multi.delete');

        Route::get('status', [App\Http\Controllers\Admin\Property\StatusController::class, 'index'])->name('status');
        Route::post('status/store', [App\Http\Controllers\Admin\Property\StatusController::class, 'store'])->name('status.store');
        Route::post('status/dt', [App\Http\Controllers\Admin\Property\StatusController::class, 'statusForDatatable'])->name('status.dt');
        Route::post('status/status', [App\Http\Controllers\Admin\Property\StatusController::class, 'updateStatus'])->name('status.status');
        Route::post('status/update', [App\Http\Controllers\Admin\Property\StatusController::class, 'update'])->name('status.update');
        Route::get('status/get', [App\Http\Controllers\Admin\Property\StatusController::class, 'getStatus'])->name('status.get');
        Route::post('status/delete', [App\Http\Controllers\Admin\Property\StatusController::class, 'delete'])->name('status.delete');
        Route::post('status/delete-multiple', [App\Http\Controllers\Admin\Property\StatusController::class, 'deleteMultiple'])->name('status.multi.delete');

        Route::get('feature', [App\Http\Controllers\Admin\Property\FeatureController::class, 'index'])->name('feature');
        Route::post('feature/store', [App\Http\Controllers\Admin\Property\FeatureController::class, 'store'])->name('feature.store');
        Route::post('feature/dt', [App\Http\Controllers\Admin\Property\FeatureController::class, 'featureForDatatable'])->name('feature.dt');
        Route::post('feature/status', [App\Http\Controllers\Admin\Property\FeatureController::class, 'updateStatus'])->name('feature.status');
        Route::post('feature/update', [App\Http\Controllers\Admin\Property\FeatureController::class, 'update'])->name('feature.update');
        Route::get('feature/get', [App\Http\Controllers\Admin\Property\FeatureController::class, 'getFeature'])->name('feature.get');
        Route::post('feature/delete', [App\Http\Controllers\Admin\Property\FeatureController::class, 'delete'])->name('feature.delete');
        Route::post('feature/delete-multiple', [App\Http\Controllers\Admin\Property\FeatureController::class, 'deleteMultiple'])->name('feature.multi.delete');

        Route::get("bath", [App\Http\Controllers\Admin\Property\BathController::class, 'index'])->name('bath');
        Route::post('bath/store', [App\Http\Controllers\Admin\Property\BathController::class, 'store'])->name('bath.store');
        Route::post('bath/dt', [App\Http\Controllers\Admin\Property\BathController::class, 'bathForDatatable'])->name('bath.dt');
        Route::post('bath/status', [App\Http\Controllers\Admin\Property\BathController::class, 'updateStatus'])->name('bath.status');
        Route::post('bath/update', [App\Http\Controllers\Admin\Property\BathController::class, 'update'])->name('bath.update');
        Route::get('bath/get', [App\Http\Controllers\Admin\Property\BathController::class, 'getBath'])->name('bath.get');
        Route::post('bath/delete', [App\Http\Controllers\Admin\Property\BathController::class, 'delete'])->name('bath.delete');
        Route::post('bath/delete-multiple', [App\Http\Controllers\Admin\Property\BathController::class, 'deleteMultiple'])->name('bath.multi.delete');

        Route::get("bed", [App\Http\Controllers\Admin\Property\BedController::class, 'index'])->name('bed');
        Route::post('bed/store', [App\Http\Controllers\Admin\Property\BedController::class, 'store'])->name('bed.store');
        Route::post('bed/dt', [App\Http\Controllers\Admin\Property\BedController::class, 'bedForDatatable'])->name('bed.dt');
        Route::post('bed/status', [App\Http\Controllers\Admin\Property\BedController::class, 'updateStatus'])->name('bed.status');
        Route::post('bed/update', [App\Http\Controllers\Admin\Property\BedController::class, 'update'])->name('bed.update');
        Route::get('bed/get', [App\Http\Controllers\Admin\Property\BedController::class, 'getBed'])->name('bed.get');
        Route::post('bed/delete', [App\Http\Controllers\Admin\Property\BedController::class, 'delete'])->name('bed.delete');
        Route::post('bed/delete-multiple', [App\Http\Controllers\Admin\Property\BedController::class, 'deleteMultiple'])->name('bed.multi.delete');

        Route::get("developer", [App\Http\Controllers\Admin\Property\DeveloperController::class, 'index'])->name('developers');
        Route::post("developer", [App\Http\Controllers\Admin\Property\DeveloperController::class, 'dataTable'])->name('developers.dataTable');
        Route::post("store", [App\Http\Controllers\Admin\Property\DeveloperController::class, 'store'])->name('developers.store');
        Route::post("update", [App\Http\Controllers\Admin\Property\DeveloperController::class, 'update'])->name('developers.update');
        Route::get("developer-get", [App\Http\Controllers\Admin\Property\DeveloperController::class, 'getData'])->name('developers.get.data');
        Route::post("developer-status-update", [App\Http\Controllers\Admin\Property\DeveloperController::class, 'updateStatus'])->name('developers.update.status');
        Route::post("delete", [App\Http\Controllers\Admin\Property\DeveloperController::class, 'delete'])->name('developers.delete');
        Route::post("delete-multiple", [App\Http\Controllers\Admin\Property\DeveloperController::class, 'deleteMultiple'])->name('developers.delete.multiple');

        Route::get("property-approval", [App\Http\Controllers\Admin\Approval\PropertyApprovalController::class, 'index'])->name('approval.index');
        Route::post("property-approval-dt", [App\Http\Controllers\Admin\Approval\PropertyApprovalController::class, 'dataTable'])->name('approval.dt');
        Route::get("property-approval-get", [App\Http\Controllers\Admin\Approval\PropertyApprovalController::class, 'show'])->name('approval.get');
        Route::get("property-approval-get-id", [App\Http\Controllers\Admin\Approval\PropertyApprovalController::class, 'getByProperty'])->name('approval.get.property');
        Route::post("property-approval-action", [App\Http\Controllers\Admin\Approval\PropertyApprovalController::class, 'action'])->name('approval.property.action');

    });

    Route::group(["prefix" => "deals", "as" => "admin.deals."], function () {
        Route::get('deals', [App\Http\Controllers\Admin\Deals\DealsController::class, 'index'])->name('index');
    });

    Route::group(["prefix" => "contact", "as" => "admin.contact."], function () {

        Route::get('status', [App\Http\Controllers\Admin\Contact\StatusController::class, 'index'])->name('status.index');
        Route::post('status/store', [App\Http\Controllers\Admin\Contact\StatusController::class, 'store'])->name('status.store');
        Route::post('status/dt', [App\Http\Controllers\Admin\Contact\StatusController::class, 'statusForDatatable'])->name('status.dt');
        Route::post('status/status', [App\Http\Controllers\Admin\Contact\StatusController::class, 'updateStatus'])->name('status.status');
        Route::post('status/update', [App\Http\Controllers\Admin\Contact\StatusController::class, 'update'])->name('status.update');
        Route::get('status/single/get', [App\Http\Controllers\Admin\Contact\StatusController::class, 'getStatus'])->name('status.get');
        Route::post('status/delete', [App\Http\Controllers\Admin\Contact\StatusController::class, 'delete'])->name('status.delete');
        Route::post('status/delete-multiple', [App\Http\Controllers\Admin\Contact\StatusController::class, 'deleteMultiple'])->name('status.multi.delete');

        Route::get('types', [App\Http\Controllers\Admin\Contact\TypesController::class, 'index'])->name('types');

        Route::post('types/store', [App\Http\Controllers\Admin\Contact\TypesController::class, 'store'])->name('types.store');
        Route::post('types/dt', [App\Http\Controllers\Admin\Contact\TypesController::class, 'typeForDatatable'])->name('types.dt');
        Route::post('types/status', [App\Http\Controllers\Admin\Contact\TypesController::class, 'updateStatus'])->name('types.status');
        Route::post('types/update', [App\Http\Controllers\Admin\Contact\TypesController::class, 'update'])->name('types.update');
        Route::get('types/get', [App\Http\Controllers\Admin\Contact\TypesController::class, 'getType'])->name('types.get');
        Route::post('types/delete', [App\Http\Controllers\Admin\Contact\TypesController::class, 'delete'])->name('types.delete');
        Route::post('types/delete-multiple', [App\Http\Controllers\Admin\Contact\TypesController::class, 'deleteMultiple'])->name('types.multi.delete');

        Route::get('contact/source', [App\Http\Controllers\Admin\Contact\SourcesController::class, 'index'])->name('contact.sources');
    });

    Route::group(["prefix" => "temp-listings", "as" => "admin.temp-listing."], function () {
        Route::get("", [\App\Http\Controllers\Admin\Listing\TempPropertyController::class, "index"])->name("index");
        Route::group(["prefix" => "properties", "as" => "properties."], function () {
            Route::match(["get", "post"], 'get', [App\Http\Controllers\Admin\Listing\TempPropertyController::class, 'getProperty'])->name('get');
            Route::post('delete', [App\Http\Controllers\Admin\Listing\TempPropertyController::class, 'delete'])->name('delete');
            Route::post('datatable', [App\Http\Controllers\Admin\Listing\TempPropertyController::class, 'dataTable'])->name('dt');
        });
    });

    Route::group(["prefix" => "listings", "as" => "admin.listing."], function () {
        Route::get("", [\App\Http\Controllers\Admin\Listing\PropertyController::class, "index"])->name("index");
        Route::post("bulk-download-watermarked-images", [\App\Http\Controllers\Admin\Listing\PropertyController::class, "bulkMarkedImages"])->name("download.bulk.watermarked.images");
        Route::post("bulk-download-images", [\App\Http\Controllers\Admin\Listing\PropertyController::class, "bulkImages"])->name("download.bulk.withoutwatermarked.images");
        Route::post("bulk-download-not-for-marketing-images", [\App\Http\Controllers\Admin\Listing\PropertyController::class, "bulkDownloadNotForMarketingImages"])->name("download.bulk.notForMarketing.images");
        Route::post("bulk-download-documents", [\App\Http\Controllers\Admin\Listing\PropertyController::class, "bulkDocuments"])->name("download.bulk.documents");

        Route::get('get-agents', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'getAgents'])->name('getAgents');
        Route::get('update-properties-description', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'updateDescription'])->name('updateDescription');

        Route::get("unpublished-with-a-note", [\App\Http\Controllers\Admin\Listing\PropertyController::class, "unpublishedListings"])->name("unpublished.index");

        // Route::group(["prefix" => "rent", "as" => "rent."], function () {
        //     Route::get('', [App\Http\Controllers\Admin\Listing\RentListingController::class, 'index'])->name('index');
        //     Route::post('store', [App\Http\Controllers\Admin\Listing\RentListingController::class, 'store'])->name('store');
        //     Route::post('update', [App\Http\Controllers\Admin\Listing\RentListingController::class, 'update'])->name('update');
        //     Route::post('datatable', [App\Http\Controllers\Admin\Listing\RentListingController::class, 'dataTable'])->name('dt');

        // });

        // Route::group(["prefix" => "sale", "as" => "sale."], function () {
        //     Route::get('', [App\Http\Controllers\Admin\Listing\SaleListingController::class, 'index'])->name('index');
        //     Route::post('store', [App\Http\Controllers\Admin\Listing\SaleListingController::class, 'store'])->name('store');
        //     Route::post('update', [App\Http\Controllers\Admin\Listing\SaleListingController::class, 'update'])->name('update');
        //     Route::post('datatable', [App\Http\Controllers\Admin\Listing\SaleListingController::class, 'dataTable'])->name('dt');
        // });

        Route::group(["prefix" => "properties", "as" => "properties."], function () {

            Route::match(["get", "post"], 'get', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'getProperty'])->name('get');
            Route::post('delete', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'deleteMultiple'])->name('multi.delete');
            Route::post('bulk-assign-agents', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'bulkAssignAgents'])->name('bulk.assign.agents');
            Route::post('upload-photo', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'uploadPhoto'])->name('upload.photo');
            Route::post('re-order-photos', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'reOrderPhotos'])->name('re-order-photos');
            Route::post('remove-photo', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'removePhoto'])->name('remove.photo');
            Route::post('remove-portal', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'removeFromPortal'])->name('remove.portal');
            Route::post('toggle-hot', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'toggleHot'])->name('toggle.hot');
            Route::post('add-note', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'addNote'])->name('add.note');
            Route::post('duplicate', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'duplicateProperty'])->name('duplicate');
            Route::post('publish-request', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'publishRequest'])->name('publish-request');
            Route::post('unpublish-request-admin', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'publishRequestByAdmin'])->name('unpublish.request.admin');
            Route::post('unpublish-request', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'unpublishRequest'])->name('unpublish-request');
            Route::post('assign-agents', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'assignAgents'])->name('assign-agents');
            Route::post('store', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'storeRevamped'])->name('store');
            Route::post('update', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'update'])->name('update');
            Route::post('datatable', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'dataTable'])->name('dt');
            Route::get('export-excel', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'exportExcel'])->name('export-excel');
            Route::get('get-activity-log', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'getActivityLog'])->name('activity.log');
            Route::post('archive', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'archive'])->name('archive');
            Route::post('get-duplicate-listings', [App\Http\Controllers\Admin\Listing\ListingController::class, 'checkDuplicateListings'])->name('get.duplicate');
            Route::post('rera-validity-check', [App\Http\Controllers\Extra\PropertyReraController::class, 'propertyCheckRera'])->name('rera.validity.check');

            Route::get('get-related-events', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'getEvents'])->name('getEvents');

            Route::post('delete-multiple-images', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'deleteMultipleImages'])->name('multi.delete.images');
            Route::post('refuseProperty', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'refuseProperty'])->name('refuseProperty');
            Route::post('approveProperty', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'approveProperty'])->name('approveProperty');

            Route::post('getChangeInListing', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'getChangeInListing'])->name('getChangeInListing');
            Route::post('get-property-timeline', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'getPropertyTimeline'])->name('getPropertyTimeline');
            Route::post('request-download-zip', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'requestDownloadZip'])->name('requestDownloadZip');

            Route::get('filtered-helper', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'filterHelper'])->name('filterHelper');
            Route::post('open-predefined-filter', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'filterOpener'])->name('filterOpener');
            Route::get('open-predefined-filter/{id}', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'filterOpenerRedirect'])->name('filterOpenerRedirect');

            Route::post('republish-request', [App\Http\Controllers\Admin\Listing\PropertyController::class, 'republishRequest'])->name('republishRequest');

            Route::get('get-pf-upgrades', [App\Http\Controllers\Admin\Listing\ListingPortalController::class, 'getPfListingUpgrades'])->name('pf.get.upgrades');
            Route::post('buy-pf-upgrades', [App\Http\Controllers\Admin\Listing\ListingPortalController::class, 'upgradePfListing'])->name('pf.buy.upgrades');
            Route::get('get-pfv2-listing-quality-score', [App\Http\Controllers\Admin\Listing\ListingPortalController::class, 'getPfListingDetails'])->name('pf.listing-quality-score');;

        });
    });

    Route::group(["prefix" => "leads", "as" => "admin.lead."], function () {

        Route::get('source', [App\Http\Controllers\Admin\Lead\SourceController::class, 'index'])->name('source');
        Route::post('source/store', [App\Http\Controllers\Admin\Lead\SourceController::class, 'store'])->name('source.store');
        Route::post('source/dt', [App\Http\Controllers\Admin\Lead\SourceController::class, 'sourceForDatatable'])->name('source.dt');
        Route::post('source/status', [App\Http\Controllers\Admin\Lead\SourceController::class, 'updateStatus'])->name('source.status');
        Route::post('source/update', [App\Http\Controllers\Admin\Lead\SourceController::class, 'update'])->name('source.update');
        Route::get('source/get', [App\Http\Controllers\Admin\Lead\SourceController::class, 'getSource'])->name('source.get');
        Route::post('source/delete', [App\Http\Controllers\Admin\Lead\SourceController::class, 'delete'])->name('source.delete');
        Route::post('source/delete-multiple', [App\Http\Controllers\Admin\Lead\SourceController::class, 'deleteMultiple'])->name('source.multi.delete');

        Route::get('status', [App\Http\Controllers\Admin\Lead\StatusController::class, 'index'])->name('status');
        Route::post('status/store', [App\Http\Controllers\Admin\Lead\StatusController::class, 'store'])->name('status.store');
        Route::post('status/dt', [App\Http\Controllers\Admin\Lead\StatusController::class, 'statusForDatatable'])->name('status.dt');
        Route::post('status/status', [App\Http\Controllers\Admin\Lead\StatusController::class, 'updateStatus'])->name('status.status');
        Route::post('status/update', [App\Http\Controllers\Admin\Lead\StatusController::class, 'update'])->name('status.update');
        Route::get('status/get', [App\Http\Controllers\Admin\Lead\StatusController::class, 'getStatus'])->name('status.get');
        Route::post('status/delete', [App\Http\Controllers\Admin\Lead\StatusController::class, 'delete'])->name('status.delete');
        Route::post('status/delete-multiple', [App\Http\Controllers\Admin\Lead\StatusController::class, 'deleteMultiple'])->name('status.multi.delete');

        Route::get('type', [App\Http\Controllers\Admin\Lead\TypeController::class, 'index'])->name('type');
        Route::post('type/store', [App\Http\Controllers\Admin\Lead\TypeController::class, 'store'])->name('type.store');
        Route::post('type/dt', [App\Http\Controllers\Admin\Lead\TypeController::class, 'typeForDatatable'])->name('type.dt');
        Route::post('type/status', [App\Http\Controllers\Admin\Lead\TypeController::class, 'updateStatus'])->name('type.status');
        Route::post('type/update', [App\Http\Controllers\Admin\Lead\TypeController::class, 'update'])->name('type.update');
        Route::get('type/get', [App\Http\Controllers\Admin\Lead\TypeController::class, 'getType'])->name('type.get');
        Route::post('type/delete', [App\Http\Controllers\Admin\Lead\TypeController::class, 'delete'])->name('type.delete');
        Route::post('type/delete-multiple', [App\Http\Controllers\Admin\Lead\TypeController::class, 'deleteMultiple'])->name('type.multi.delete');

        Route::get('sub-status', [App\Http\Controllers\Admin\Lead\LeadSubStatusController::class, 'index'])->name('sub.statuses');
        Route::post('sub-status/store', [App\Http\Controllers\Admin\Lead\LeadSubStatusController::class, 'store'])->name('sub.statuses.store');
        Route::post('sub-status/dt', [App\Http\Controllers\Admin\Lead\LeadSubStatusController::class, 'substatusForDatatable'])->name('sub.statuses.dt');
        Route::post('sub-status/status', [App\Http\Controllers\Admin\Lead\LeadSubStatusController::class, 'updateStatus'])->name('sub.statuses.status');
        Route::post('sub-status/update', [App\Http\Controllers\Admin\Lead\LeadSubStatusController::class, 'update'])->name('sub.statuses.update');
        Route::get('sub-status/get', [App\Http\Controllers\Admin\Lead\LeadSubStatusController::class, 'getStatus'])->name('sub.statuses.get');
        Route::post('sub-status/delete', [App\Http\Controllers\Admin\Lead\LeadSubStatusController::class, 'delete'])->name('sub.statuses.delete');
        Route::post('sub-status/delete-multiple', [App\Http\Controllers\Admin\Lead\LeadSubStatusController::class, 'deleteMultiple'])->name('sub.statuses.multi.delete');

        Route::get('feature', [App\Http\Controllers\Admin\Property\FeatureController::class, 'index'])->name('feature');

    });

    //location
    Route::group(["prefix" => "location", "as" => "admin.location."], function () {
        Route::group(["prefix" => "region", "as" => "region."], function () {
            Route::get('', [App\Http\Controllers\Admin\Location\RegionController::class, 'index'])->name('index');
            Route::post('', [App\Http\Controllers\Admin\Location\RegionController::class, 'store'])->name('store');
            Route::match(["get", "post"], "dt", [App\Http\Controllers\Admin\Location\RegionController::class, 'regionForDatatable'])->name("dt");
            Route::post('update', [App\Http\Controllers\Admin\Location\RegionController::class, 'update'])->name('update');
            Route::get('get', [App\Http\Controllers\Admin\Location\RegionController::class, 'getRegion'])->name('get');
            Route::post('status', [App\Http\Controllers\Admin\Location\RegionController::class, 'updateStatus'])->name('status');
            Route::post('delete', [App\Http\Controllers\Admin\Location\RegionController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\Location\RegionController::class, 'deleteMultiple'])->name('multi.delete');
        });

        Route::group(["prefix" => "communities", "as" => "communities."], function () {
            Route::get('', [App\Http\Controllers\Admin\Location\CommunitiesController::class, 'index'])->name('index');
            Route::post('', [App\Http\Controllers\Admin\Location\CommunitiesController::class, 'store'])->name('store');
            Route::match(["get", "post"], "dt", [App\Http\Controllers\Admin\Location\CommunitiesController::class, 'communityForDatatable'])->name("dt");
            Route::post('update', [App\Http\Controllers\Admin\Location\CommunitiesController::class, 'update'])->name('update');
            Route::get('get', [App\Http\Controllers\Admin\Location\CommunitiesController::class, 'getCommunity'])->name('get');
            Route::post('status', [App\Http\Controllers\Admin\Location\CommunitiesController::class, 'updateStatus'])->name('status');
            Route::post('delete', [App\Http\Controllers\Admin\Location\CommunitiesController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\Location\CommunitiesController::class, 'deleteMultiple'])->name('multi.delete');
        });

        Route::group(["prefix" => "sub-communities", "as" => "sub-communities."], function () {
            Route::get('', [App\Http\Controllers\Admin\Location\SubCommunityController::class, 'index'])->name('index');
            Route::post('', [App\Http\Controllers\Admin\Location\SubCommunityController::class, 'store'])->name('store');
            Route::match(["get", "post"], "dt", [App\Http\Controllers\Admin\Location\SubCommunityController::class, 'subCommunityForDatatable'])->name("dt");
            Route::post('update', [App\Http\Controllers\Admin\Location\SubCommunityController::class, 'update'])->name('update');
            Route::get('get', [App\Http\Controllers\Admin\Location\SubCommunityController::class, 'getSubCommunity'])->name('get');
            Route::post('status', [App\Http\Controllers\Admin\Location\SubCommunityController::class, 'updateStatus'])->name('status');
            Route::post('delete', [App\Http\Controllers\Admin\Location\SubCommunityController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\Location\SubCommunityController::class, 'deleteMultiple'])->name('multi.delete');
        });

        Route::group(["prefix" => "community-properties", "as" => "community-properties."], function () {
            Route::get('', [App\Http\Controllers\Admin\Location\CommunityPropertyController::class, 'index'])->name('index');
            Route::post('', [App\Http\Controllers\Admin\Location\CommunityPropertyController::class, 'store'])->name('store');
            Route::match(["get", "post"], "dt", [App\Http\Controllers\Admin\Location\CommunityPropertyController::class, 'datatable'])->name("dt");
            Route::post('update', [App\Http\Controllers\Admin\Location\CommunityPropertyController::class, 'update'])->name('update');
            Route::get('get', [App\Http\Controllers\Admin\Location\CommunityPropertyController::class, 'getCommunityProperty'])->name('get');
            Route::post('status', [App\Http\Controllers\Admin\Location\CommunityPropertyController::class, 'updateStatus'])->name('status');
            Route::post('delete', [App\Http\Controllers\Admin\Location\CommunityPropertyController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\Location\CommunityPropertyController::class, 'deleteMultiple'])->name('multi.delete');
        });
        Route::group(["prefix" => "extra", "as" => "extra."], function () {
            Route::get('search-at-pf', [App\Http\Controllers\Admin\Location\PfLocationController::class, 'searchAtPf'])->name('pf.search');
            Route::post('map-pf-location', [App\Http\Controllers\Admin\Location\PfLocationController::class, 'mapPFlocation'])->name('pf.map.location');
        });

    });

    Route::group(["prefix" => "deals", "as" => "admin.deals."], function () {
        Route::group(["prefix" => "sources", "as" => "sources."], function () {
            Route::get('', [App\Http\Controllers\Admin\Deals\SourcesController::class, 'index'])->name('index');
            Route::post('store', [App\Http\Controllers\Admin\Deals\SourcesController::class, 'store'])->name('store');
            Route::post('dt', [App\Http\Controllers\Admin\Deals\SourcesController::class, 'sourceForDatatable'])->name('dt');
            Route::post('status', [App\Http\Controllers\Admin\Deals\SourcesController::class, 'updateStatus'])->name('status');
            Route::post('update', [App\Http\Controllers\Admin\Deals\SourcesController::class, 'update'])->name('update');
            Route::get('get', [App\Http\Controllers\Admin\Deals\SourcesController::class, 'getSource'])->name('get');
            Route::post('delete', [App\Http\Controllers\Admin\Deals\SourcesController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\Deals\SourcesController::class, 'deleteMultiple'])->name('multi.delete');
        });

        Route::group(["prefix" => "types", "as" => "types."], function () {
            Route::get('', [App\Http\Controllers\Admin\Deals\TypesController::class, 'index'])->name('index');
            Route::post('store', [App\Http\Controllers\Admin\Deals\TypesController::class, 'store'])->name('store');
            Route::post('dt', [App\Http\Controllers\Admin\Deals\TypesController::class, 'typeForDatatable'])->name('dt');
            Route::post('status', [App\Http\Controllers\Admin\Deals\TypesController::class, 'updateStatus'])->name('status');
            Route::post('update', [App\Http\Controllers\Admin\Deals\TypesController::class, 'update'])->name('update');
            Route::get('get', [App\Http\Controllers\Admin\Deals\TypesController::class, 'getType'])->name('get');
            Route::post('delete', [App\Http\Controllers\Admin\Deals\TypesController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\Deals\TypesController::class, 'deleteMultiple'])->name('multi.delete');
        });

        Route::group(["prefix" => "statuses", "as" => "statuses."], function () {
            Route::get('', [App\Http\Controllers\Admin\Deals\StatusController::class, 'index'])->name('index');
            Route::post('store', [App\Http\Controllers\Admin\Deals\StatusController::class, 'store'])->name('store');
            Route::post('dt', [App\Http\Controllers\Admin\Deals\StatusController::class, 'dealsForDatatable'])->name('dt');
            Route::post('status', [App\Http\Controllers\Admin\Deals\StatusController::class, 'updateStatus'])->name('status');
            Route::post('update', [App\Http\Controllers\Admin\Deals\StatusController::class, 'update'])->name('update');
            Route::get('get', [App\Http\Controllers\Admin\Deals\StatusController::class, 'getSubStatus'])->name('get');
            Route::post('delete', [App\Http\Controllers\Admin\Deals\StatusController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\Deals\StatusController::class, 'deleteMultiple'])->name('multi.delete');
        });

        Route::group(["prefix" => "sub-status", "as" => "sub.statuses."], function () {
            Route::get('', [App\Http\Controllers\Admin\Deals\DealSubStatusController::class, 'index'])->name('index');
            Route::post('store', [App\Http\Controllers\Admin\Deals\DealSubStatusController::class, 'store'])->name('store');
            Route::post('dt', [App\Http\Controllers\Admin\Deals\DealSubStatusController::class, 'dealsForDatatable'])->name('dt');
            Route::post('status', [App\Http\Controllers\Admin\Deals\DealSubStatusController::class, 'updateStatus'])->name('status');
            Route::post('update', [App\Http\Controllers\Admin\Deals\DealSubStatusController::class, 'update'])->name('update');
            Route::get('get', [App\Http\Controllers\Admin\Deals\DealSubStatusController::class, 'getSubDeals'])->name('get');
            Route::post('delete', [App\Http\Controllers\Admin\Deals\DealSubStatusController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\Deals\DealSubStatusController::class, 'deleteMultiple'])->name('multi.delete');
        });

        Route::group(["prefix" => "transaction", "as" => "transaction."], function () {
            Route::get('', [App\Http\Controllers\Admin\Deals\TransactionTypeController::class, 'index'])->name('index');
            Route::post('store', [App\Http\Controllers\Admin\Deals\TransactionTypeController::class, 'store'])->name('store');
            Route::post('dt', [App\Http\Controllers\Admin\Deals\TransactionTypeController::class, 'transactionForDatatable'])->name('dt');
            Route::post('status', [App\Http\Controllers\Admin\Deals\TransactionTypeController::class, 'updateStatus'])->name('status');
            Route::post('update', [App\Http\Controllers\Admin\Deals\TransactionTypeController::class, 'update'])->name('update');
            Route::get('get', [App\Http\Controllers\Admin\Deals\TransactionTypeController::class, 'getTransaction'])->name('get');
            Route::post('delete', [App\Http\Controllers\Admin\Deals\TransactionTypeController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\Deals\TransactionTypeController::class, 'deleteMultiple'])->name('multi.delete');
        });
    });
    Route::group(["prefix" => "widgets", "as" => "admin.widget."], function () {
        Route::post('events', [App\Http\Controllers\Admin\Widget\WidgetController::class, 'eventWidget'])->name('events');
        Route::post('events/{user}', [App\Http\Controllers\Admin\Widget\WidgetController::class, 'eventWidget'])->name('events.user');
        Route::get('call-log', [App\Http\Controllers\Admin\Widget\WidgetController::class, 'getCallLog'])->name('events.call.log');
    });

    Route::group(["prefix" => "configuration", "as" => "admin.configuration."], function () {
        Route::group(["prefix" => "email-settings", "as" => "email-settings."], function () {
            Route::get("", [App\Http\Controllers\Admin\Configuration\EmailSettingController::class, 'index'])->name("index");
            Route::post("", [App\Http\Controllers\Admin\Configuration\EmailSettingController::class, 'store'])->name("store");
            Route::post("update", [App\Http\Controllers\Admin\Configuration\EmailSettingController::class, 'update'])->name("update");
            Route::post("status", [App\Http\Controllers\Admin\Configuration\EmailSettingController::class, 'updateStatus'])->name("status");
            Route::match(["get", "post"], "dt", [App\Http\Controllers\Admin\Configuration\EmailSettingController::class, 'settingsForDatatable'])->name("dt");
            Route::match(["get", "post"], "get", [App\Http\Controllers\Admin\Configuration\EmailSettingController::class, 'getSetting'])->name("get");
            Route::post('delete', [App\Http\Controllers\Admin\Configuration\EmailSettingController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\Configuration\EmailSettingController::class, 'deleteMultiple'])->name('multi.delete');
        });
        Route::group(["prefix" => "notification-settings", "as" => "notification-settings."], function () {
            Route::get("", [App\Http\Controllers\Admin\Configuration\NotificationController::class, 'index'])->name("index");
            Route::post("store", [App\Http\Controllers\Admin\Configuration\NotificationController::class, 'store'])->name("store");
            Route::post("update", [App\Http\Controllers\Admin\Configuration\NotificationController::class, 'update'])->name("update");
            Route::post("update-status", [App\Http\Controllers\Admin\Configuration\NotificationController::class, 'updateStatus'])->name("status");
            Route::post("delete", [App\Http\Controllers\Admin\Configuration\NotificationController::class, 'delete'])->name("delete");
            Route::get("get-notification", [App\Http\Controllers\Admin\Configuration\NotificationController::class, 'getNotificationEvent'])->name("get");
            Route::post("datatable", [App\Http\Controllers\Admin\Configuration\NotificationController::class, 'settingsForDatatable'])->name("datatable");
        });
        Route::group(["prefix" => "cronjob", "as" => "cronjobs."], function () {
            Route::get("/", [App\Http\Controllers\Admin\Configuration\CronjobController::class, 'index'])->name("index");
            Route::get("get-event-list", [App\Http\Controllers\Admin\Configuration\CronjobController::class, 'getTimingList'])->name("timelist");

        });

        Route::group(["prefix" => "traccar", "as" => "traccar."], function () {
            Route::get("/client-settings", [App\Http\Controllers\Admin\Configuration\TraccarController::class, 'client_settings'])->name("client_settings");
            Route::post("update_client_settings", [App\Http\Controllers\Admin\Configuration\TraccarController::class, 'update_client_settings'])->name("update_client_settings");

        });

        Route::group(["prefix" => "devices", "as" => "devices."], function () {
            Route::get('/', [App\Http\Controllers\Admin\Device\DeviceController::class, 'index'])->name('index');
            Route::post('dt', [App\Http\Controllers\Admin\Device\DeviceController::class, 'datatable'])->name('dt');
            Route::post('delete-device', [App\Http\Controllers\Admin\Device\DeviceController::class, 'destroy'])->name('delete');
        });

    });

    Route::group(["prefix" => "settings", "as" => "admin.settings."], function () {
        Route::group(["prefix" => "portals", "as" => "portals."], function () {
            Route::get("", [App\Http\Controllers\Admin\Setting\PortalController::class, 'index'])->name("index");
            Route::post("", [App\Http\Controllers\Admin\Setting\PortalController::class, 'store'])->name("store");
            Route::post("update", [App\Http\Controllers\Admin\Setting\PortalController::class, 'update'])->name("update");
            Route::post("status", [App\Http\Controllers\Admin\Setting\PortalController::class, 'updateStatus'])->name("status");
            Route::match(["get", "post"], "dt", [App\Http\Controllers\Admin\Setting\PortalController::class, 'dt'])->name("dt");
            Route::match(["get", "post"], "get", [App\Http\Controllers\Admin\Setting\PortalController::class, 'getPortal'])->name("get");
            Route::post('delete', [App\Http\Controllers\Admin\Setting\PortalController::class, 'delete'])->name('delete');
            Route::post('delete-multiple', [App\Http\Controllers\Admin\Setting\PortalController::class, 'deleteMultiple'])->name('multi.delete');
        });

        Route::group(["prefix" => "general", "as" => "general."], function () {
            Route::get("", [App\Http\Controllers\Admin\Setting\GeneralSettingController::class, 'index'])->name("index");
            Route::post("update", [App\Http\Controllers\Admin\Setting\GeneralSettingController::class, 'store'])->name("store");
            Route::post("get-settings", [App\Http\Controllers\Admin\Setting\GeneralSettingController::class, 'getSettings'])->name("getSettings");
            Route::post("delete-settings", [App\Http\Controllers\Admin\Setting\GeneralSettingController::class, 'delete'])->name("delete");

        });

        Route::group(["prefix" => "group", "as" => "group."], function () {
            Route::get("", [App\Http\Controllers\Admin\Group\GroupController::class, 'index'])->name("index");
            Route::post("dt", [App\Http\Controllers\Admin\Group\GroupController::class, 'datatable'])->name("datatable");
            Route::post("store", [App\Http\Controllers\Admin\Group\GroupController::class, 'store'])->name("store");
            Route::post("update/{id}", [App\Http\Controllers\Admin\Group\GroupController::class, 'update'])->name("update");
            Route::post("get-groups", [App\Http\Controllers\Admin\Group\GroupController::class, 'getGroups'])->name("getGroups");
            Route::get("g/{id}", [App\Http\Controllers\Admin\Group\GroupController::class, 'edit'])->name("edit");
            Route::post("get-group-users", [App\Http\Controllers\Admin\Group\GroupController::class, 'getGroupUsers'])->name("getGroupUsers");
            Route::post("get-group-users/save", [App\Http\Controllers\Admin\Group\GroupController::class, 'userGroupsSave'])->name("userGroupsSave");
            Route::get("/tree", [App\Http\Controllers\Admin\Group\GroupController::class, 'makeTree'])->name("makeTree");
            Route::post("/delete-group", [App\Http\Controllers\Admin\Group\GroupController::class, 'deleteGroup'])->name("deleteGroup");
            Route::post("/delete-all-group", [App\Http\Controllers\Admin\Group\GroupController::class, 'deleteGroupAll'])->name("deleteGroup.all");

        });

    });
    Route::group(["prefix" => "my-account", "as" => "admin.my-account."], function () {
        Route::group(["prefix" => "profile", "as" => "profile."], function () {
            Route::get("", [App\Http\Controllers\Admin\MyAccount\ProfileController::class, 'myProfile'])->name("index");
            Route::post("save-language", [App\Http\Controllers\Admin\MyAccount\ProfileController::class, 'saveLanguages'])->name("saveLanguages");
            Route::post("get-languags", [App\Http\Controllers\Admin\MyAccount\ProfileController::class, 'getLanguages'])->name("getLanguages");
            Route::post("delete-languags", [App\Http\Controllers\Admin\MyAccount\ProfileController::class, 'deleteLanguages'])->name("deleteLanguages");

        });
    });

    Route::group(["prefix" => "events", "as" => "admin.events."], function () {
        // TO BE REMOVED STARTS
        Route::get("test", [\App\Http\Controllers\Admin\Event\TestEventController::class, "test"]);
        Route::get("social", [\App\Http\Controllers\Admin\Event\TestEventController::class, "social"]);
        Route::get("redirect", [\App\Http\Controllers\Admin\Event\TestEventController::class, "redirect"]);
        // TO BE REMOVED ENDS
        Route::get("v2", [\App\Http\Controllers\Admin\Event\EventController::class, "index2"])->name("index2");
        Route::get("", [\App\Http\Controllers\Admin\Event\EventController::class, "index"])->name("index");
        Route::post("", [\App\Http\Controllers\Admin\Event\EventController::class, "store"])->name("store");
        Route::post("update", [\App\Http\Controllers\Admin\Event\EventController::class, "update"])->name("update");
        Route::post("update-date", [\App\Http\Controllers\Admin\Event\EventController::class, "updateDate"])->name("update.date");
        Route::match(["get", "post"], "get", [\App\Http\Controllers\Admin\Event\EventController::class, "getEvent"])->name("get");
        Route::post("delete", [\App\Http\Controllers\Admin\Event\EventController::class, "delete"])->name("delete");
        Route::post("get-all-event", [\App\Http\Controllers\Admin\Event\EventController::class, "getEventAllLog"])->name("all");
        Route::get("event-for-calendar", [\App\Http\Controllers\Admin\Event\EventController::class, "eventCalendar"])->name("calendar");
        Route::post("user-event", [\App\Http\Controllers\Admin\Event\EventController::class, "getUserEvents"])->name("calendar.user");
        Route::get("get-event-form", [\App\Http\Controllers\Admin\Event\EventController::class, "getFormModal"])->name("calendar.form.modal");
        Route::post("get-event-summary", [\App\Http\Controllers\Admin\Event\EventController::class, "getEventSummary"])->name("calendar.form.event.summary");

        Route::get("types", [\App\Http\Controllers\Admin\Event\EventTypeController::class, "index"])->name("type.index");
        Route::post('types/store', [App\Http\Controllers\Admin\Event\EventTypeController::class, 'store'])->name('type.store');
        Route::post('types/dt', [App\Http\Controllers\Admin\Event\EventTypeController::class, 'datatable'])->name('type.dt');
        Route::post('types/status', [App\Http\Controllers\Admin\Event\EventTypeController::class, 'updateStatus'])->name('type.status');
        Route::post('types/update', [App\Http\Controllers\Admin\Event\EventTypeController::class, 'update'])->name('type.update');
        Route::get('types/get', [App\Http\Controllers\Admin\Event\EventTypeController::class, 'getStatus'])->name('type.get');
        Route::get('types/get-all', [App\Http\Controllers\Admin\Event\EventTypeController::class, 'getAllTypes'])->name('type.get.all');
        Route::post('types/delete', [App\Http\Controllers\Admin\Event\EventTypeController::class, 'delete'])->name('type.delete');
        Route::post('types/delete-multiple', [App\Http\Controllers\Admin\Event\EventTypeController::class, 'deleteMultiple'])->name('type.multi.delete');

    });

    Route::group(["prefix" => "oauth", "as" => "oauth."], function () {
        Route::get("authenticate", [App\Http\Controllers\Admin\Auth\OAuthController::class, "authenticate"])->name("authenticate");
        Route::get("authenticated-callback", [App\Http\Controllers\Admin\Auth\OAuthController::class, "authenticateedCallback"])->name("authenticated-callback");
    });

    Route::group(["prefix" => "listing-leads", "as" => "listings.leads."], function () {
        Route::get("/", [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, "index"])->name("index");
        Route::get('get', [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, 'getLead'])->name('get');
        Route::post('/store', [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, 'store'])->name('store');
        Route::post('source/dt', [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, 'datatable'])->name('dt');
        Route::post('update', [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, 'update'])->name('update');
        Route::post('bulk-assign', [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, 'generalBulk'])->name('general.bulk.assign.leads.listing');
        Route::post('archive', [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, 'archive'])->name('archive');
        Route::post('delete-multiple', [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, 'deleteMultiple'])->name('delete.multi');
        Route::post('add-note', [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, 'addNote'])->name('add-note');
        Route::post('convert-to-listing', [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, 'convertToListing'])->name('convertToListing');
        Route::post('lead-to-listing/convert', [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, 'leadToListing'])->name('leadToListing');
        Route::post('start-call', [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, 'startCall'])->name('start-call');
        Route::post('lead-to-listing/check-duplicate', [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, 'leadToListingCheckDuplicate'])->name('leadToListingCheckDuplicate');

        Route::get('export-excel', [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, 'exportExcel'])->name('export-excel');
        Route::post('unsuccessful-listing-lead', [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, 'saveReason'])->name('saveReason.listingleads');
        Route::get('export-dialmydata', [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, 'leadExport'])->name('leadExport');

        Route::get('status', [App\Http\Controllers\Admin\ListingLeads\StatusController::class, 'index'])->name('status');
        Route::post('status/store', [App\Http\Controllers\Admin\ListingLeads\StatusController::class, 'store'])->name('status.store');
        Route::post('status/dt', [App\Http\Controllers\Admin\ListingLeads\StatusController::class, 'statusForDatatable'])->name('status.dt');
        Route::post('status/status', [App\Http\Controllers\Admin\ListingLeads\StatusController::class, 'updateStatus'])->name('status.status');
        Route::post('status/update', [App\Http\Controllers\Admin\ListingLeads\StatusController::class, 'update'])->name('status.update');
        Route::get('status/get', [App\Http\Controllers\Admin\ListingLeads\StatusController::class, 'getStatus'])->name('status.get');
        Route::post('status/delete', [App\Http\Controllers\Admin\ListingLeads\StatusController::class, 'delete'])->name('status.delete');
        Route::post('status/delete-multiple', [App\Http\Controllers\Admin\ListingLeads\StatusController::class, 'deleteMultiple'])->name('status.multi.delete');
        Route::get('filtered-helper', [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, 'filterHelper'])->name('filterHelper');
        Route::get('property/autocomplete', [App\Http\Controllers\Admin\ListingLeads\ListingleadController::class, 'autocomplete'])->name('autocomplete');

    });
    Route::group(["prefix" => "security/traccar-recording", "as" => "admin.traccar.recording.", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
        Route::get('/', [App\Http\Controllers\Admin\Traccar\TraccarRecordingController::class, 'index'])->name('index');
        Route::post('/dt', [App\Http\Controllers\Admin\Traccar\TraccarRecordingController::class, 'datatable'])->name('dt');
        Route::get('/show/{id}', [App\Http\Controllers\Admin\Traccar\TraccarRecordingController::class, 'show'])->name('show');

    });

    Route::group(["prefix" => "criteria-mactching", "as" => "admin.criteria.matching.", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
        Route::get('/', [App\Http\Controllers\Admin\CriteriaMatching\CriteriaMatchingController::class, 'index'])->name('index');
        Route::post('/fetch-details', [App\Http\Controllers\Admin\CriteriaMatching\CriteriaMatchingController::class, 'fetchDetails'])->name('fetchDetails');
        Route::post('/submit-criteria', [App\Http\Controllers\Admin\CriteriaMatching\CriteriaMatchingController::class, 'returnModel'])->name('returnModel');
    });

    Route::group(["prefix" => "ax-calculator", "as" => "admin.ax.calculator.", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
        Route::get('/', [App\Http\Controllers\Admin\Calculator\CalculatorController::class, 'index'])->name('index');
    });
    Route::group(["prefix" => "developers-meta", "as" => "admin.developer.meta.", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
        Route::get('/', [App\Http\Controllers\Admin\Offplan\DeveloperMetaController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\Offplan\DeveloperMetaController::class, 'create'])->name('create');
        Route::post('/store', [App\Http\Controllers\Admin\Offplan\DeveloperMetaController::class, 'store'])->name('store');
        Route::get('edit/{id}', [App\Http\Controllers\Admin\Offplan\DeveloperMetaController::class, 'edit'])->name('edit');
        Route::post('edit/{id}/update', [App\Http\Controllers\Admin\Offplan\DeveloperMetaController::class, 'update'])->name('update');
        Route::post('store/{id}/gallery', [App\Http\Controllers\Admin\Offplan\DeveloperMetaController::class, 'storeGallery'])->name('storeGallery');
        Route::post('/update-property-data', [App\Http\Controllers\Admin\Offplan\DeveloperMetaController::class, 'updateExistingProperty'])->name('updateExistingProperty');
        Route::post('/publish-property-job', [App\Http\Controllers\Admin\Offplan\DeveloperMetaController::class, 'publishOffplanImportedProperty'])->name('publishOffplanImportedProperty');
        Route::post('/update-rera-property', [App\Http\Controllers\Admin\Offplan\DeveloperMetaController::class, 'updateReraNumber'])->name('updateReraNumber');
        Route::post('/delete', [App\Http\Controllers\Admin\Offplan\DeveloperMetaController::class, 'delete'])->name('delete');
    });

    Route::group(["prefix" => "jobs", "as" => "admin.jobs."], function () {
        Route::get("", [\App\Http\Controllers\Admin\Jobs\JobController::class, "index"])->name("index");
        Route::post("", [\App\Http\Controllers\Admin\Jobs\JobController::class, "store"])->name("store");
        Route::post("update", [\App\Http\Controllers\Admin\Jobs\JobController::class, "update"])->name("update");
        Route::match(["get", "post"], "get", [\App\Http\Controllers\Admin\Jobs\JobController::class, "getJob"])->name("get");
        Route::post("delete", [\App\Http\Controllers\Admin\Jobs\JobController::class, "delete"])->name("delete");
        Route::post('delete-multiple', [\App\Http\Controllers\Admin\Jobs\JobController::class, 'deleteMultiple'])->name('multi.delete');
        Route::post('dt', [\App\Http\Controllers\Admin\Jobs\JobController::class, 'dataTable'])->name('dt');
        Route::post('status', [\App\Http\Controllers\Admin\Jobs\JobController::class, 'updateStatus'])->name('status');
        Route::post('add-note', [\App\Http\Controllers\Admin\Jobs\JobController::class, 'addNote'])->name('add-note');

        Route::get("property-shoot", [\App\Http\Controllers\Admin\Jobs\ShootController::class, "index"])->name("shoot.index");
        Route::get("shoot/get", [\App\Http\Controllers\Admin\Jobs\ShootController::class, "getJob"])->name("shoot.get");
        Route::get("get-shoot-form", [\App\Http\Controllers\Admin\Jobs\ShootController::class, "getFormModal"])->name("shoot.form");
        Route::get("get-pg-timeline", [\App\Http\Controllers\Admin\Jobs\ShootController::class, "getPhotographerTimeline"])->name("shoot.pg.timeline");
        Route::post("shoot/store", [\App\Http\Controllers\Admin\Jobs\ShootController::class, "store"])->name("shoot.store");
        Route::post("shoot/update", [\App\Http\Controllers\Admin\Jobs\ShootController::class, "update"])->name("shoot.update");
        Route::post("shoot/datatable", [\App\Http\Controllers\Admin\Jobs\ShootController::class, "dataTable"])->name("shoot.dt");
        Route::post("shoot/delete-multiple", [\App\Http\Controllers\Admin\Jobs\ShootController::class, "deleteMultiple"])->name("shoot.multi.delete");
        Route::post("shoot/delete", [\App\Http\Controllers\Admin\Jobs\ShootController::class, "delete"])->name("shoot.delete");
        Route::post("shoot/assign", [\App\Http\Controllers\Admin\Jobs\ShootController::class, "approveAndAssignToUser"])->name("shoot.assign");
        Route::post("shoot/status-update", [\App\Http\Controllers\Admin\Jobs\ShootController::class, "updateStatus"])->name("shoot.status.update");
        Route::post("shoot/pg/status-update", [\App\Http\Controllers\Admin\Jobs\ShootController::class, "photoGrapherStatusUpdate"])->name("shoot.pg.status.update");
        Route::post("shoot/note/store", [\App\Http\Controllers\Admin\Jobs\ShootController::class, "addNote"])->name("shoot.note.store");
        Route::get("shoot/note/get", [\App\Http\Controllers\Admin\Jobs\ShootController::class, "getNotes"])->name("shoot.note.get");
        Route::get("shoot/activity-log", [\App\Http\Controllers\Admin\Jobs\ShootController::class, "getActivityLog"])->name("shoot.activity.log");
        Route::post("shoot/send-notification", [\App\Http\Controllers\Admin\Jobs\ShootController::class, "manualNotification"])->name("shoot.notification.force");
    });

    Route::group(["prefix" => "messages", "as" => "admin.message."], function () {
        Route::get('inbox', [App\Http\Controllers\Admin\Message\MessageController::class, 'inbox'])->name('inbox');
        Route::post("get-user-notification", [App\Http\Controllers\Admin\Message\MessageController::class, 'getNotificationByNotiUser'])->name("get.related.notification");
        Route::post("mark-read-notification", [App\Http\Controllers\Admin\Message\MessageController::class, 'markAsRead'])->name("mark.read");
        Route::post("mark-all-read-notification", [App\Http\Controllers\Admin\Message\MessageController::class, 'markAllAsRead'])->name("mark.all.read");
        Route::post("mark-unread-notification", [App\Http\Controllers\Admin\Message\MessageController::class, 'markAsUnRead'])->name("mark.unread");
        Route::post('read-notifications', [App\Http\Controllers\Admin\Message\MessageController::class, 'readNotification'])->name('read.notifications');
        Route::get('paginate-notifications', [App\Http\Controllers\Admin\Message\MessageController::class, 'paginateNotification'])->name('notification.paginate');

    });
    Route::group(["prefix" => "app", "as" => "admin.app."], function () {
        Route::get('app-log', [App\Http\Controllers\Admin\App\AppLogController::class, 'index'])->name('log');
        Route::post('app-log-datatable', [App\Http\Controllers\Admin\App\AppLogController::class, 'dataTable'])->name('log.dt');
        Route::get('app-log-get', [App\Http\Controllers\Admin\App\AppLogController::class, 'get'])->name('log.get');
    });

    Route::group(['prefix' => 'listing-verifications', 'as' => 'admin.listing-verifications'], function () {
        Route::get('', [ListingVerificationController::class, 'index'])->name('index');
        Route::get('counts', [ListingVerificationController::class, 'index'])->name('index');
    });
});



Route::get("test-email", [\App\Http\Controllers\TestController::class, "testEmail"])->name("test-email");
Route::get("test-vue", [\App\Http\Controllers\TestController::class, "vueTest"]);
Route::get("pf-create", [\App\Http\Controllers\TestController::class, "createPf"]);
Route::get("pf-delete", [\App\Http\Controllers\TestController::class, "deletePf"]);
Route::get("pf-unpublish", [\App\Http\Controllers\TestController::class, "unPublishAtPf"]);
Route::get("test-pdf", [\App\Http\Controllers\TestController::class, "testPdf"])->name("test-pdf");
Route::get("test-make-call", [\App\Http\Controllers\TestController::class, "testMakeCall"])->name("test-make-call");
Route::get("test-kpi-report", [\App\Http\Controllers\TestKPIController::class, "index"]);
Route::get("auto-login", [App\Http\Controllers\AutoLoginController::class, 'login']);
Route::get("pf-map-location", [App\Http\Controllers\TestController::class, 'mapPFlocation']);
Route::group(["prefix" => "testing-phase", "as" => "testing.bayut", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
    Route::get('bayut-leads', [App\Http\Controllers\Testing\BayutLeadsFetchJobController::class, 'handle'])->name('bayut.handle');
});

//Put this route in the end


Route::group(["prefix" => "collaborator/deals", "as" => "collab.deals.", "middleware" => ['auth']], function () {
    Route::get('/{invite_code}', [App\Http\Controllers\Admin\Deals\ExternalDealPageController::class, 'index'])->name('index');
    Route::post('/store-lead', [App\Http\Controllers\Admin\Deals\ExternalDealPageController::class, 'storeInformation'])->name('storeInformation');



});
Route::group(["prefix" => "collaborator/deals", "as" => "collab.deals.", "middleware" => ['web']], function () {
    Route::get('/user/{invite_code}', [App\Http\Controllers\Admin\Deals\ExternalDealPageController::class, 'leadExternal'])->name('lead.kyc.form');
    Route::post('/external-update-lead', [App\Http\Controllers\Admin\Deals\ExternalDealPageController::class, 'updateLeadInformation'])->name('update.lead.external.user');
    Route::post('update-lead-signed-pdf', [App\Http\Controllers\Admin\Deals\ExternalDealPageController::class, 'uploadSignedPdf'])->name('pdf.upload');
    Route::post('update-lead-sign', [App\Http\Controllers\Admin\Deals\ExternalDealPageController::class, 'signDocument'])->name('sign.document');
});

Route::group(["prefix" => "reports", "as" => "admin.reports.", "middleware" => ["web", "auth", "checkUserStatus",'2fa','tOuth']], function () {
    Route::get('agent', [App\Http\Controllers\Admin\Report\AgentReportController::class, 'index'])->name('agent');
    Route::post('agent-portal-data', [App\Http\Controllers\Admin\Report\AgentReportController::class, 'agentPortalData'])->name('agent.portal');
    Route::post('agent-portal-data-abu-dhabi', [App\Http\Controllers\Admin\Report\AgentReportController::class, 'agentPortalDatAbuDhabi'])->name('agent.portal.abudhabi');
    Route::post('agent-portal-data-primary', [App\Http\Controllers\Admin\Report\AgentReportController::class, 'agentPortalDataPrimary'])->name('agent.portal.primary');
    Route::post('agent-portal-data-commercial', [App\Http\Controllers\Admin\Report\AgentReportController::class, 'agentPortalDataCommercial'])->name('agent.portal.commercial');
    Route::post('agent-portal-data-chinese', [App\Http\Controllers\Admin\Report\AgentReportController::class, 'agentPortalDataChinese'])->name('agent.portal.chinese');
    Route::post('agent-portal-data-secondary', [App\Http\Controllers\Admin\Report\AgentReportController::class, 'agentPortalDataSecondary'])->name('agent.portal.secondary');

    Route::get('get-lead', [App\Http\Controllers\Admin\Report\AgentReportController::class, 'getLeads'])->name('agent.report.leads');
    Route::get('dashboard-portal-pie', [App\Http\Controllers\Admin\Report\AgentReportController::class, 'dashboardPieData'])->name('agent.report.portal');
    Route::get('agent-activity-log', [App\Http\Controllers\Admin\Report\AgentReportController::class, 'getActivityLog'])->name('agent.report.activity.log');
    Route::get('agent-recent-leads', [App\Http\Controllers\Admin\Report\AgentReportController::class, 'getRecentLeads'])->name('agent.report.recent.leads');
    Route::get('agent-listing-recent-leads', [App\Http\Controllers\Admin\Report\AgentReportController::class, 'getRecentListingLeads'])->name('agent.report.recent.listing.leads');



    Route::post('agent-listing-report', [App\Http\Controllers\Admin\Report\AgentReportController::class, 'agentListingReport'])->name('agent.listing.report');
    Route::post('agent-sub-communities-details', [App\Http\Controllers\Admin\Report\AgentReportController::class, 'agentListingReportSubCommunity'])->name('agent.listing.report.sub.community');


    Route::get('calendar', [App\Http\Controllers\Admin\Report\CalendarReportController::class, 'index'])->name('calendar');
    Route::post('calenda-overview', [App\Http\Controllers\Admin\Report\CalendarReportController::class, 'calenderActivities'])->name('eventsOverview');
    Route::post('agent-events-report', [App\Http\Controllers\Admin\Report\CalendarReportController::class, 'agentEventsReport'])->name('agent.events.report');

    //Consolidate Report
    Route::get('agent-consolidate-report', [App\Http\Controllers\Admin\Report\ConsolidateReportController::class, 'index'])->name('agent.consolidate.report');
    Route::get('agent-consolidate-report/landing', [App\Http\Controllers\Admin\Report\ConsolidateReportController::class, 'landing'])->name('agent.consolidate.report.landing');
    Route::get('agent-consolidate-report/refresh', [App\Http\Controllers\Admin\Report\ConsolidateReportController::class, 'runCommand'])->name('agent.consolidate.report.refresh');

    Route::get('groups', [App\Http\Controllers\Admin\Report\GroupReportController::class, 'index'])->name('group');

    Route::get('groups/locations-overview', [App\Http\Controllers\Admin\Report\GroupReportController::class, 'locationOverview'])->name('locationOverview');
    Route::post('groups/locations-overview/fetch', [App\Http\Controllers\Admin\Report\GroupReportController::class, 'locationOverviewFetch'])->name('locationOverviewFetch');

    Route::get('groups/events-overview', [App\Http\Controllers\Admin\Report\GroupReportController::class, 'eventsOverview'])->name('group.eventsOverview');
    Route::post('groups/events-overview/fetch', [App\Http\Controllers\Admin\Report\GroupReportController::class, 'eventsOverviewFetch'])->name('eventsOverviewFetch');
    Route::post('groups/events-overview/graph-fetch', [App\Http\Controllers\Admin\Report\GroupReportController::class, 'eventsMonthlyOverview'])->name('eventsMonthlyOverview');

    Route::get('groups/listings-overview', [App\Http\Controllers\Admin\Report\GroupReportController::class, 'listingStatsOverviewBlade'])->name('listingStatsOverviewBlade');
    Route::get('groups/users-overview', [App\Http\Controllers\Admin\Report\GroupReportController::class, 'UserStatsOverviewBlade'])->name('UserStatsOverviewBlade');

    Route::get('groups/leads-overview', [App\Http\Controllers\Admin\Report\GroupReportController::class, 'leadsStatsOverviewBlade'])->name('leadsStatsOverviewBlade');
    Route::get('groups/listing-leads-overview', [App\Http\Controllers\Admin\Report\GroupReportController::class, 'listingLeadsStatsOverviewBlade'])->name('listingLeadsStatsOverviewBlade');

    Route::POST('make-data', [App\Http\Controllers\Admin\Report\GroupReportController::class, 'processData'])->name('processData');
    Route::get('group-properties-data', [App\Http\Controllers\Admin\Report\GroupReportController::class, 'getPropertiesStatus'])->name('getPropertiesStatus');
    Route::get('group-report-test', [App\Http\Controllers\Admin\Report\GroupReportController::class, 'testFunction'])->name('testFunction');

    Route::get('minimal/groups', [App\Http\Controllers\Admin\Report\GroupReportTableController::class, 'index'])->name('group.minimal');

    Route::get('listing/', [App\Http\Controllers\Admin\Report\ListingReportController::class, 'index'])->name('listings');
    Route::post('listing/dt', [App\Http\Controllers\Admin\Report\ListingReportController::class, 'datatable'])->name('listings.datatable');
    Route::post('listing/update-rera', [App\Http\Controllers\Admin\Report\ListingReportController::class, 'updateReraContract'])->name('listings.updateReraContract');

});
Route::group(["prefix" => "reports/v2", "as" => "reports.group.v2.", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
    Route::get('groups', [App\Http\Controllers\Admin\Report\GroupReportV2Controller::class, 'index'])->name('index');
    Route::POST('make-data', [App\Http\Controllers\Admin\Report\GroupReportV2Controller::class, 'processData'])->name('processData');
});

Route::group(["prefix" => "reports/v3", "as" => "reports.group.v3.", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
    Route::get('groups', [App\Http\Controllers\Admin\Report\GroupReportV3Controller::class, 'index'])->name('index');
    Route::POST('make-data', [App\Http\Controllers\Admin\Report\GroupReportV3Controller::class, 'processData'])->name('processData');
});



Route::group(["prefix" => "reports/v4", "as" => "reports.group.v4.", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
    Route::get('groups', [App\Http\Controllers\Admin\Report\GroupReportV4Controller::class, 'index'])->name('index');
    Route::POST('make-data', [App\Http\Controllers\Admin\Report\GroupReportV4Controller::class, 'processData'])->name('processData');
});
Route::group(["prefix" => "reports/kpi", "as" => "reports.group.kpi.", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
    Route::get('/', [App\Http\Controllers\Admin\Kpi\UserKpiController::class, 'index'])->name('index');
    Route::get('/get', [App\Http\Controllers\Admin\Kpi\UserKpiController::class, 'view'])->name('view');

    Route::get('get-users', [App\Http\Controllers\Admin\Kpi\UserKpiController::class, 'getUsers'])->name('users');
    Route::get('get-model-kpi', [App\Http\Controllers\Admin\Kpi\UserKpiController::class, 'getModelKpi'])->name('model');
    Route::POST('datatable', [App\Http\Controllers\Admin\Kpi\UserKpiController::class, 'datatable'])->name('datatable');
    Route::get('get-user-kpi', [App\Http\Controllers\Admin\Kpi\UserKpiController::class, 'getUserKpi'])->name('user.kpi');
    Route::get('get-kpi-stats', [App\Http\Controllers\Admin\Kpi\UserKpiController::class, 'getkpiStats'])->name('kpi.stats');
    Route::POST('save-parameters', [App\Http\Controllers\Admin\Kpi\UserKpiController::class, 'saveParameters'])->name('save.parameters');
    Route::POST('update-parameters', [App\Http\Controllers\Admin\Kpi\UserKpiController::class, 'updateParameters'])->name('update.parameters');
    Route::get('/view/{id}', [App\Http\Controllers\Admin\Kpi\UserKpiController::class, 'viewPerformace'])->name('viewPerformace');

});

Route::group(["prefix" => "reports/kpi-v2", "as" => "reports.group.kpi-v2.", "middleware" => ["web", "auth", "checkUserStatus","2fa","tOuth"]], function () {
   // Route::get('/', [App\Http\Controllers\Admin\Kpi\KpiV2Controller::class, 'index'])->name('index');
    Route::get('setup', [App\Http\Controllers\Admin\Kpi\KpiV2Controller::class, 'setup'])->name('setup');

    Route::POST('save-bulk-target', [App\Http\Controllers\Admin\Kpi\KpiV2Controller::class, 'saveBulkTarget'])->name('save-bulk-target');
    Route::POST('save-individual-target', [App\Http\Controllers\Admin\Kpi\KpiV2Controller::class, 'saveIndividualTarget'])->name('save-individual-target');
    Route::POST('save-agent-target', [App\Http\Controllers\Admin\Kpi\KpiV2Controller::class, 'saveAgentTarget'])->name('save-agent-target');
    Route::get('get-reports', [App\Http\Controllers\Admin\Kpi\KpiV2Controller::class, 'getReport'])->name('get-report');
    Route::get('view/{agentId}', [App\Http\Controllers\Admin\Kpi\KpiV2Controller::class, 'view'])->name('view');
    Route::get('get-breakdown-data', [App\Http\Controllers\Admin\Kpi\KpiV2Controller::class, 'getBreakdownData'])->name('get-breakdown-data');

    Route::get('/dashboard', [App\Http\Controllers\Admin\Kpi\KpiV2Controller::class, 'index'])->name('v3.index');
    Route::get('get-report', [App\Http\Controllers\Admin\Kpi\KpiDashboardController::class, 'getTargetReport'])->name('get.kpi.report');
    Route::get('get-agent-targets', [App\Http\Controllers\Admin\Kpi\KpiV2Controller::class, 'getIndividualTarget'])->name('kpi.agent.target');
    Route::get('refresh-agent-reports', [App\Http\Controllers\Admin\Kpi\KpiV2Controller::class, 'refreshAgentReports'])->name('kpi.agent.refresh.report');
    Route::get('refresh-team-reports', [App\Http\Controllers\Admin\Kpi\KpiV2Controller::class, 'refreshTeamReports'])->name('kpi.team.refresh.report');


    Route::get('get-agent-dashboard-data', [App\Http\Controllers\Admin\Kpi\KpiDashboardController::class, 'getAgentTargetReport'])->name('agent.dashboard.target.report');
    Route::get('get-agent-live-data', [App\Http\Controllers\Admin\Kpi\KpiDashboardController::class, 'getAgentLiveReportData'])->name('agent.dashboard.live.report');

    Route::get('get-agent-listing-data', [App\Http\Controllers\Admin\Kpi\KpiV2Controller::class, 'getAgentListingData'])->name('agent.listing.data');
    Route::get('get-agent-listing-meta-data', [App\Http\Controllers\Admin\Kpi\KpiV2Controller::class, 'getListingMetaData'])->name('agent.listing.meta.data');

    Route::get('get-teams-total', [App\Http\Controllers\Admin\Kpi\KpiTeamReportController::class, 'getTeamTotals'])->name('team.get.totals');
});
Route::group(["prefix" => "reports/kpi-v2/teams", "as" => "reports.team.kpi-v2.", "middleware" => ["web", "auth", "checkUserStatus","2fa","tOuth"]], function () {
    Route::get('dashboard', [App\Http\Controllers\Admin\Kpi\KpiTeamController::class, 'dashboard'])->name('team.dashboard');
    Route::get('team-reports', [App\Http\Controllers\Admin\Kpi\KpiTeamController::class, 'teamReportPage'])->name('team.report');
    Route::get('team-setup', [App\Http\Controllers\Admin\Kpi\KpiTeamController::class, 'teamTargetSetup'])->name('team.setup');
    Route::post('team-bulk-kpi-save', [App\Http\Controllers\Admin\Kpi\KpiTeamController::class, 'saveBulkTarget'])->name('target.bulk.save');
    Route::post('team-individual-kpi-save', [App\Http\Controllers\Admin\Kpi\KpiTeamController::class, 'saveIndividualTarget'])->name('target.individual.save');
    Route::get('get-team-report', [App\Http\Controllers\Admin\Kpi\KpiTeamReportController::class, 'getTargetReport'])->name('get.report');
    Route::get('team-report/{id}', [App\Http\Controllers\Admin\Kpi\KpiTeamReportController::class, 'teamReportPage'])->name('get.report.page');
    Route::get('team-report-breakdown', [App\Http\Controllers\Admin\Kpi\KpiTeamReportController::class, 'getResultBreakdown'])->name('get.report.breakdown');
    Route::get('team-agent-report', [App\Http\Controllers\Admin\Kpi\KpiTeamReportController::class, 'getAgentReport'])->name('get.team.agent.report');
    Route::get('get-reports', [App\Http\Controllers\Admin\Kpi\KpiTeamController::class, 'getReports'])->name('get-report');
    Route::get('get-group-targets', [App\Http\Controllers\Admin\Kpi\KpiTeamController::class, 'getIndividualTarget'])->name('get.group.targets');
    Route::POST('save-group-target', [App\Http\Controllers\Admin\Kpi\KpiTeamController::class, 'saveGroupTarget'])->name('save-group-target');
    Route::get('get-team-data-kpi', [App\Http\Controllers\Admin\Kpi\KpiTeamReportController::class, 'getTeamData'])->name('get.team.kpi.data');
    Route::get('get-teams-overview-data', [App\Http\Controllers\Admin\Kpi\KpiTeamReportController::class, 'getTeamsOverviewData'])->name('get.teams.overview');
});

Route::group(["prefix" => "reports/kpi-v3", "as" => "reports.kpi-v3.", "middleware" => ["web", "auth", "checkUserStatus","2fa","tOuth"]], function () {
    Route::get('/', [App\Http\Controllers\Admin\KpiV3\KpiV3DashboardController::class, 'index'])->name('dashboard');
    Route::get('kpi-overview', [App\Http\Controllers\Admin\KpiV3\KpiV3DashboardController::class, 'kpiOverview'])->name('overview');
    Route::get('kpi-team-summary', [App\Http\Controllers\Admin\KpiV3\KpiV3DashboardController::class, 'teamSummary'])->name('team.summary');
    Route::get('team-member-overview', [App\Http\Controllers\Admin\KpiV3\KpiV3DashboardController::class, 'teamMemberOverview'])->name('team.member.overview');
    Route::get('team-member-reports', [App\Http\Controllers\Admin\KpiV3\KpiV3DashboardController::class, 'teamAgentReports'])->name('team.agent.reports');
    Route::post('team-target-update', [App\Http\Controllers\Admin\KpiV3\KpiV3DashboardController::class, 'saveGroupTarget'])->name('team.target.update');
    Route::post('agent-target-update', [App\Http\Controllers\Admin\KpiV3\KpiV3DashboardController::class, 'saveAgentTarget'])->name('agent.target.update');

});

Route::group(["prefix" => "bulk/sms", "as" => "contacts.bulk.sms.", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
    Route::get('/', [App\Http\Controllers\Admin\Contact\BulkSMSController::class, 'index'])->name('index');
    Route::get('/get', [App\Http\Controllers\Admin\Contact\BulkSMSController::class, 'view'])->name('view');
    Route::POST('datatable', [App\Http\Controllers\Admin\Contact\BulkSMSController::class, 'datatable'])->name('datatable');
    Route::POST('save', [App\Http\Controllers\Admin\Contact\BulkSMSController::class, 'save'])->name('save');
    Route::GET('/get-contacts', [App\Http\Controllers\Admin\Contact\BulkSMSController::class, 'getContacts'])->name('get.contacts');
    Route::post('send-msg-to-filtered-contacts', [App\Http\Controllers\Admin\Contact\BulkSMSController::class, 'sendMsg'])->name('sendMsg');
    Route::post('send-msg-to-filtered-listingleads', [App\Http\Controllers\Admin\Contact\BulkSMSController::class, 'sendMsgListingLead'])->name('listingleads.sendMsg');
    Route::post('send-msg-to-filtered-leads', [App\Http\Controllers\Admin\Contact\BulkSMSController::class, 'sendMsgLead'])->name('leads.sendMsg');
    Route::post('import-excel', [App\Http\Controllers\Admin\Contact\BulkSMSController::class, 'excelImport'])->name('excelImport');

});

Route::group(["prefix" => "sms/campaigns", "as" => "contacts.sms.campaigns.", "middleware" => ["web", "auth", "checkUserStatus"]], function () {

    Route::get('/', [App\Http\Controllers\Admin\Campaign\CampaignController::class, 'index'])->name('index');
    Route::POST('edit', [App\Http\Controllers\Admin\Campaign\CampaignController::class, 'edit'])->name('edit');
    Route::POST('update', [App\Http\Controllers\Admin\Campaign\CampaignController::class, 'update'])->name('update');
    Route::POST('datatable', [App\Http\Controllers\Admin\Campaign\CampaignController::class, 'datatable'])->name('datatable');

});

Route::group(["prefix" => "reports/v5", "as" => "reports.group.v5.", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
    Route::get('groups', [App\Http\Controllers\Admin\Report\GroupReportV5Controller::class, 'index'])->name('index');
    Route::POST('get-view', [App\Http\Controllers\Admin\Report\GroupReportV5Controller::class, 'getView'])->name('getView');
});

Route::group(["prefix" => "reports/agent-insights", "as" => "reports.agent-insights.", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
    Route::get('', [App\Http\Controllers\Admin\Report\AgentInsightsReportController::class, 'index'])->name('index');
    Route::POST('get-view', [App\Http\Controllers\Admin\Report\AgentInsightsReportController::class, 'getView'])->name('getView');
    Route::get('update-users-zone', [App\Http\Controllers\Admin\Report\AgentInsightsReportController::class, 'updateUsersZone'])->name('updateUsersZone');
});

Route::group(["prefix" => "reports/lead-insights", "as" => "reports.lead-insights.", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
    Route::get('', [App\Http\Controllers\Admin\Report\LeadInsightsReportController::class, 'index'])->name('index');
    Route::POST('get-view', [App\Http\Controllers\Admin\Report\LeadInsightsReportController::class, 'getView'])->name('getView');
});

Route::group(["prefix" => "reports/listing-insights", "as" => "reports.listing-insights.", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
    Route::get('', [App\Http\Controllers\Admin\Report\ListingInsightsReportController::class, 'index'])->name('index');
});

Route::group(["prefix" => "security", "as" => "admin.security.", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
    Route::get('dashboard', [App\Http\Controllers\Admin\Security\SecurityDashboardController::class, 'dashboard'])->name('index');

    Route::get('telescope/overview', [App\Http\Controllers\Admin\Security\SecurityTelescopeController::class, 'overview'])->name('telescope.overview');
    Route::get('request/overview', [App\Http\Controllers\Admin\Security\SecurityRequestLogController::class, 'overview'])->name('request.log.overview');

    Route::get('request-log', [App\Http\Controllers\Admin\Security\SecurityDashboardController::class, 'requestLog'])->name('request.log');
    Route::post('request-log', [App\Http\Controllers\Admin\Security\SecurityRequestLogController::class, 'dataTable'])->name('request.log.datatable');
    Route::get('request-log-details', [App\Http\Controllers\Admin\Security\SecurityRequestLogController::class, 'getDetails'])->name('request.log.details');

});

Route::group(["prefix" => "google-drive", "as" => "admin.google.", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
    Route::post('fetch-drive-images', [App\Http\Controllers\Admin\Google\GoogleDriveController::class, 'fetechImages'])->name('fetch.images');
    Route::post('attach-images', [App\Http\Controllers\Admin\Google\GoogleDriveController::class, 'attachImage'])->name('attach.images');
});

Route::group(["prefix" => "documents", "as" => "admin.documents."], function () {

    Route::get('/', [App\Http\Controllers\Admin\Documents\DocumentsController::class, 'index'])->name('index');
    Route::get('{any}', [App\Http\Controllers\Admin\Documents\DocumentsController::class, 'index'])->name('view');

    Route::get('type', [App\Http\Controllers\Admin\Documents\DocumentLibraryTypeController::class, 'index'])->name('type');
    Route::post('type/store', [App\Http\Controllers\Admin\Documents\DocumentLibraryTypeController::class, 'store'])->name('type.store');
    Route::post('type/dt', [App\Http\Controllers\Admin\Documents\DocumentLibraryTypeController::class, 'datatable'])->name('type.dt');
    Route::post('type/status', [App\Http\Controllers\Admin\Documents\DocumentLibraryTypeController::class, 'updateStatus'])->name('type.status');
    Route::post('type/update', [App\Http\Controllers\Admin\Documents\DocumentLibraryTypeController::class, 'update'])->name('type.update');
    Route::get('type/get', [App\Http\Controllers\Admin\Documents\DocumentLibraryTypeController::class, 'get'])->name('type.get');
    Route::post('type/delete', [App\Http\Controllers\Admin\Documents\DocumentLibraryTypeController::class, 'delete'])->name('type.delete');

    //  Route::post('type/delete-multiple', [App\Http\Controllers\Admin\Documents\DocumentLibraryTypeController::class, 'deleteMultiple'])->name('type.multi.delete');

});
Route::group(["prefix" => "quiz", "as" => "admin.quiz.", "middleware" => ["web","auth", "checkUserStatus","2fa","tOuth"]], function () {
    Route::get('/', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'index'])->name('index');
    Route::get('dashboard', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'dashboard'])->name('dashboard');
    Route::get('list', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'list'])->name('list');
    Route::post('store', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'store'])->name('store');
    Route::post('update', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'update'])->name('update');
    Route::post('dt', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'datatableRes'])->name('datatable');
    Route::post('update/status', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'updateStatus'])->name('update.status');
    Route::post('duplicate-quiz', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'duplicate'])->name('duplicate');
    Route::post('quiz/delete', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'delete'])->name('delete');

    Route::get('view/{id}', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'view'])->name('view');
    Route::get('report/{id}', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'reportView'])->name('report.view');
    Route::get('rules/{id}', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'rules'])->name('rules');
    Route::post('start', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'start'])->name('start');
    Route::get('get', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'get'])->name('get');
    Route::get('get/trial/quiz', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'getUnusedTrialQuiz'])->name('get.unused.trial.quiz');
    Route::get('feedback', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'feedback'])->name('feedback');
    Route::post('feedback/store', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'feedbackStore'])->name('feedback.store');


    Route::get('level', [App\Http\Controllers\Admin\Quiz\QuizLevelController::class, 'index'])->name('level.index');
    Route::post('level/store', [App\Http\Controllers\Admin\Quiz\QuizLevelController::class, 'store'])->name('level.store');
    Route::post('level/dt', [App\Http\Controllers\Admin\Quiz\QuizLevelController::class, 'statusForDatatable'])->name('level.dt');
    Route::get('level/get', [App\Http\Controllers\Admin\Quiz\QuizLevelController::class, 'get'])->name('level.get');
    Route::post('level/update', [App\Http\Controllers\Admin\Quiz\QuizLevelController::class, 'update'])->name('level.update');
    Route::post('level/delete', [App\Http\Controllers\Admin\Quiz\QuizLevelController::class, 'delete'])->name('level.delete');
    Route::post('level/multi-delete', [App\Http\Controllers\Admin\Quiz\QuizLevelController::class, 'deleteMultiple'])->name('level.multi.delete');
    Route::post('level/update/status', [App\Http\Controllers\Admin\Quiz\QuizLevelController::class, 'updateStatus'])->name('level.status.update');



    Route::post('question/store', [App\Http\Controllers\Admin\Quiz\QuizQuestionController::class, 'store'])->name('question.store');
    Route::post('question/update', [App\Http\Controllers\Admin\Quiz\QuizQuestionController::class, 'update'])->name('question.update');
    Route::post('question/delete', [App\Http\Controllers\Admin\Quiz\QuizQuestionController::class, 'delete'])->name('question.delete');
    Route::get('question/get', [App\Http\Controllers\Admin\Quiz\QuizQuestionController::class, 'getQuestion'])->name('question.get');
    Route::post('question/option/delete', [App\Http\Controllers\Admin\Quiz\QuizQuestionController::class, 'deleteQuestionOption'])->name('question.option.delete');


    Route::get('quiz/report/data1', [App\Http\Controllers\Admin\Quiz\QuizReportController::class, 'getQuizData1'])->name('report.data1');
    Route::get('quiz/report/data2', [App\Http\Controllers\Admin\Quiz\QuizReportController::class, 'getQuizData2'])->name('report.data2');
    Route::get('quiz/report/data3', [App\Http\Controllers\Admin\Quiz\QuizReportController::class, 'getQuizData3'])->name('report.data3');
    Route::post('quiz/report/user-history-dt', [App\Http\Controllers\Admin\Quiz\QuizReportController::class, 'userQuizHisotry'])->name('report.user.attempts');
    Route::get('quiz/report/last30-days', [App\Http\Controllers\Admin\Quiz\QuizReportController::class, 'last30DaysData'])->name('report.last30.days');

    ///quiz view page urls
    Route::post('quiz/user/list', [App\Http\Controllers\Admin\Quiz\QuizAttemptController::class, 'datatable'])->name('user.list');
    Route::post('quiz/user/history', [App\Http\Controllers\Admin\Quiz\QuizAttemptController::class, 'userQuizHisotry'])->name('user.attempt.list');
    Route::get('quiz/attempt/view/{id}', [App\Http\Controllers\Admin\Quiz\QuizAttemptController::class, 'view'])->name('user.attempt.view');
    Route::get('youtube/iframe', [App\Http\Controllers\Admin\Quiz\QuizAttemptController::class, 'youtubeIframe'])->name('youtube.iframe');



    //urls used in quiz vuejs module
    Route::get('get/target/quiz', [App\Http\Controllers\Admin\Quiz\QuizController::class, 'getMainQuiz'])->name('get.target.quiz');
    Route::get('question/all', [App\Http\Controllers\Admin\Quiz\QuizQuestionController::class, 'getAllQuestions'])->name('question.get.all');
    Route::post('answer/save', [App\Http\Controllers\Admin\Quiz\QuizQuestionController::class, 'saveAnswer'])->name('answer.save');
    Route::post('attempt/data', [App\Http\Controllers\Admin\Quiz\QuizAttemptController::class, 'getRunningAttemptData'])->name('attempt.data');
});
Route::group(['prefix'=>'api/documents','as'=>"admin.api.documents."], function () {
    Route::post('update-file', [\App\Http\Controllers\Api\V1\Folders\FolderApiController::class,'updateFile'])->name('update.file');
    Route::get('get-my-file-list', [\App\Http\Controllers\Api\V1\Folders\FolderApiController::class,'getMyFilesList'])->name('my.files.list');

});

Route::group(["prefix" => "listing", "as" => "admin.listing.image", "middleware" => ["web", "checkUserStatus"]], function () {
    Route::get('images', [App\Http\Controllers\Admin\Listing\ListingImageController::class, 'index'])->name('index');
    Route::get('image-data', [App\Http\Controllers\Admin\Listing\ListingImageController::class, 'data'])->name('data');
});
Route::group(["prefix" => "pipeline", "as" => "admin.pipeline.", "middleware" => ["web", "checkUserStatus"]], function () {
    Route::get('/', [PipelineController::class, 'index'])->name('index');
    Route::get('/list', [PipelineController::class, 'list'])->name('list');
    Route::get('/agents', [PipelineController::class, 'getAgents'])->name('get.agents');
    Route::post('/save', [PipelineController::class, 'save'])->name('save');
    Route::post('/delete/{id}', [PipelineController::class, 'delete'])->name('delete');

    Route::get('report/landing', [PipelineDashboardController::class, 'landing'])->name('landing');
});



Route::get("test-slug", [FolderApiController::class, "createFolder"]);

Route::group(["prefix" => "reports/admin-dashboard", "as" => "reports.admin-dashboard.", "middleware" => ["web", "auth", "checkUserStatus","2fa","tOuth"]], function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardV3\AdminDashboardController::class, 'index'])->name('index');
});

// This function should be out of auth

Route::get('test-pf', [\App\Http\Controllers\TestPFController::class, 'index']);

Route::get('s/{slug}', [App\Http\Controllers\ShortlinkController::class, 'smsShortLink'])->name('public.smsShortlink'); // if no custom link then generate a new one
Route::get('sms/{slug}', [App\Http\Controllers\ShortlinkController::class, 'smslinkClicked'])->name('public.smslinkClicked'); // open the link/shortlink

Route::get('ax/{slug}', [App\Http\Controllers\ShortlinkController::class, 'linkOpener'])->name('public.linkOpener');
Route::get('bitrix/{slug}', [App\Http\Controllers\ShortlinkController::class, 'bitrixLogin'])->name('public.bitrixLogin');
Route::get('contacts/{slug}', [App\Http\Controllers\ShortlinkController::class, 'ContactsLogin'])->name('public.ContactsLogin');
// Route::get('axchats/{slug}', [App\Http\Controllers\ShortlinkController::class, 'ContactsLogin'])->name('public.ContactsLogin');

Route::get('deals-revamped/approval/{uuid}', [App\Http\Controllers\Admin\Deals\Revamped\DealController::class, 'showApprovalPage'])->name('public.deals.aaproval');
Route::get('{any}', [App\Http\Controllers\TemplateController::class, 'index'])->name('index');
