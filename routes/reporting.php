<?php

use App\Http\Controllers\Api\V1\Folders\FolderApiController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Route::prefix('reporting/scripts/google')->group(function () {
    Route::get('lead', [App\Http\Controllers\Api\GoogleReporting\GoogleReportingController::class, 'lead']);
    Route::get('listing-lead', [App\Http\Controllers\Api\GoogleReporting\GoogleReportingController::class, 'listingLeads']);

    Route::get('leads-by-status', [App\Http\Controllers\Api\GoogleReporting\GoogleReportingController::class, 'leadsByStatus']);
    Route::get('listing-leads-by-status', [App\Http\Controllers\Api\GoogleReporting\GoogleReportingController::class, 'listingLeadsByStatus']);
    Route::get('verify-listing-lead', [App\Http\Controllers\Api\GoogleReporting\GoogleReportingController::class, 'CheckIFExist']);
    Route::get('google-listings-report/', [App\Http\Controllers\Api\GoogleReporting\GoogleReportingController::class, 'propertyReport']);

});

Route::prefix('reporting/commands/requests')->group(function () {
    Route::get('all', [App\Http\Controllers\Admin\Report\ReportRequestController::class, 'index'])->name('report.requests.index');
    Route::post('send', [App\Http\Controllers\Admin\Report\ReportRequestController::class, 'send'])->name('report.requests.send');
});

Route::group(["prefix" => "reporting/dashboard", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
    Route::get('initial-request', [App\Http\Controllers\Admin\DashboardV3\DashboardReportController::class, 'initialReqPoint']);
    Route::get('lead-insight', [App\Http\Controllers\Admin\DashboardV3\DashboardReportController::class, 'leadInsight']);
    Route::get('refresh', [App\Http\Controllers\Admin\DashboardV3\DashboardReportController::class, 'runCommand']);
    Route::get('get-departments', [App\Http\Controllers\Admin\DashboardV3\DashboardReportController::class, 'getDepartmentList']);

});
Route::group(["prefix" => "reporting/agent/dashboard", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
    Route::get('initial-request', [App\Http\Controllers\Admin\DashboardV3\AgentDashboardController::class, 'landingReq']);

});

Route::group(["prefix" => "reporting/admin/dashboard", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
    Route::get('listings-data', [App\Http\Controllers\Admin\DashboardV3\AdminDashboardController::class, 'listingsData']);
});

Route::group(["prefix" => "reporting/admin/listing-verifications", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
    Route::get('list', [App\Http\Controllers\Admin\Listing\ListingVerificationController::class, 'list']);
    Route::get('user-and-status-wise-count', [App\Http\Controllers\Admin\Listing\ListingVerificationController::class, 'getUserAndStatusWiseCount']);
    Route::post('submit-for-verification', [App\Http\Controllers\Admin\Listing\ListingVerificationController::class, 'submitForVerification']);
    Route::post('re-submit-for-verification', [App\Http\Controllers\Admin\Listing\ListingVerificationController::class, 'reSubmitForVerification']);
    Route::get('get-properties-for-verification', [App\Http\Controllers\Admin\Listing\ListingVerificationController::class, 'getPropertiesForVerification']);

});
Route::group(["prefix" => "admin/listing/portal-log", "middleware" => ["web", "auth", "checkUserStatus"]], function () {
    Route::get('listing-portal-timeline', [App\Http\Controllers\Admin\Listing\ListingPortalSyncLogController::class, 'index'])->name('listing.portal.timeline.index');
    Route::post('listing-portal-timeline-dt', [App\Http\Controllers\Admin\Listing\ListingPortalSyncLogController::class, 'dataTable'])->name('listing.portal.timeline.dt');
    Route::get('listing-portal-timeline-details', [App\Http\Controllers\Admin\Listing\ListingPortalSyncLogController::class, 'getDetails'])->name('listing.portal.timeline.getDetails');
});
