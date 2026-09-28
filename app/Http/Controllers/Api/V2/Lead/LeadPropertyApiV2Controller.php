<?php

namespace App\Http\Controllers\Api\V2\Lead;

use App\Http\Controllers\Admin\Lead\LeadController;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\V1\Lead\GetLeadListRequest;
use App\Http\Resources\Lead\LeadPropertyResource;
use App\Http\Resources\LeadAxResource;
use App\Http\Resources\LeadNoteResource;
use App\Http\Resources\LeadResource;
use App\Http\Tasks\Api\V1\Lead\LeadListTaskV2;
use App\Models\CallLog;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Lead;
use App\Models\LeadCriteria;
use App\Models\LeadNote;
use App\Models\LeadStatus;
use App\Models\ListingLead;
use App\Models\ListingLeadNote;
use App\Models\Medium;
use App\Models\Property;
use App\Models\Source;
use App\Models\SubCommunity;
use App\Models\Type;
use App\Models\User;
use App\Traits\CallTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Validator;

class LeadPropertyApiV2Controller extends BaseController
{
    use CallTrait;

    /**
     *
     *
     *
     * Get Agent Properties
     * @group Mobile Leads
     * @authenticated
     * @header Accept application/json
    Example: abc
     *
     *
     *
     * @header Accept application/json
     * @bodyParam agent_id int agent id for getting his properties. Example: 172

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": 200,
    "code": 200,
    "message": "Data found",
    "data": {
    "id": 13683,
    "my_crm_id": null,
    "ref_no": "axc-3972035",
    "title": "Huge Plot | Corner Unit | Single Row",
    "description": "Ax Capital Real Estate is delighted to present this beautiful 5 bedrooms Villa in Nad Al Sheba Gardens Phase 2 for Sale situated in the popular development area of Maydan.\r\n\r\n-Plot size: 8110.13sqft\r\n-Bulit up: 6487.35sqft\r\n-Bedrooms: 5\r\n-Completion: Q2 of 2025\r\n\r\nVilla Specifications:\r\n* Maid's Room.\r\n* Laundry Room.\r\n* Powder Room\r\n* In-Home Elevator\r\n* Double Height Living Space\r\n* Walk in Closets\r\n* Laundry Room\r\n* Floor-to-Ceiling Windows\r\n* High-End Finishing\r\n* Private parking\r\n* Gated community\r\n\r\nKey features and highlights of Nad Al Sheba Gardens:\r\n\r\n* 05 Minutes to Meydan Racetrack\r\n* 10 Minutes to Downtown\r\n* 10 Minutes to Dubai Hills Mall\r\n* 10 Minutes proximity to Major Schools in Dubai\r\n* 03 Minutes to Al Ain Road\r\n* 15 Minutes to Dubai International Airport\r\n\r\nCommunity Amenities:\r\n* Central Park\r\n* Primary/Secondary Schools\r\n* Cycling Tracks\r\n* Organic markets\r\n* Community farms\r\n* Kids Play area\r\n* Swimming pools\r\n* Outdoor Gyms\r\n* Sports Courts\r\n\r\nWith its meandering lanes and open front lawns, the private gated community of Nad Al Sheba Gardens welcomes you to live in Dubai at your own pace. In a space carefully designed to flow seamlessly with your everyday life as you nurture your inner and outer growth. Offering numerous types of luxury townhouses and villas, with sizes ranging from 243.22 sqm (2,618 sqft) to 610.06 sqm (6,567 sqft)\r\n\r\nThe community is designed in an organic manner, with meandering streets and open front lawns providing a unique character. The amenities in community go above and beyond the ordinary, which include beekeeping areas, community farms, world- class primary and secondary school, cycling tracks, multipurpose sports areas, training fields, clubhouses to name but a few.\r\n\r\nAlternatively visit our website https://www.axcapital.ae/ where you will find an extensive selection of properties available both for sale and for rent.\r\nAsk us about:\r\n\r\n* Off-Plan\r\n* Property Management\r\n* Interior Design (Ax Design) \r\n* Exclusive Off-Plan \r\n* Short Term Let",
    "listing_source": null,
    "for": "Residential for sale",
    "region_id": 1,
    "community_id": 112,
    "sub_community_id": 1947,
    "community_property_id": 3369,
    "property_category_id": 2,
    "bed_id": 6,
    "bath_id": 7,
    "unit_no": "0000",
    "built_up_area": "6487.35",
    "plot_size": "8110.07",
    "street": null,
    "floor": null,
    "view": "[\"Community View\",\"Garden View\"]",
    "parking": 3,
    "dewa_number": null,
    "rera_permit_number": null,
    "rera_issue_date": null,
    "rera_expiry_date": null,
    "rera_validation_url": null,
    "property_type_id": null,
    "property_status_id": 12,
    "completion_status": "Off-Plan Secondary",
    "rented_at": null,
    "rented_until": null,
    "next_available": null,
    "availability_date": "2024-01-11",
    "remind": null,
    "furnished": "UNFURNISHED",
    "featured": 0,
    "exclusive": 0,
    "exclusive_start": null,
    "exclusive_end": null,
    "hot": 0,
    "publish_status": "SOLD",
    "admin_unpublish_reason": null,
    "agent_id": 172,
    "previous_agent_id": null,
    "external_agent_id": null,
    "telesales_id": null,
    "owner_id": 14931,
    "developer_id": 6,
    "cheques": null,
    "mycrm_updated": 0,
    "created_by_id": 238,
    "updated_by_id": 172,
    "video_link": null,
    "video_360_link": null,
    "qr_link": null,
    "title_deed_text": null,
    "data_version": 1,
    "is_archived": 0,
    "payment_method": null,
    "down_payment_price": null,
    "build_year": null,
    "created_at": "2024-01-11T12:17:37.000000Z",
    "updated_at": "2024-04-23T13:49:57.000000Z",
    "deleted_by_id": null,
    "deleted_at": null,
    "state": null,
    "is_live": 0,
    "request_status_type": null,
    "PASSWORD": null,
    "villa_type": "[\"SR\"]",
    "contract_start": null,
    "contract_end": null,
    "contract_number": null,
    "published_date": null,
    "unpublished_date": null,
    "unable_to_generate_qr": 0,
    "offplan_portal_fetch": 0,
    "republished_date": null,
    "first_image": {
    "id": 359211,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351412520240419120845JKJR20P80H.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351412520240419120845JKJR20P80H.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351412520240419120845JKJR20P80H.jpg",
    "password": null,
    "note": null,
    "order": 285490,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:08:45.000000Z",
    "updated_at": "2024-04-19T08:08:45.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    "category": "Villa",
    "location": "Nad Al Sheba Gardens - Phase 2, Nad Al Sheba 1, Nadd Al Sheba, Dubai",
    "my_property": true,
    "bed": {
    "id": 6,
    "name": "5 beds",
    "status": 1,
    "order": 5,
    "created_by_id": null,
    "updated_by_id": 1,
    "deleted_at": null,
    "created_at": "2023-02-14T03:30:31.000000Z",
    "updated_at": "2023-05-31T11:49:25.000000Z"
    },
    "property_category": {
    "id": 2,
    "title": "Villa",
    "slug": "villa",
    "thumbnail": null,
    "image": null,
    "color_code": null,
    "status": 1,
    "order": 0,
    "badge": null,
    "icon": null,
    "created_by_id": null,
    "updated_by_id": 263,
    "created_at": "2023-02-14T03:30:33.000000Z",
    "updated_at": "2023-06-26T14:22:02.000000Z",
    "deleted_at": null
    },
    "community": {
    "id": 112,
    "region_id": 1,
    "name": "Nadd Al Sheba",
    "alternate_name": null,
    "slug": "nadd-al-sheba",
    "description": null,
    "path_name": "Nadd Al Sheba, Dubai",
    "alternate_path_name": null,
    "latitude": "25.1435458",
    "longitude": "55.3475331",
    "created_by_id": 1,
    "updated_by_id": 1,
    "status": 1,
    "created_at": "2020-04-29T19:54:06.000000Z",
    "updated_at": "2020-04-29T19:54:06.000000Z",
    "deleted_at": null
    },
    "sub_community": {
    "id": 1947,
    "region_id": 1,
    "community_id": 112,
    "name": "Nad Al Sheba 1",
    "alternate_name": null,
    "slug": "nad-al-sheba-1",
    "description": null,
    "path_name": "Nad Al Sheba 1, Nadd Al Sheba, Dubai",
    "alternate_path_name": null,
    "latitude": "25.1591193",
    "longitude": "55.3088196",
    "created_by_id": 1,
    "updated_by_id": 1,
    "status": 1,
    "created_at": "2020-04-29T19:54:06.000000Z",
    "updated_at": "2020-04-29T19:54:06.000000Z",
    "deleted_at": null
    },
    "portals": [],
    "price": {
    "id": 12766,
    "property_id": 13683,
    "price": "17000000",
    "prices": null,
    "price_on_application": 0,
    "created_at": "2024-01-11T12:17:37.000000Z",
    "updated_at": "2024-01-11T12:17:37.000000Z"
    },
    "agent": {
    "id": 172,
    "bitrix_role": null,
    "entity": null,
    "group_id": null,
    "parent_id": 11,
    "public_profile_id": null,
    "old_user_id": null,
    "slug": "muhammad-saim-aziz",
    "title": null,
    "name": "Muhammad Saim Aziz",
    "first_name": "Muhammad Saim",
    "last_name": "Aziz",
    "position": "Dev",
    "email": "smirza277@gmail.com",
    "secondary_email": "smirza277@gmail.com",
    "email_verified_at": null,
    "last_login": "2024-06-04 11:32:44",
    "mobile": "+971588159645",
    "api_access": 0,
    "api_rate_limit": 0,
    "telegram_username": null,
    "telegram_chatid": "5970238984",
    "call_tracking_user_id": null,
    "call_tracking_account_id": null,
    "bitrix_id": null,
    "website_link": null,
    "facebook_link": null,
    "instagram_link": null,
    "youtube_link": null,
    "linkedin_link": null,
    "bio": null,
    "license_no": null,
    "pp_enabled": null,
    "pp_name": null,
    "pp_photo": null,
    "pp_license_no": null,
    "country_id": null,
    "state_id": null,
    "nationality_id": null,
    "community_id": null,
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_168457219720230520124317.jpg",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/168457219720230520124317.jpg",
    "avatar": null,
    "color_code": null,
    "status": 1,
    "region_id": 1,
    "traccar_status": 1,
    "device_identifier": "172",
    "port": "5055",
    "mic_status": 1,
    "device_token": "eeHUT4cDSpCXSLuXS9Vfnb:APA91bG25Q-F8MrTCeb7bP9wwCyUDQERFVChZ2rNZhjAm-OurL58LN8AcCfrI_1DeAaVAZ8J9wJccnEtQ3bfC9NMW7UnxohuQZzCixslZjwN3U5yt9uEAMgqsuTLCCPMzbE2fK7BUNLl",
    "call_tracking": 1,
    "call_lead": 0,
    "is_commercial": 0,
    "created_by_id": 1,
    "updated_by_id": 172,
    "created_at": "2023-02-15T06:42:31.000000Z",
    "updated_at": "2024-06-04T07:32:44.000000Z",
    "deleted_at": null,
    "google2fa_secret": "H6AUE7QXV7OAHEGK",
    "telegram2fa_secret": null,
    "back_up_google2fa_secret": null,
    "login_attempt": 0,
    "joining_date": "2019-02-28 11:39:59",
    "last_deal_date": null,
    "is_report_exempt": 0,
    "full_name": "Muhammad Saim Aziz",
    "thumb_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_168457219720230520124317.jpg",
    "image_url": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/168457219720230520124317.jpg",
    "role_list": [
    "Agent"
    ]
    },
    "photos": [
    {
    "id": 359211,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351412520240419120845JKJR20P80H.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351412520240419120845JKJR20P80H.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351412520240419120845JKJR20P80H.jpg",
    "password": null,
    "note": null,
    "order": 285490,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:08:45.000000Z",
    "updated_at": "2024-04-19T08:08:45.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359212,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351412620240419120846hfGbncLEiQ.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351412620240419120846hfGbncLEiQ.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351412620240419120846hfGbncLEiQ.jpg",
    "password": null,
    "note": null,
    "order": 285491,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:08:47.000000Z",
    "updated_at": "2024-04-19T08:08:47.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359213,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351417220240419120932Y9XZtqil2T.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351417220240419120932Y9XZtqil2T.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351417220240419120932Y9XZtqil2T.jpg",
    "password": null,
    "note": null,
    "order": 285492,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:09:33.000000Z",
    "updated_at": "2024-04-19T08:09:33.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359214,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351417320240419120933NqEWadilMJ.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351417320240419120933NqEWadilMJ.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351417320240419120933NqEWadilMJ.jpg",
    "password": null,
    "note": null,
    "order": 285493,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:09:36.000000Z",
    "updated_at": "2024-04-19T08:09:36.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359215,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351417620240419120936XCJ9BlJ9jk.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351417620240419120936XCJ9BlJ9jk.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351417620240419120936XCJ9BlJ9jk.jpg",
    "password": null,
    "note": null,
    "order": 285494,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:09:37.000000Z",
    "updated_at": "2024-04-19T08:09:37.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359216,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351422020240419121020MocBp5sKxx.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351422020240419121020MocBp5sKxx.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351422020240419121020MocBp5sKxx.jpg",
    "password": null,
    "note": null,
    "order": 285494,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:10:21.000000Z",
    "updated_at": "2024-04-19T08:10:21.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359217,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351422120240419121021IO6QpZgbl6.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351422120240419121021IO6QpZgbl6.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351422120240419121021IO6QpZgbl6.jpg",
    "password": null,
    "note": null,
    "order": 285495,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:10:22.000000Z",
    "updated_at": "2024-04-19T08:10:22.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359218,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/17135142222024041912102262Snre6oCi.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_17135142222024041912102262Snre6oCi.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_17135142222024041912102262Snre6oCi.jpg",
    "password": null,
    "note": null,
    "order": 285496,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:10:23.000000Z",
    "updated_at": "2024-04-19T08:10:23.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359219,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351422320240419121023fHrjIkbciN.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351422320240419121023fHrjIkbciN.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351422320240419121023fHrjIkbciN.jpg",
    "password": null,
    "note": null,
    "order": 285497,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:10:23.000000Z",
    "updated_at": "2024-04-19T08:10:23.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359220,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351422420240419121024TlGsOBIuTt.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351422420240419121024TlGsOBIuTt.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351422420240419121024TlGsOBIuTt.jpg",
    "password": null,
    "note": null,
    "order": 285498,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:10:26.000000Z",
    "updated_at": "2024-04-19T08:10:26.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 359221,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171351422620240419121026yoU6SnJsQp.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171351422620240419121026yoU6SnJsQp.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171351422620240419121026yoU6SnJsQp.jpg",
    "password": null,
    "note": null,
    "order": 285499,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 1,
    "updated_by_id": null,
    "created_at": "2024-04-19T08:10:26.000000Z",
    "updated_at": "2024-04-19T08:10:26.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 362035,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/171377761820240422132018l0bLGYihn0.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_171377761820240422132018l0bLGYihn0.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_171377761820240422132018l0bLGYihn0.jpg",
    "password": null,
    "note": null,
    "order": 287718,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 262,
    "updated_by_id": null,
    "created_at": "2024-04-22T09:20:20.000000Z",
    "updated_at": "2024-04-22T09:20:20.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    },
    {
    "id": 362036,
    "property_id": 13683,
    "title": null,
    "description": null,
    "path": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/1713777620202404221320204VLIZs1Jjm.jpg",
    "original": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/original_1713777620202404221320204VLIZs1Jjm.jpg",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb_1713777620202404221320204VLIZs1Jjm.jpg",
    "password": null,
    "note": null,
    "order": 287719,
    "type": "Photos",
    "temp_image": 0,
    "not_uploaded_on_s3": 0,
    "status": 1,
    "media_changed": null,
    "temp_id": null,
    "created_by_id": 262,
    "updated_by_id": null,
    "created_at": "2024-04-22T09:20:21.000000Z",
    "updated_at": "2024-04-22T09:20:21.000000Z",
    "deleted_at": null,
    "deleted_by_id": null
    }
    ],
    "community_property": {
    "id": 3369,
    "region_id": 1,
    "community_id": 112,
    "sub_community_id": 1947,
    "name": "Nad Al Sheba Gardens - Phase 2",
    "alternate_name": null,
    "slug": "nad-al-sheba-gardens-phase-2",
    "description": null,
    "path_name": "Nad Al Sheba Gardens - Phase 2, Nad Al Sheba 1, Nadd Al Sheba, Dubai",
    "alternate_path_name": null,
    "latitude": "25.13498511382361",
    "longitude": "55.3082090153433",
    "created_by_id": 146,
    "updated_by_id": null,
    "default_use": 1,
    "alternate_use": 1,
    "status": 1,
    "created_at": "2023-11-22T07:38:35.000000Z",
    "updated_at": "2023-11-22T07:38:35.000000Z",
    "deleted_at": null
    }
    },
    "page": 1,
    "per_page": 10
    }
     */
    public function getProperties(Request $request)
    {


        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $query = Property::query();
        $data = $query->when($request->for, function ($q) {
            $this->isArray(\request("for")) ? $q->whereIn("for", \request("for")) : $q->where("for", \request("for"));
        })->where("agent_id", auth()->user()->id)
        ->when($request->keyword, function ($q) {
            $q->where(function ($q2) {
                $q2->where("ref_no", "LIKE", "%" . \request("keyword") . "%")
                    ->orWhere("title", "LIKE", "%" . \request("keyword") . "%")
                    ->orWhere("unit_no", "LIKE", "%" . \request("keyword") . "%")
                    ->orWhereHas('communityProperty', function ($q) {
                        $q->where(\DB::raw('lower(path_name)'), 'like', '%' . strtolower(\request("keyword")) . '%');
                    })->orWhereHas('subCommunity', function ($q) {
                        $q->where(\DB::raw('lower(path_name)'), 'like', '%' . strtolower(\request("keyword")) . '%');
                    })->orWhereHas('community', function ($q) {
                        $q->where(\DB::raw('lower(path_name)'), 'like', '%' . strtolower(\request("keyword")) . '%');
                    })->orWhereHas('price', function ($q) {
                        $q->where(\DB::raw('lower(price)'), 'like', '%' . strtolower(\request("keyword")) . '%');
                    });
            });

        })
            ->with(["bed", "community", "subCommunity","price"])->get();
        $count = $query->count();
        $properties = $query->paginate(20);

        if ($count > 0) {
            $properties->data = LeadPropertyResource::collection($properties);
            $properties->appends($request->query())->links();
        } else {
            return $this->sendResponse(false, 'No Leads found', [], 200,1,10, $request->all());
        }

        return $this->sendResponse(200, 'Data found', $properties, 200);
    }
}
