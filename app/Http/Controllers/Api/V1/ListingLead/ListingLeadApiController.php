<?php

namespace App\Http\Controllers\Api\V1\ListingLead;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Api\V1\ListingLead\GetListingLeadRequest;
use App\Http\Resources\ListingLeadNoteResource;
use App\Http\Resources\ListingLeadResource;
use App\Http\Tasks\Api\V1\ListingLead\ListingLeadTask;
use App\Http\Tasks\Api\V1\ListingLead\SearchListingLeadTask;
use App\Models\CallLog;
use App\Models\CommunityProperty;
use App\Models\Contact;
use App\Models\ContactAgent;
use App\Models\ContactNote;
use App\Models\ListingLead;
use App\Models\ListingLeadNote;
use App\Models\Property;
use App\Models\PropertyNote;
use App\Models\SubCommunity;
use App\Models\User;
use App\Traits\TelegramTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class ListingLeadApiController extends BaseController
{
    use TelegramTrait;

    /**
     *
     *
     * Listing Lead List
     *
     * Get a list of Listing leads. with pagination
     * @group Listing Leads
     * @authenticated
     * @header Accept application/json
     * @queryParam search string Search by first name, last name, email or mobile Example: John, Doe, johan@demo.com, 1234567890
     * @queryParam agent_id int Agent ID Example: 1
     * @queryParam status_id int Lead Status ID Example: 1
     *
     * @queryParam medium_id int Medium ID Example: 1
     * @queryParam source_id int Source ID Example: 1
     * @queryParam type_id int Type ID Example: 1
     * @queryParam is_archive boolean Is Archive Example: 1
     *
     * @queryParam bed_id int Minimum bed id Example: 1
     * @queryParam bath_id int Minimum bath id Example: 1
     * @queryParam communities int for search in communities Example: 1
     * @queryParam sub_communities int for search in sub communities Example: 1
     * @queryParam community_properties int for search in community properties Example: 1
     * @queryParam created_at string Created at Example: 2023-04-24
     * @queryParam updated_at string Updated at Example: 2023-04-24
     * @queryParam page int Page number Example: 1
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Lead list",
    "data": {
    "leads": {
    "current_page": 1,
    "data": [
    {
    "id": 19803,
    "first_name": "NITIN",
    "last_name": "JAGAT ADVANI",
    "email": null,
    "mobile": "+971528888634",
    "unit_no": "2601",
    "for": "Residential for rent",
    "bed_id": "2",
    "bed": {
    "id": 2,
    "name": "1 bed"
    },
    "bath_id": "1",
    "bath": {
    "id": 1,
    "name": "1 bath"
    },
    "status_id": 2,
    "status": {
    "id": 2,
    "name": "Assigned"
    },
    "source_id": 131,
    "source": {
    "id": 131,
    "name": "AX CONTACTS"
    },
    "community_id": 72,
    "sub_community_id": 799,
    "community_property_id": 993,
    "location_id": null,
    "location_type": null,
    "note": "Listing Lead Imported : AX CONTACTS \n\n\nLocation: Opera Grand,Downtown Dubai,Dubai,UAE\n\n\n  Webhook Data: POST /api/webhook/ax-contacts/save-listing-leads HTTP/1.1\r\nAccept:         application/json\r\nContent-Length: 560\r\nContent-Type:   application/json\r\nHost:           secondary.axcrm.ae\r\nUser-Agent:     GuzzleHttp/7\r\n\r\n{\"owner_id\":21117,\"property_id\":387692,\"owner_first_name\":\"NITIN\",\"owner_last_name\":\"JAGAT ADVANI\",\"owner_mobile\":\"+971528888634\",\"owner_email\":\"\",\"property_type\":\"Apartment\",\"property_purpose\":\"Residential for rent\",\"property_location\":\"Opera Grand,Downtown Dubai,Dubai,UAE\",\"property_bed\":\"1 bed\",\"property_bath\":\"1 bath\",\"property_unit_no\":\"2601\",\"agent_email\":\"l.setlhare@axcapital.ae\",\"call_recording\":\"https:\\/\\/api.twilio.com\\/2010-04-01\\/Accounts\\/AC75402c32b34d5e0c5dec206c3b865cf5\\/Recordings\\/REa1689d4dfb2c0a49cf4f0a1ab72e1e69.mp3\",\"source_id\":131}",
    "recording_file": "https://api.twilio.com/2010-04-01/Accounts/AC75402c32b34d5e0c5dec206c3b865cf5/Recordings/REa1689d4dfb2c0a49cf4f0a1ab72e1e69.mp3",
    "property_category_id": 1,
    "propertyCategory": {
    "id": 1,
    "title": "Apartment"
    },
    "agent_id": {
    "id": 123,
    "bitrix_role": "Secondary, Karim",
    "name": "Lindiwe Setlhare",
    "full_name": "Lindiwe Setlhare",
    "first_name": "Lindiwe",
    "last_name": "Setlhare",
    "position": "Agent",
    "email": "l.setlhare@axcapital.ae",
    "secondary_email": "l.setlhare@axcapital.ae",
    "password": "$2y$10$vbrZ2wjasbc4fUs3fVtG.e7RQxKL6me6a/Mm7egfee3leRRvs47j2",
    "roles": [
    {
    "id": 5,
    "name": "Agent",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-14T03:29:47.000000Z",
    "updated_at": "2023-02-14T03:29:47.000000Z",
    "pivot": {
    "model_id": 123,
    "role_id": 5,
    "model_type": "App\\Models\\User"
    }
    }
    ],
    "mobile": "+971521706526",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_171802118620240610160626.jpg",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/171802118620240610160626.jpg",
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "5970238984",
    "status": 1,
    "locations": [],
    "parent_id": 17,
    "bio": null,
    "is_report_exempt": 0,
    "is_manager": 0
    }
    },
    {
    "id": 19751,
    "first_name": "MOHAMMED",
    "last_name": "ANIS DADANI",
    "email": null,
    "mobile": "+971506313158",
    "unit_no": "AFSCQMOTHA0357",
    "for": "Residential for rent",
    "bed_id": "4",
    "bed": {
    "id": 4,
    "name": "3 beds"
    },
    "bath_id": "5",
    "bath": {
    "id": 5,
    "name": "5 baths"
    },
    "status_id": 5,
    "status": {
    "id": 5,
    "name": "Successful"
    },
    "source_id": 131,
    "source": {
    "id": 131,
    "name": "AX CONTACTS"
    },
    "community_id": 46,
    "sub_community_id": 331,
    "community_property_id": 370,
    "location_id": null,
    "location_type": null,
    "note": "Listing Lead Imported : AX CONTACTS \n\n\nLocation: Quortaj,Al Furjan,Dubai,UAE\n\n\n  Webhook Data: POST /api/webhook/ax-contacts/save-listing-leads HTTP/1.1\r\nAccept:         application/json\r\nContent-Length: 561\r\nContent-Type:   application/json\r\nHost:           secondary.axcrm.ae\r\nUser-Agent:     GuzzleHttp/7\r\n\r\n{\"owner_id\":48501,\"property_id\":126404,\"owner_first_name\":\"MOHAMMED\",\"owner_last_name\":\"ANIS DADANI\",\"owner_mobile\":\"+971506313158\",\"owner_email\":\"\",\"property_type\":\"Apartment\",\"property_purpose\":\"Residential for rent\",\"property_location\":\"Quortaj,Al Furjan,Dubai,UAE\",\"property_bed\":\"1 bed\",\"property_bath\":\"1 bath\",\"property_unit_no\":\"AFSCQMOTHA0357\",\"agent_email\":\"k.eliyas@axcapital.ae\",\"call_recording\":\"https:\\/\\/api.twilio.com\\/2010-04-01\\/Accounts\\/AC75402c32b34d5e0c5dec206c3b865cf5\\/Recordings\\/RE2871f18978100616e63e85fc0a689550.mp3\",\"source_id\":131}",
    "recording_file": "https://api.twilio.com/2010-04-01/Accounts/AC75402c32b34d5e0c5dec206c3b865cf5/Recordings/RE2871f18978100616e63e85fc0a689550.mp3",
    "property_category_id": 3,
    "propertyCategory": {
    "id": 3,
    "title": "Townhouse"
    },
    "agent_id": {
    "id": 1524,
    "bitrix_role": "Secondary, Natascha",
    "name": "Eliyas Khan",
    "full_name": "Eliyas Khan",
    "first_name": "Eliyas",
    "last_name": "Khan",
    "position": "Property Consultant",
    "email": "k.eliyas@axcapital.ae",
    "secondary_email": null,
    "password": "$2y$10$g9v2Ro2ClYRd5/O7d6rbguiB3Kfn9NOmZ.KEpJEIsVMetQwHnjlV6",
    "roles": [
    {
    "id": 5,
    "name": "Agent",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-14T03:29:47.000000Z",
    "updated_at": "2023-02-14T03:29:47.000000Z",
    "pivot": {
    "model_id": 1524,
    "role_id": 5,
    "model_type": "App\\Models\\User"
    }
    }
    ],
    "mobile": "+971528756540",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_172009104120240704150401.jpg",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/172009104120240704150401.jpg",
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "5970238984",
    "status": 1,
    "locations": [],
    "parent_id": 1310,
    "bio": null,
    "is_report_exempt": 0,
    "is_manager": 0
    }
    },
    {
    "id": 19727,
    "first_name": "JUNYI address",
    "last_name": "ZHANG",
    "email": null,
    "mobile": "+971549989253",
    "unit_no": "4103",
    "for": "Residential for rent",
    "bed_id": "2",
    "bed": {
    "id": 2,
    "name": "1 bed"
    },
    "bath_id": "1",
    "bath": {
    "id": 1,
    "name": "1 bath"
    },
    "status_id": 6,
    "status": {
    "id": 6,
    "name": "Unsuccessful"
    },
    "source_id": 131,
    "source": {
    "id": 131,
    "name": "AX CONTACTS"
    },
    "community_id": 80,
    "sub_community_id": 960,
    "community_property_id": null,
    "location_id": 960,
    "location_type": "sub_community",
    "note": "Listing Lead Imported : AX CONTACTS \r\n\r\n\r\nLocation: Address Harbour Point,Dubai Creek Harbour,Dubai,UAE\r\n\r\n\r\n  Webhook Data: POST /api/webhook/ax-contacts/save-listing-leads HTTP/1.1\r\nAccept:         application/json\r\nContent-Length: 568\r\nContent-Type:   application/json\r\nHost:           secondary.axcrm.ae\r\nUser-Agent:     GuzzleHttp/7\r\n\r\n{\"owner_id\":177204,\"property_id\":38178,\"owner_first_name\":\"JUNYI\",\"owner_last_name\":\"ZHANG\",\"owner_mobile\":\"+971549989253\",\"owner_email\":\"\",\"property_type\":\"Apartment\",\"property_purpose\":\"Residential for rent\",\"property_location\":\"Address Harbour Point,Dubai Creek Harbour,Dubai,UAE\",\"property_bed\":\"1 bed\",\"property_bath\":\"1 bath\",\"property_unit_no\":\"4103\",\"agent_email\":\"v.dubrovin@axcapital.ae\",\"call_recording\":\"https:\\/\\/api.twilio.com\\/2010-04-01\\/Accounts\\/AC75402c32b34d5e0c5dec206c3b865cf5\\/Recordings\\/RE7165e55027f6e0fb76a959c9af0e941c.mp3\",\"source_id\":131}",
    "recording_file": "https://api.twilio.com/2010-04-01/Accounts/AC75402c32b34d5e0c5dec206c3b865cf5/Recordings/RE7165e55027f6e0fb76a959c9af0e941c.mp3",
    "property_category_id": 1,
    "propertyCategory": {
    "id": 1,
    "title": "Apartment"
    },
    "agent_id": {
    "id": 1596,
    "bitrix_role": "Secondary, Azim",
    "name": "Vladislav Dubrovin",
    "full_name": "Vladislav Dubrovin",
    "first_name": "Vladislav",
    "last_name": "Dubrovin",
    "position": "Property Consultant",
    "email": "v.dubrovin@axcapital.ae",
    "secondary_email": null,
    "password": "$2y$10$7.VIGMwYISA4and26rpOn.GxGNQttRUXGyRZPzS6AdTtacQAvnViO",
    "roles": [
    {
    "id": 5,
    "name": "Agent",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-14T03:29:47.000000Z",
    "updated_at": "2023-02-14T03:29:47.000000Z",
    "pivot": {
    "model_id": 1596,
    "role_id": 5,
    "model_type": "App\\Models\\User"
    }
    }
    ],
    "mobile": "+971586981540",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_1719900043yg5o0vgrn920240702100043.jpg",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/1719900043yg5o0vgrn920240702100043.jpg",
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "5970238984",
    "status": 1,
    "locations": [],
    "parent_id": 129,
    "bio": null,
    "is_report_exempt": 0,
    "is_manager": 0
    }
    },
    {
    "id": 19802,
    "first_name": "ABDUL",
    "last_name": "MUTEEN SAEED HAFIZ MUHAMMAD",
    "email": null,
    "mobile": "+971552691281",
    "unit_no": "A1813",
    "for": "Residential for rent",
    "bed_id": "2",
    "bed": {
    "id": 2,
    "name": "1 bed"
    },
    "bath_id": "1",
    "bath": {
    "id": 1,
    "name": "1 bath"
    },
    "status_id": 4,
    "status": {
    "id": 4,
    "name": "Qualified"
    },
    "source_id": 131,
    "source": {
    "id": 131,
    "name": "AX CONTACTS"
    },
    "community_id": 153,
    "sub_community_id": 30094,
    "community_property_id": null,
    "location_id": 30094,
    "location_type": "sub_community",
    "note": "Listing Lead Imported : AX CONTACTS \r\n\r\n\r\nLocation: Carson Tower A,Carson - The Drive,DAMAC Hills,Dubai,UAE\r\n\r\n\r\n  Webhook Data: POST /api/webhook/ax-contacts/save-listing-leads HTTP/1.1\r\nAccept:         application/json\r\nContent-Length: 595\r\nContent-Type:   application/json\r\nHost:           secondary.axcrm.ae\r\nUser-Agent:     GuzzleHttp/7\r\n\r\n{\"owner_id\":39657,\"property_id\":44122,\"owner_first_name\":\"ABDUL\",\"owner_last_name\":\"MUTEEN SAEED HAFIZ MUHAMMAD\",\"owner_mobile\":\"+971552691281\",\"owner_email\":\"\",\"property_type\":\"Apartment\",\"property_purpose\":\"Residential for rent\",\"property_location\":\"Carson Tower A,Carson - The Drive,DAMAC Hills,Dubai,UAE\",\"property_bed\":\"1 bed\",\"property_bath\":\"1 bath\",\"property_unit_no\":\"A1813\",\"agent_email\":\"i.diddeniya@axcapital.ae\",\"call_recording\":\"https:\\/\\/api.twilio.com\\/2010-04-01\\/Accounts\\/AC75402c32b34d5e0c5dec206c3b865cf5\\/Recordings\\/REad144b7f8ec1e3a46e77d2e2e51e71b2.mp3\",\"source_id\":131}",
    "recording_file": "https://api.twilio.com/2010-04-01/Accounts/AC75402c32b34d5e0c5dec206c3b865cf5/Recordings/REad144b7f8ec1e3a46e77d2e2e51e71b2.mp3",
    "property_category_id": 1,
    "propertyCategory": {
    "id": 1,
    "title": "Apartment"
    },
    "agent_id": {
    "id": 1409,
    "bitrix_role": "Secondary, Azim",
    "name": "Inoka Diddeniya",
    "full_name": "Inoka Diddeniya",
    "first_name": "Inoka",
    "last_name": "Diddeniya",
    "position": "Property Consultant",
    "email": "i.diddeniya@axcapital.ae",
    "secondary_email": null,
    "password": "$2y$10$IAHgKfNywqUSbPJRXrmTj.K7BX0Bq22.HMPNRJtOWJLWm87K.Nxby",
    "roles": [
    {
    "id": 5,
    "name": "Agent",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-14T03:29:47.000000Z",
    "updated_at": "2023-02-14T03:29:47.000000Z",
    "pivot": {
    "model_id": 1409,
    "role_id": 5,
    "model_type": "App\\Models\\User"
    }
    }
    ],
    "mobile": "+971581650321",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_1706248833zDM3dIX4Ma20240126100033.JPG",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/1706248833zDM3dIX4Ma20240126100033.JPG",
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "5970238984",
    "status": 1,
    "locations": [],
    "parent_id": 129,
    "bio": null,
    "is_report_exempt": 0,
    "is_manager": 0
    }
    },
    {
    "id": 19801,
    "first_name": "MUHAMMAD",
    "last_name": "UMER",
    "email": null,
    "mobile": "+971506547204",
    "unit_no": "1504",
    "for": "Residential for rent",
    "bed_id": "2",
    "bed": {
    "id": 2,
    "name": "1 bed"
    },
    "bath_id": "1",
    "bath": {
    "id": 1,
    "name": "1 bath"
    },
    "status_id": 3,
    "status": {
    "id": 3,
    "name": "Contacted"
    },
    "source_id": 131,
    "source": {
    "id": 131,
    "name": "AX CONTACTS"
    },
    "community_id": 82,
    "sub_community_id": 1096,
    "community_property_id": 1205,
    "location_id": 1205,
    "location_type": "community_property",
    "note": "Listing Lead Imported : AX CONTACTS \r\n\r\n\r\nLocation: Marina Diamond 6,Marina Diamonds,Dubai Marina,Dubai,UAE\r\n\r\n\r\n  Webhook Data: POST /api/webhook/ax-contacts/save-listing-leads HTTP/1.1\r\nAccept:         application/json\r\nContent-Length: 576\r\nContent-Type:   application/json\r\nHost:           secondary.axcrm.ae\r\nUser-Agent:     GuzzleHttp/7\r\n\r\n{\"owner_id\":140753,\"property_id\":225292,\"owner_first_name\":\"MUHAMMAD\",\"owner_last_name\":\"UMER\",\"owner_mobile\":\"+971506547204\",\"owner_email\":\"\",\"property_type\":\"Apartment\",\"property_purpose\":\"Residential for rent\",\"property_location\":\"Marina Diamond 6,Marina Diamonds,Dubai Marina,Dubai,UAE\",\"property_bed\":\"1 bed\",\"property_bath\":\"1 bath\",\"property_unit_no\":\"1504\",\"agent_email\":\"d.adomaitis@axcapital.ae\",\"call_recording\":\"https:\\/\\/api.twilio.com\\/2010-04-01\\/Accounts\\/AC75402c32b34d5e0c5dec206c3b865cf5\\/Recordings\\/REf7b44e8ee99e62270190660808bda26a.mp3\",\"source_id\":131}",
    "recording_file": "https://api.twilio.com/2010-04-01/Accounts/AC75402c32b34d5e0c5dec206c3b865cf5/Recordings/REf7b44e8ee99e62270190660808bda26a.mp3",
    "property_category_id": 1,
    "propertyCategory": {
    "id": 1,
    "title": "Apartment"
    },
    "agent_id": {
    "id": 1226,
    "bitrix_role": "Secondary, Otabek",
    "name": "Diana Adomaitis",
    "full_name": "Diana Adomaitis",
    "first_name": "Diana",
    "last_name": "Adomaitis",
    "position": "Property Consultant",
    "email": "d.adomaitis@axcapital.ae",
    "secondary_email": null,
    "password": "$2y$10$fGxYH0JpHU/R6RZ8tHfvV.6qMqoT63UMp7po6jPfLx/WgER8t19NO",
    "roles": [
    {
    "id": 5,
    "name": "Agent",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-14T03:29:47.000000Z",
    "updated_at": "2023-02-14T03:29:47.000000Z",
    "pivot": {
    "model_id": 1226,
    "role_id": 5,
    "model_type": "App\\Models\\User"
    }
    }
    ],
    "mobile": "+971521232852",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_170721311120240206135151.jpg",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/170721311120240206135151.jpg",
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "5970238984",
    "status": 1,
    "locations": [],
    "parent_id": 23,
    "bio": null,
    "is_report_exempt": 0,
    "is_manager": 0
    }
    },
    {
    "id": 4860,
    "first_name": "Sagheer",
    "last_name": "Hussain",
    "email": "sagheer@test.com",
    "mobile": "+971585830076",
    "unit_no": "232",
    "for": "Residential for sale",
    "bed_id": "2",
    "bed": {
    "id": 2,
    "name": "1 bed"
    },
    "bath_id": "1",
    "bath": {
    "id": 1,
    "name": "1 bath"
    },
    "status_id": 5,
    "status": {
    "id": 5,
    "name": "Successful"
    },
    "source_id": 1,
    "source": {
    "id": 1,
    "name": "Agent"
    },
    "community_id": 1,
    "sub_community_id": 1,
    "community_property_id": null,
    "location_id": 1,
    "location_type": "sub_community",
    "note": "Test Listing Lead",
    "recording_file": null,
    "property_category_id": 1,
    "propertyCategory": {
    "id": 1,
    "title": "Apartment"
    },
    "agent_id": {
    "id": 172,
    "bitrix_role": null,
    "name": "Muhammad Saim Aziz",
    "full_name": "Muhammad Saim Aziz",
    "first_name": "Muhammad Saim",
    "last_name": "Aziz",
    "position": "Dev",
    "email": "smirza277@gmail.com",
    "secondary_email": "smirza277@gmail.com",
    "password": "$2y$10$iIHmLaxrxdOsDBeo.gkCcO0Xh.kExiloBLAxSQPncxJiivshJ9U16",
    "roles": [
    {
    "id": 5,
    "name": "Agent",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-14T03:29:47.000000Z",
    "updated_at": "2023-02-14T03:29:47.000000Z",
    "pivot": {
    "model_id": 172,
    "role_id": 5,
    "model_type": "App\\Models\\User"
    }
    }
    ],
    "mobile": "+971588159645",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_168457219720230520124317.jpg",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/168457219720230520124317.jpg",
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "5970238984",
    "status": 1,
    "locations": [],
    "parent_id": 173,
    "bio": null,
    "is_report_exempt": 0,
    "is_manager": 0
    }
    },
    {
    "id": 19800,
    "first_name": "Ibrahim AlAmir",
    "last_name": null,
    "email": "",
    "mobile": "966537579360",
    "unit_no": null,
    "for": "Unspecified",
    "bed_id": null,
    "bed": null,
    "bath_id": null,
    "bath": null,
    "status_id": 2,
    "status": {
    "id": 2,
    "name": "Assigned"
    },
    "source_id": 41,
    "source": {
    "id": 41,
    "name": "Whatsapp"
    },
    "community_id": 97,
    "sub_community_id": null,
    "community_property_id": null,
    "location_id": null,
    "location_type": null,
    "note": "Imported from Bitrix24 webhook\n                                        \n ID: 1996071\n                                        \n Source Details: \n                                        \n Source Name: Whatsapp\n                                        \n Location: Jumeirah\n                                        \n Nationlity: \n                                        \n Language: \n                                        \n Imported at: 2024-08-08 12:10:56",
    "recording_file": null,
    "property_category_id": null,
    "propertyCategory": null,
    "agent_id": {
    "id": 24,
    "bitrix_role": "Secondary, Otabek",
    "name": "Kairatbek Saparaliev",
    "full_name": "Kairatbek Saparaliev",
    "first_name": "Kairatbek",
    "last_name": "Saparaliev",
    "position": "Property Consultant",
    "email": "k.saparaliev@axcapital.ae",
    "secondary_email": null,
    "password": "$2y$10$NHqUHsMPnV.nILOTdCsvlOfbdkF5fJAHEAVbXCKjkfTYYxX/ce2MC",
    "roles": [
    {
    "id": 5,
    "name": "Agent",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-14T03:29:47.000000Z",
    "updated_at": "2023-02-14T03:29:47.000000Z",
    "pivot": {
    "model_id": 24,
    "role_id": 5,
    "model_type": "App\\Models\\User"
    }
    }
    ],
    "mobile": "+971521301689",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_169875806120231031171421.jpg",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/169875806120231031171421.jpg",
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "5970238984",
    "status": 1,
    "locations": [],
    "parent_id": 23,
    "bio": "Kairatbek is a Real Estate Professional based in Dubai, UAE specializing in Palm Jumeirah.  \r\n  \r\nWith his well-rounded experience in all areas of the industry, Kairat prides himself on bringing a more consultative approach for buyers and sellers.  His strong business acumen allows Kairat to build and maintain lifelong relationships. Understanding your home is most likely your largest investment, Kairat believes you should be represented by a partner – a professional.  \r\n  \r\nWhether he is listing, selling, or everything in between, Kairat possesses the local marketplace familiarity, industry knowledge, and negotiation skill set necessary to provide a streamlined transaction for each client he represents. Both driven and equally as dedicated, he strives to make his client’s Real Estate goals a reality by being there for them from closing and beyond.",
    "is_report_exempt": 0,
    "is_manager": 0
    }
    },
    {
    "id": 19798,
    "first_name": "ALEKSEI",
    "last_name": "RYZHKOV",
    "email": null,
    "mobile": "+971504018698",
    "unit_no": "3405",
    "for": "Residential for rent",
    "bed_id": "2",
    "bed": {
    "id": 2,
    "name": "1 bed"
    },
    "bath_id": "1",
    "bath": {
    "id": 1,
    "name": "1 bath"
    },
    "status_id": 5,
    "status": {
    "id": 5,
    "name": "Successful"
    },
    "source_id": 131,
    "source": {
    "id": 131,
    "name": "AX CONTACTS"
    },
    "community_id": 156,
    "sub_community_id": 30149,
    "community_property_id": null,
    "location_id": null,
    "location_type": null,
    "note": "Listing Lead Imported : AX CONTACTS \n\n\nLocation: Golf Heights,The Views,Dubai,UAE\n\n\n  Webhook Data: POST /api/webhook/ax-contacts/save-listing-leads HTTP/1.1\r\nAccept:         application/json\r\nContent-Length: 549\r\nContent-Type:   application/json\r\nHost:           secondary.axcrm.ae\r\nUser-Agent:     GuzzleHttp/7\r\n\r\n{\"owner_id\":43888,\"property_id\":48700,\"owner_first_name\":\"ALEKSEI\",\"owner_last_name\":\"RYZHKOV\",\"owner_mobile\":\"+971504018698\",\"owner_email\":\"\",\"property_type\":\"Apartment\",\"property_purpose\":\"Residential for rent\",\"property_location\":\"Golf Heights,The Views,Dubai,UAE\",\"property_bed\":\"1 bed\",\"property_bath\":\"1 bath\",\"property_unit_no\":\"3405\",\"agent_email\":\"s.baqai@axcapital.ae\",\"call_recording\":\"https:\\/\\/api.twilio.com\\/2010-04-01\\/Accounts\\/AC75402c32b34d5e0c5dec206c3b865cf5\\/Recordings\\/RE672cda53c2035fe3c2bbe9d69fbd0f3b.mp3\",\"source_id\":131}",
    "recording_file": "https://api.twilio.com/2010-04-01/Accounts/AC75402c32b34d5e0c5dec206c3b865cf5/Recordings/RE672cda53c2035fe3c2bbe9d69fbd0f3b.mp3",
    "property_category_id": 1,
    "propertyCategory": {
    "id": 1,
    "title": "Apartment"
    },
    "agent_id": {
    "id": 14,
    "bitrix_role": "Primary, Secondary, Gabriela",
    "name": "Shermeen Baqai",
    "full_name": "Shermeen Baqai",
    "first_name": "Shermeen",
    "last_name": "Baqai",
    "position": "Property Consultant",
    "email": "s.baqai@axcapital.ae",
    "secondary_email": null,
    "password": "$2y$10$shPWcqWXsFtyuh0kYyFZduAPbs7AhKx1T5giWYRWWOc5hA89t2UEa",
    "roles": [
    {
    "id": 5,
    "name": "Agent",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-14T03:29:47.000000Z",
    "updated_at": "2023-02-14T03:29:47.000000Z",
    "pivot": {
    "model_id": 14,
    "role_id": 5,
    "model_type": "App\\Models\\User"
    }
    }
    ],
    "mobile": "+971521804510",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_169878095020231031233550.jpg",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/169878095020231031233550.jpg",
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "5970238984",
    "status": 1,
    "locations": [],
    "parent_id": 16,
    "bio": "Shermeen Baqai is a sales associate at AX Capital Real Estate. Shermeen is a postgraduate from the University of Wollongong where she received her master's degree in business administration, international business, economics, and finance. Shermeen worked as a Finance Manager in a real estate and developers’ company for four years. Her passion for real estate and strong finance skills in business and investment led her to pursue a sales career in 2018. Shermeen is a villa and townhouse specialist. Working in Dubai Hills Estate and Mudon gave her the opportunity to work with investors and owners alike in these popular residential communities.\r\nHer comprehensive understanding of the Dubai Hills Estate community and excellent knowledge of various residential projects has enabled her to achieve impressive sales volume over the past four years.",
    "is_report_exempt": 0,
    "is_manager": 0
    }
    },
    {
    "id": 19795,
    "first_name": "AISHA",
    "last_name": "YOUSUF BAKER ABDULLA BAKER",
    "email": null,
    "mobile": "+971506443338",
    "unit_no": "3906",
    "for": "Residential for rent",
    "bed_id": "2",
    "bed": {
    "id": 2,
    "name": "1 bed"
    },
    "bath_id": "1",
    "bath": {
    "id": 1,
    "name": "1 bath"
    },
    "status_id": 7,
    "status": {
    "id": 7,
    "name": "Not Answered"
    },
    "source_id": 131,
    "source": {
    "id": 131,
    "name": "AX CONTACTS"
    },
    "community_id": 156,
    "sub_community_id": 30149,
    "community_property_id": null,
    "location_id": 30149,
    "location_type": "sub_community",
    "note": "Listing Lead Imported : AX CONTACTS \r\n\r\n\r\nLocation: Golf Heights,The Views,Dubai,UAE\r\n\r\n\r\n  Webhook Data: POST /api/webhook/ax-contacts/save-listing-leads HTTP/1.1\r\nAccept:         application/json\r\nContent-Length: 565\r\nContent-Type:   application/json\r\nHost:           secondary.axcrm.ae\r\nUser-Agent:     GuzzleHttp/7\r\n\r\n{\"owner_id\":1450,\"property_id\":48434,\"owner_first_name\":\"AISHA\",\"owner_last_name\":\"YOUSUF BAKER ABDULLA BAKER\",\"owner_mobile\":\"+971506443338\",\"owner_email\":\"\",\"property_type\":\"Apartment\",\"property_purpose\":\"Residential for rent\",\"property_location\":\"Golf Heights,The Views,Dubai,UAE\",\"property_bed\":\"1 bed\",\"property_bath\":\"1 bath\",\"property_unit_no\":\"3906\",\"agent_email\":\"s.baqai@axcapital.ae\",\"call_recording\":\"https:\\/\\/api.twilio.com\\/2010-04-01\\/Accounts\\/AC75402c32b34d5e0c5dec206c3b865cf5\\/Recordings\\/RE4de206fa433bb4c7cbe5f643ebd2c020.mp3\",\"source_id\":131}",
    "recording_file": "https://api.twilio.com/2010-04-01/Accounts/AC75402c32b34d5e0c5dec206c3b865cf5/Recordings/RE4de206fa433bb4c7cbe5f643ebd2c020.mp3",
    "property_category_id": 1,
    "propertyCategory": {
    "id": 1,
    "title": "Apartment"
    },
    "agent_id": {
    "id": 14,
    "bitrix_role": "Primary, Secondary, Gabriela",
    "name": "Shermeen Baqai",
    "full_name": "Shermeen Baqai",
    "first_name": "Shermeen",
    "last_name": "Baqai",
    "position": "Property Consultant",
    "email": "s.baqai@axcapital.ae",
    "secondary_email": null,
    "password": "$2y$10$shPWcqWXsFtyuh0kYyFZduAPbs7AhKx1T5giWYRWWOc5hA89t2UEa",
    "roles": [
    {
    "id": 5,
    "name": "Agent",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-14T03:29:47.000000Z",
    "updated_at": "2023-02-14T03:29:47.000000Z",
    "pivot": {
    "model_id": 14,
    "role_id": 5,
    "model_type": "App\\Models\\User"
    }
    }
    ],
    "mobile": "+971521804510",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_169878095020231031233550.jpg",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/169878095020231031233550.jpg",
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "5970238984",
    "status": 1,
    "locations": [],
    "parent_id": 16,
    "bio": "Shermeen Baqai is a sales associate at AX Capital Real Estate. Shermeen is a postgraduate from the University of Wollongong where she received her master's degree in business administration, international business, economics, and finance. Shermeen worked as a Finance Manager in a real estate and developers’ company for four years. Her passion for real estate and strong finance skills in business and investment led her to pursue a sales career in 2018. Shermeen is a villa and townhouse specialist. Working in Dubai Hills Estate and Mudon gave her the opportunity to work with investors and owners alike in these popular residential communities.\r\nHer comprehensive understanding of the Dubai Hills Estate community and excellent knowledge of various residential projects has enabled her to achieve impressive sales volume over the past four years.",
    "is_report_exempt": 0,
    "is_manager": 0
    }
    },
    {
    "id": 19796,
    "first_name": "OSAMA",
    "last_name": "MOHAMMAD AWAD AL BADDAD",
    "email": null,
    "mobile": "+971504474561",
    "unit_no": "2705",
    "for": "Residential for rent",
    "bed_id": "2",
    "bed": {
    "id": 2,
    "name": "1 bed"
    },
    "bath_id": "1",
    "bath": {
    "id": 1,
    "name": "1 bath"
    },
    "status_id": 6,
    "status": {
    "id": 6,
    "name": "Unsuccessful"
    },
    "source_id": 131,
    "source": {
    "id": 131,
    "name": "AX CONTACTS"
    },
    "community_id": 156,
    "sub_community_id": 30149,
    "community_property_id": null,
    "location_id": null,
    "location_type": null,
    "note": "Listing Lead Imported : AX CONTACTS \n\n\nLocation: Golf Heights,The Views,Dubai,UAE\n\n\n  Webhook Data: POST /api/webhook/ax-contacts/save-listing-leads HTTP/1.1\r\nAccept:         application/json\r\nContent-Length: 563\r\nContent-Type:   application/json\r\nHost:           secondary.axcrm.ae\r\nUser-Agent:     GuzzleHttp/7\r\n\r\n{\"owner_id\":27705,\"property_id\":48674,\"owner_first_name\":\"OSAMA\",\"owner_last_name\":\"MOHAMMAD AWAD AL BADDAD\",\"owner_mobile\":\"+971504474561\",\"owner_email\":\"\",\"property_type\":\"Apartment\",\"property_purpose\":\"Residential for rent\",\"property_location\":\"Golf Heights,The Views,Dubai,UAE\",\"property_bed\":\"1 bed\",\"property_bath\":\"1 bath\",\"property_unit_no\":\"2705\",\"agent_email\":\"s.baqai@axcapital.ae\",\"call_recording\":\"https:\\/\\/api.twilio.com\\/2010-04-01\\/Accounts\\/AC75402c32b34d5e0c5dec206c3b865cf5\\/Recordings\\/RE5d812e0da9ffeab86b7d7a380a77b4a1.mp3\",\"source_id\":131}",
    "recording_file": "https://api.twilio.com/2010-04-01/Accounts/AC75402c32b34d5e0c5dec206c3b865cf5/Recordings/RE5d812e0da9ffeab86b7d7a380a77b4a1.mp3",
    "property_category_id": 1,
    "propertyCategory": {
    "id": 1,
    "title": "Apartment"
    },
    "agent_id": {
    "id": 14,
    "bitrix_role": "Primary, Secondary, Gabriela",
    "name": "Shermeen Baqai",
    "full_name": "Shermeen Baqai",
    "first_name": "Shermeen",
    "last_name": "Baqai",
    "position": "Property Consultant",
    "email": "s.baqai@axcapital.ae",
    "secondary_email": null,
    "password": "$2y$10$shPWcqWXsFtyuh0kYyFZduAPbs7AhKx1T5giWYRWWOc5hA89t2UEa",
    "roles": [
    {
    "id": 5,
    "name": "Agent",
    "guard_name": "web",
    "status": 1,
    "created_at": "2023-02-14T03:29:47.000000Z",
    "updated_at": "2023-02-14T03:29:47.000000Z",
    "pivot": {
    "model_id": 14,
    "role_id": 5,
    "model_type": "App\\Models\\User"
    }
    }
    ],
    "mobile": "+971521804510",
    "thumbnail": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/thumb_169878095020231031233550.jpg",
    "image": "https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/avatar/169878095020231031233550.jpg",
    "avatar": null,
    "telegram_username": null,
    "telegram_chatid": "5970238984",
    "status": 1,
    "locations": [],
    "parent_id": 16,
    "bio": "Shermeen Baqai is a sales associate at AX Capital Real Estate. Shermeen is a postgraduate from the University of Wollongong where she received her master's degree in business administration, international business, economics, and finance. Shermeen worked as a Finance Manager in a real estate and developers’ company for four years. Her passion for real estate and strong finance skills in business and investment led her to pursue a sales career in 2018. Shermeen is a villa and townhouse specialist. Working in Dubai Hills Estate and Mudon gave her the opportunity to work with investors and owners alike in these popular residential communities.\r\nHer comprehensive understanding of the Dubai Hills Estate community and excellent knowledge of various residential projects has enabled her to achieve impressive sales volume over the past four years.",
    "is_report_exempt": 0,
    "is_manager": 0
    }
    }
    ],
    "first_page_url": "http://secondary.lan/api/v1/listing-leads?page=1",
    "from": 1,
    "last_page": 1113,
    "last_page_url": "http://secondary.lan/api/v1/listing-leads?page=1113",
    "links": [
    {
    "url": null,
    "label": "&laquo; Previous",
    "active": false
    },
    {
    "url": "http://secondary.lan/api/v1/listing-leads?page=1",
    "label": "1",
    "active": true
    },
    {
    "url": "http://secondary.lan/api/v1/listing-leads?page=2",
    "label": "2",
    "active": false
    },
    {
    "url": "http://secondary.lan/api/v1/listing-leads?page=3",
    "label": "3",
    "active": false
    },
    {
    "url": "http://secondary.lan/api/v1/listing-leads?page=4",
    "label": "4",
    "active": false
    },
    {
    "url": "http://secondary.lan/api/v1/listing-leads?page=5",
    "label": "5",
    "active": false
    },
    {
    "url": "http://secondary.lan/api/v1/listing-leads?page=6",
    "label": "6",
    "active": false
    },
    {
    "url": "http://secondary.lan/api/v1/listing-leads?page=7",
    "label": "7",
    "active": false
    },
    {
    "url": "http://secondary.lan/api/v1/listing-leads?page=8",
    "label": "8",
    "active": false
    },
    {
    "url": "http://secondary.lan/api/v1/listing-leads?page=9",
    "label": "9",
    "active": false
    },
    {
    "url": "http://secondary.lan/api/v1/listing-leads?page=10",
    "label": "10",
    "active": false
    },
    {
    "url": null,
    "label": "...",
    "active": false
    },
    {
    "url": "http://secondary.lan/api/v1/listing-leads?page=1112",
    "label": "1112",
    "active": false
    },
    {
    "url": "http://secondary.lan/api/v1/listing-leads?page=1113",
    "label": "1113",
    "active": false
    },
    {
    "url": "http://secondary.lan/api/v1/listing-leads?page=2",
    "label": "Next &raquo;",
    "active": false
    }
    ],
    "next_page_url": "http://secondary.lan/api/v1/listing-leads?page=2",
    "path": "http://secondary.lan/api/v1/listing-leads",
    "per_page": 10,
    "prev_page_url": null,
    "to": 10,
    "total": 11125
    }
    },
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 200 scenario="leads not found"
     * {
    "status": false,
    "code": 200,
    "message": "No Leads found",
    "data": []
    }
     */
    public function index(GetListingLeadRequest $request, ListingLeadTask $task)
    {
        if (!auth()->user()->can('Leads List')) {
            return $this->sendResponse(false, 'You are not authorized to access this resource', [], 403);
        }

        $response = $task->run($request);
        return $this->sendResponse($response['status'], $response['message'], $response['data'], $response['code']);
    }

    public function searchListingLeads(GetListingLeadRequest $request, SearchListingLeadTask $task)
    {
        if (!auth()->user()->can('Leads List')) {
            return $this->sendResponse(false, 'You are not authorized to access this resource', [], 403);
        }

        $response = $task->run($request);
        return $this->sendResponse($response['status'], $response['message'], $response['data'], $response['code']);
    }

    /**
     *
     *
     *
     * Create Listing Lead
     * @group Listing Leads
     * @authenticated
     * @header Accept application/json
     * @bodyParam for nullable for lead purpose. Example: Residential for sale
     * @bodyParam unit_no nullable for unit_no. Example: 123H
     * @bodyParam bed_id nullable for bed id. Example:1
     * @bodyParam bath_id nullable for bath id. Example: 12
     * @bodyParam location_id nullable for location id. Example: 12
     * @bodyParam location_type nullable for deciding is it sub community or community property. Example: community_property
     * @bodyParam source_id nullable for source of the lead. Example: 3
     * @bodyParam first_name required for owner first name. Example: John
     * @bodyParam last_name nullable for owner last name. Example: Doe
     * @bodyParam email nullable for owner email. Example: email@mail.com
     * @bodyParam mobile required for owner mobile. Example: 971123456789
     * @bodyParam recording_file nullable for lead id. Example: mp3
     * @bodyParam property_category_id nullable for Property Category. Example: 1
     * @bodyParam agent_id required for assigning to an agent. Example: 1
     * @bodyParam status_id required for lead status. Example: 1
     * @bodyParam note required for lead note. Example: This is a note
     *
     *
     *

     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Listing Lead Added Successfully.",
    "data": "Success",
    "page": 1,
    "per_page": 10
    }
     * @response 422 scenario="validation Error"
    {
    "status": false,
    "code": 422,
    "message": "Validation Error",
    "data": {
    "for": [
    "The for field is required."
    ],
    "bed_id": [
    "The bed id field is required."
    ],
    "location_id": [
    "The location id field is required."
    ],
    "source_id": [
    "The source id field is required."
    ],
    "first_name": [
    "The first name field is required."
    ],
    "mobile": [
    "The mobile field is required."
    ],
    "status_id": [
    "The status id field is required."
    ],
    "agent_id": [
    "The agent id field is required."
    ],
    "note": [
    "The note field is required."
    ]
    },
    "page": 1,
    "per_page": 10
    }
     */

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "for" => "required",
            'bed_id' => 'nullable',
            'bath_id' => 'nullable',
            'location_id' => 'required',
            'location_type' => 'nullable',
            'source_id' => 'nullable',
            'first_name' => 'required',
            'last_name' => 'nullable',
            'email' => 'nullable',
            'mobile' => 'required',
            'recording_file' => 'nullable|mimes:mp3,wav,ogg,mpga,m4a,3gp,3gpp,amr,|max:30000',
            'property_category_id' => 'nullable',
            'source_id' => 'required',
            'status_id' => 'required',
            'agent_id' => 'required',
            'note' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        if (checkBlocklist($request->mobile, ListingLead::class, $request)) {

            return $this->sendResponse(false, 'This number is in Blacklist. Please do not contact with this number.', [], 403);

        }

        try {
            $property = new ListingLead();
            $property->first_name = $request->first_name;
            $property->last_name = $request->last_name;
            $property->email = $request->email;
            $property->mobile = validatePhoneNumber($request->mobile);
            $property->source_id = $request->source_id;
            $property->medium_id = $request->medium_id;

            $property->title = $request->title;
            $property->for = is_null($request->for) ? [] : $request->for;
            $property->bed_id = $request->bed_id;
            $property->bath_id = $request->bath_id;
            $property->location_id = $request->location_id;
            $property->location_type = $request->location_type;

            $property->status_id = $request->status_id ?? 1;
            $property->unit_no = $request->unit_no;
            $property->note = $request->note;
            $property->property_category_id = $request->property_category_id;

            if ($request->location_type == "community_property") {
                $communityProperty = CommunityProperty::find($request->location_id);
                $community_property_id = $communityProperty->id ?? null;
                $sub_community_id = $communityProperty->sub_community_id ?? null;
                $community_id = $communityProperty->community_id ?? null;
            } else {

                $subCommunity = SubCommunity::find($request->location_id);
                $community_property_id = null;
                $sub_community_id = $subCommunity->id ?? null;
                $community_id = $subCommunity->community_id ?? null;
            }

            if ($request->file("recording_file")) {
                $path = $this->uploadDocument($request->file("recording_file"), "lead_listing");
                $property->recording_file = $path;
            }

            $property->sub_community_id = $sub_community_id;
            $property->community_id = $community_id;
            $property->community_property_id = $community_property_id;
            $property->save();

            if ($request->agent_id) {
                $agent = User::where('id', $request->agent_id)->first();
                $obj = new ListingLeadNote();
                $obj->listing_lead_id = $property->id;
                $obj->note = $agent->first_name . ' ' . $agent->last_name . ' as a Agent Assigned by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name . ' using Mobile Application.';
                $obj->created_by_id = auth()->id();
                $obj->save();
                $property->status_id = 2;
            }

            if ($request->telesales_id) {
                $telesales = User::where('id', $request->telesales_id)->first();
                $obj = new ListingLeadNote();
                $obj->listing_lead_id = $property->id;
                $obj->note = $telesales->first_name . ' ' . $telesales->last_name . ' as a Agent Assigned by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name . ' using Mobile Application.';
                $obj->created_by_id = auth()->id();
                $obj->save();
            }

            // if ($request->manager_id && ($property->manager_id != $request->manager_id)) {
            //     if ($request->manager_id != $property->manager_id) {
            //         $manager = User::where('id', $request->manager_id)->first();

            //         $obj = new ListingLeadNote();
            //         $obj->listing_lead_id = $property->id;
            //         $obj->note = $manager->first_name . ' ' . $manager->last_name . ' as a Manager Assigned by ' . auth()->user()->first_name . ' ' . auth()->user()->last_name . '.';
            //         $obj->created_by_id = auth()->id();
            //         $obj->save();
            //         $property->status_id = 2;

            //     }
            // }

            $property->manager_id = $request->manager_id;
            $property->agent_id = $request->agent_id;
            $property->telesales_id = $request->telesales_id;
            $property->save();

            $environment = \App::environment();
            // if ($environment == "production") {
            //     $message = "<b>AX Secondary CRM</b>\n\n<b>New Listing Lead:</b>\nA new listing lead has been received. Please assign it to an agent.";
            //     $agents = User::whereHas("roles", function ($q) {
            //         $q->whereIn("name", ["Line Manager", "Team Leader", "Sales Director"]);
            //     })->where("status", true)->get();
            //     foreach ($agents as $agent) {
            //         if ($agent->telegram_chatid != null) {
            //             $this->sendTelegramMessageHtml($message, [$agent->telegram_chatid], null, null, 'lead_bot');
            //         }
            //     }
            // }

            return $this->sendResponse(true, 'Listing Lead Added Successfully.', ['id' => $property->id], 200);

        } catch (\Exception $th) {
            return $this->sendResponse(false, 'Something went wrong!!.', ['error' => $th->getMessage()], 500);

            // return response()->json(['status' => '0', 'msg' => 'Something went wrong!!', 'error' => $th->getMessage()]);
        }
    }

    /**
     *
     *
     *
     * Update Listing Lead
     * @group Listing Leads
     * @authenticated
     * @header Accept application/json
     * @bodyParam for nullable for lead purpose. Example: Residential for sale
     * @bodyParam id required for listing lead id. Example:1
     * @bodyParam bed_id nullable for bed id. Example:1
     * @bodyParam bath_id nullable for bath id. Example: 12
     * @bodyParam unit_no nullable for unit_no. Example: 123H
     * @bodyParam location_id nullable for location id. Example: 12
     * @bodyParam location_type nullable for deciding is it sub community or community property. Example: community_property
     * @bodyParam source_id nullable for source of the lead. Example: 3
     * @bodyParam first_name required for owner first name. Example: John
     * @bodyParam last_name nullable for owner last name. Example: Doe
     * @bodyParam email nullable for owner email. Example: email@mail.com
     * @bodyParam mobile required for owner mobile. Example: 971123456789
     * @bodyParam recording_file nullable for lead id. Example: mp3
     * @bodyParam property_category_id nullable for Property Category. Example: 1
     * @bodyParam agent_id required for assigning to an agent. Example: 1
     * @bodyParam status_id required for lead status. Example: 1
     * @bodyParam note required for lead note. Example: This is a note
     *

     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Listing lead Updated Successfully.",
    "data": "Success",
    "page": 1,
    "per_page": 10
    }
     * @response 422 scenario="validation Error"
    {
    "status": false,
    "code": 422,
    "message": "Validation Error",
    "data": {
    "for": [
    "The for field is required."
    ],
    "bed_id": [
    "The bed id field is required."
    ],
    "location_id": [
    "The location id field is required."
    ],
    "source_id": [
    "The source id field is required."
    ],
    "first_name": [
    "The first name field is required."
    ],
    "mobile": [
    "The mobile field is required."
    ],
    "status_id": [
    "The status id field is required."
    ],
    "agent_id": [
    "The agent id field is required."
    ],
    "note": [
    "The note field is required."
    ]
    },
    "page": 1,
    "per_page": 10
    }
     */
    public function update(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            "for" => "required",
            'bed_id' => 'nullable',
            'bath_id' => 'nullable',
            'location_id' => 'required',
            'location_type' => 'nullable',
            'source_id' => 'nullable',
            'first_name' => 'required',
            'last_name' => 'nullable',
            'email' => 'nullable',
            'mobile' => 'required',
            'recording_file' => 'nullable|mimes:mp3,wav,ogg,mpga,m4a,3gp,3gpp,amr,|max:30000',
            'property_category_id' => 'nullable',
            'source_id' => 'required',
            'status_id' => 'required',
            'agent_id' => 'required',
            'note' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        if (checkBlocklist($request->mobile, ListingLead::class, $request)) {

            return $this->sendResponse(false, 'This number is in Blacklist. Please do not contact with this number.', [], 403);

        }

        try {
            $property = ListingLead::find($request->id);

            $property->first_name = $request->first_name;
            $property->last_name = $request->last_name;
            $property->email = $request->email;
            $property->mobile = validatePhoneNumber($request->mobile);

            $source = explode('-', $request->source_id);
            if ($request->medium_id) {
                // $medium = explode('-', $request->medium_id);
                $property->medium_id = $request->medium_id;

            }

            if ($request->source_id) {
                // $source = explode('-', $request->source_id);
                $property->source_id = $request->source_id;
            } // $property->medium_id = $request->medium_id;

            $property->title = $request->title;
            $property->for = $request->for;
            $property->bed_id = $request->bed_id;
            $property->bath_id = $request->bath_id;
            $property->location_id = $request->location_id;
            $property->location_type = $request->location_type;
            $property->property_category_id = $request->property_category_id;

            $property->unit_no = $request->unit_no;
            $property->note = $request->note;

            if ($request->location_type == "community_property") {
                $communityProperty = CommunityProperty::find($request->location_id);
                $community_property_id = $communityProperty->id ?? null;
                $sub_community_id = $communityProperty->sub_community_id ?? null;
                $community_id = $communityProperty->community_id ?? null;
            } else {

                $subCommunity = SubCommunity::find($request->location_id);
                $community_property_id = null;
                $sub_community_id = $subCommunity->id ?? null;
                $community_id = $subCommunity->community_id ?? null;
            }

            $property->sub_community_id = $sub_community_id;
            $property->community_id = $community_id;
            $property->community_property_id = $community_property_id;
            $original = $property->getOriginal() ?? [];
            $dirty = $property->getDirty() ?? [];

            $original = array_intersect_key($original, $dirty);

            $property->save();

            if ($request->agent_id && ($property->agent_id != $request->agent_id)) {
                // dd($request->agent_id, $property->agent_id);
                if ($request->agent_id != $property->agent_id) {
                    $agent = User::where('id', $request->agent_id)->first();
                    $obj = new ListingLeadNote();
                    $obj->listing_lead_id = $property->id;
                    $obj->note = "The lead has been assigned to " . $agent->first_name . ' ' . $agent->last_name . ".";
                    $obj->created_by_id = auth()->id();
                    $obj->save();
                    $property->status_id = 2;
                }

            }
            if ($request->telesales_id && ($property->telesales_id != $request->telesales_id)) {
                if ($request->telesales_id != $property->telesales_id) {
                    $telesales = User::where('id', $request->telesales_id)->first();
                    $obj = new ListingLeadNote();
                    $obj->listing_lead_id = $property->id;
                    $obj->note = "The lead has been assigned to " . $telesales->first_name . ' ' . $telesales->last_name . "as a telesales.";
                    $obj->created_by_id = auth()->id();
                    $obj->save();
                    $property->status_id = 2;
                }

            }

            if ($request->agent_id == $property->agent_id) {
                $property->status_id = $request->status_id;
            }
            $property->agent_id = $request->agent_id;
            $property->updated_by_id = auth()->id();
            $property->telesales_id = $request->telesales_id;
            $property->save();

            $arr = ['attributes' => $dirty,
                'old' => $original, 'listing_lead_id' => $property->id];
            activity()
                ->performedOn($property)
                ->causedBy(auth()->user())
                ->withProperties($arr)
                ->event('deleted')
                ->log(auth()->user()->first_name . ' has Updated the listing lead using Mobile Application');

            return $this->sendResponse(true, 'Listing lead Updated Successfully.', [], 200);

        } catch (\Exception $exception) {
            // dd($exception->getMessage());

            return $this->sendResponse(false, 'Something went wrong', ['error' => $exception], 500);

        }
    }

    /**
     *
     *
     *
     * Show Listing Lead
     * @group Listing Leads
     * @authenticated
     * @header Accept application/json
     * @bodyParam id required for listing lead id. Example:1

     *

     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Lead Found.",
    "data": {
    "data": {
    "id": 4860,
    "first_name": "Sagheer",
    "last_name": "Hussain",
    "email": "sagheer@test.com",
    "mobile": "+971585830076",
    "unit_no": "232",
    "for": "Residential for sale",
    "bed": {
    "id": 2,
    "name": "1 bed",
    "status": 1,
    "order": 1,
    "created_by_id": null,
    "updated_by_id": 1,
    "deleted_at": null,
    "created_at": "2023-02-14T03:30:31.000000Z",
    "updated_at": "2023-05-31T11:49:06.000000Z"
    },
    "bath": {
    "id": 1,
    "name": "1 bath",
    "status": 1,
    "order": 1,
    "created_by_id": null,
    "updated_by_id": 1,
    "deleted_at": null,
    "created_at": "2023-02-14T03:30:31.000000Z",
    "updated_at": "2023-05-31T11:46:11.000000Z"
    },
    "status": {
    "id": 2,
    "name": "Assigned",
    "status": 1,
    "icon": "ri ri-thumb-up-line",
    "badge": "badge color-bg-10",
    "thumbnail": null,
    "image": null,
    "order": 40,
    "created_by_id": null,
    "updated_by_id": 262,
    "deleted_at": null,
    "deleted_by_id": null,
    "created_at": "2023-06-20T10:53:47.000000Z",
    "updated_at": "2023-08-08T06:45:32.000000Z"
    }
    }
    },
    "page": 1,
    "per_page": 10
    }
     * @response 422 scenario="validation Error"
    {
    "status": false,
    "code": 422,
    "message": "Validation Error",
    "data": {
    "id": [
    "The id field is required."
    ],

    },
    "page": 1,
    "per_page": 10
    }
     */
    public function show(Request $request, $id)
    {

        $notAdmin = auth()->user()->hasRole(["Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $lead = ListingLead::where('id', $id)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->where(function ($q) use ($childrenIds) {
                    $q->whereIn('agent_id', $childrenIds);
                });
            })->first();
        if (!empty($lead)) {
            $lead = new ListingLeadResource($lead);

            return $this->sendResponse(true, 'Lead Found.', ['data' => $lead], 200);

        } else {
            return $this->sendResponse(false, 'Lead Found.', [], 404);

        }
    }

    /**
     *
     *
     *
     * Add Note to Listing Lead
     * @group Listing Leads
     * @authenticated
     * @header Accept application/json
     * @bodyParam listing_lead_id required for listing lead id. Example:1
     * @bodyParam note required for note. Example: This is a note
     * @bodyParam agent_id required for assigning to an agent. Example: 1

     *

     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Lead note added successfully.",
    "data": {
    "data": {
    "listing_lead_id": "101",
    "note": "THis is a test id",
    "created_by_id": 172,
    "updated_at": "2024-07-15T13:42:12.000000Z",
    "created_at": "2024-07-15T13:42:12.000000Z",
    "id": 41677
    }
    },
    "page": 1,
    "per_page": 10
    }
     * @response 422 scenario="validation Error"
    {
    "status": false,
    "code": 422,
    "message": "Validation Error",
    "data": {
    "id": [
    "The listing_lead_id field is required."
    ],
    },
    "page": 1,
    "per_page": 10
    }
     */
    public function addNote(Request $request)
    {
        $request->validate([
            'listing_lead_id' => 'required|exists:leads,id',
            'note' => 'required|string',
        ]);

        $validator = Validator::make($request->all(), [
            'listing_lead_id' => 'required|exists:leads,id',
            'note' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        try {
            if ($request->agent_id != auth()->user()->id) {
                return $this->sendResponse(false, 'You are not authorized to access this resource', [], 403);
            }
            $obj = new ListingLeadNote();
            $obj->listing_lead_id = $request->listing_lead_id;
            $obj->note = $request->note;
            $obj->created_by_id = auth()->id();
            $obj->save();
            // event(new LeadNoteAdded($obj));
            $result = array('status' => '1', 'msg' => "Lead note added successfully.");
            return $this->sendResponse(true, 'Lead note added successfully.', ['data' => $obj], 200);

        } catch (\Exception $exception) {
            return $this->sendResponse(false, 'Something went wrong!!', ['error' => $exception->getMessage()], 500);

        }
    }

    /**
     *
     *
     *
     * Convert Listing Lead to Listing
     * @group Listing Leads
     * @authenticated
     * @header Accept application/json
     * @bodyParam listing_lead_id required for listing lead id. Example:1
     * @bodyParam for nullable for lead purpose. Example: Residential for sale
     * @bodyParam bed_id nullable for bed id. Example:1
     * @bodyParam bath_id nullable for bath id. Example: 12
     * @bodyParam listing_location_id nullable for location id. Example: 12
     * @bodyParam listing_location_type nullable for deciding is it sub community or community property. Example: community_property
     * @bodyParam source_id nullable for source of the lead. Example: 3
     * @bodyParam first_name required for owner first name. Example: John
     * @bodyParam last_name nullable for owner last name. Example: Doe
     * @bodyParam email nullable for owner email. Example:
     * @bodyParam mobile required for owner mobile. Example: 971123456789
     * @bodyParam agent_id required for assigning to an agent. Example: 1
     * @bodyParam property_category_id nullable for Property Category. Example: 1


     *

     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Converted to listing",
    "data": {
    "listing": {
    "ref_no": "axc-3978747",
    "title": null,
    "for": "Residential for sale",
    "bed_id": "1",
    "bath_id": "1",
    "property_category_id": "1",
    "sub_community_id": 30,
    "community_id": 10,
    "community_property_id": null,
    "publish_status": "DRAFT",
    "unit_no": "101",
    "owner_id": 18180,
    "agent_id": "172",
    "created_by_id": 172,
    "region_id": 1,
    "updated_at": "2024-07-15T14:38:03.000000Z",
    "created_at": "2024-07-15T14:38:03.000000Z",
    "id": 20442,
    "first_image": null,
    "category": "Apartment",
    "location": "The Residences at District One, Mohammed Bin Rashid City, Dubai",
    "my_property": true,
    "property_category": {
    "id": 1,
    "title": "Apartment",
    "slug": "apartment",
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
    "updated_at": "2023-06-26T14:31:16.000000Z",
    "deleted_at": null
    },
    "community_property": null,
    "sub_community": {
    "id": 30,
    "region_id": 1,
    "community_id": 10,
    "name": "The Residences at District One",
    "alternate_name": null,
    "slug": "the-residences-at-district-one",
    "description": null,
    "path_name": "The Residences at District One, Mohammed Bin Rashid City, Dubai",
    "alternate_path_name": null,
    "latitude": "25.171461",
    "longitude": "55.2758",
    "created_by_id": 1,
    "updated_by_id": 1,
    "status": 1,
    "created_at": "2020-04-29T19:54:06.000000Z",
    "updated_at": "2020-04-29T19:54:06.000000Z",
    "deleted_at": null
    }
    },
    "listing_lead": {
    "id": 19175,
    "bitrix_id": null,
    "first_name": "Antonios",
    "last_name": "Clive representative",
    "email": "",
    "mobile": "35799380070",
    "title": null,
    "bed_id": "1",
    "bath_id": "1",
    "for": "Residential for sale",
    "unit_no": null,
    "type_id": 3,
    "source_id": 41,
    "medium_id": null,
    "location_id": null,
    "community_property_id": null,
    "community_id": 10,
    "sub_community_id": 30,
    "location_type": null,
    "note": "Imported from Bitrix24 webhook\n                                        \n ID: 1964963\n                                        \n Source Details: \n                                        \n Source Name: Whatsapp\n                                        \n Location: Dubai Harbour\n                                        \n Nationlity: \n                                        \n Language: \n                                        \n Imported at: 2024-07-01 13:41:57",
    "sale_id": null,
    "assigned_by_id": null,
    "created_by_id": null,
    "updated_by_id": 172,
    "deleted_by_id": null,
    "created_at": "2024-07-01T09:41:57.000000Z",
    "updated_at": "2024-07-15T14:38:03.000000Z",
    "deleted_at": null,
    "agent_id": "172",
    "telesales_id": null,
    "manager_id": null,
    "status_id": 5,
    "is_archive": 0,
    "contact_id": 18180,
    "property_id": 20442,
    "recording_file": null,
    "property_category_id": "1",
    "location": "The Residences at District One, Mohammed Bin Rashid City, Dubai",
    "community_property": null,
    "sub_community": {
    "id": 30,
    "region_id": 1,
    "community_id": 10,
    "name": "The Residences at District One",
    "alternate_name": null,
    "slug": "the-residences-at-district-one",
    "description": null,
    "path_name": "The Residences at District One, Mohammed Bin Rashid City, Dubai",
    "alternate_path_name": null,
    "latitude": "25.171461",
    "longitude": "55.2758",
    "created_by_id": 1,
    "updated_by_id": 1,
    "status": 1,
    "created_at": "2020-04-29T19:54:06.000000Z",
    "updated_at": "2020-04-29T19:54:06.000000Z",
    "deleted_at": null
    }
    }
    },
    "page": 1,
    "per_page": 10
    }
     * @response 422 scenario="validation Error"
    {
    "status": false,
    "code": 422,
    "message": "Validation Error",
    "data": {
    "id": [
    "The listing_lead_id field is required."
    ],
    },
    "page": 1,
    "per_page": 10
    }
     */

    public function convertToListing(Request $request)
    {
        $request->validate([
            'for' => 'required',
            'unit_no' => 'required',
            'mobile' => 'required',
            'email' => 'nullable',
            'first_name' => 'required',
            'agent_id' => 'required',
            'property_category_id' => 'nullable',
            'listing_location_type' => 'nullable',
            'listing_location_id' => 'nullable',
            'bed_id' => 'nullable',
            'bath_id' => 'nullable',
        ]);

        try {
            DB::beginTransaction();
            $already_contact = Contact::where('email', $request->email)
                ->where('mobile', validatePhoneNumber($request->full_mobile))
                ->where('agent_id', $request->agent_id)
                ->first();

            if ($already_contact) {
                $contact = ContactAgent::updateOrCreate([
                    'contact_id' => $already_contact->id,
                    'user_id' => $request->agent_id,
                    'created_by_id' => auth()->id(),
                ]);
                $contact_id = $already_contact->id;
            } else {
                $contact = new Contact();
                $contact->first_name = $request->first_name;
                $contact->last_name = $request->last_name;
                $contact->contact_source_id = 129;
                $contact->email = $request->email;
                $contact->mobile = validatePhoneNumber($request->mobile);

                if ($request->full_mobile) {
                    $contact->mobile = validatePhoneNumber($request->full_mobile);
                }

                $contact->agent_id = $request->agent_id;
                $contact->created_by_id = auth()->id();
                $contact->save();
                $contact_id = $contact->id;

                // $contact_type = new ContactContactType();
                // $contact_type->contact_id = $contact->id;
                // $contact_type->contact_type_id = 1;
                // $contact_type->save();
                // $contact_type = ContactContactType::create([
                //     'contact_id' => $contact->id,
                //     'contact_type_id' => 1,
                // ]);
                // add from relation contactTypes
                $contact->contactTypes()->attach(1);

            }

            $listingLead = ListingLead::find($request->listing_lead_id);
            $listingLead->updated_by_id = auth()->id();

            $listing = new Property();
            $listing->ref_no = generatePropertyRefNo();
            $listing->title = $request->title;
            $listing->for = $request->for;
            $listing->bed_id = $request->bed_id;
            $listing->bath_id = $request->bath_id;
            $listing->property_category_id = $request->property_category_id;

            if ($request->listing_location_type == "community_property") {
                $communityProperty = CommunityProperty::find($request->listing_location_id);
                $community_property_id = $communityProperty->id ?? null;
                $sub_community_id = $communityProperty->sub_community_id ?? null;
                $community_id = $communityProperty->community_id ?? null;
            } else {

                $subCommunity = SubCommunity::find($request->listing_location_id);
                $community_property_id = null;
                $sub_community_id = $subCommunity->id ?? null;
                $community_id = $subCommunity->community_id ?? null;
            }

            $listing->sub_community_id = $sub_community_id;
            $listing->community_id = $community_id;
            $listing->community_property_id = $community_property_id;

            $listing->publish_status = 'DRAFT';
            $listing->unit_no = $request->unit_no;
            $listing->owner_id = $contact_id;
            $listing->agent_id = $request->agent_id;
            $listing->created_by_id = auth()->id();
            $listing->withoutApproval()->save();

            $listingLead->property_id = $listing->id;
            $listingLead->contact_id = $contact_id;
            $listingLead->agent_id = $request->agent_id;
            $listingLead->status_id = 5;
            $listingLead->sub_community_id = $sub_community_id;
            $listingLead->community_id = $community_id;
            $listingLead->community_property_id = $community_property_id;

            $listingLead->for = $request->for;
            $listingLead->bed_id = $request->bed_id;
            $listingLead->bath_id = $request->bath_id;
            $listingLead->property_category_id = $request->property_category_id;
            $original = $listingLead->getOriginal() ?? [];
            $dirty = $listingLead->getDirty() ?? [];

            $original = array_intersect_key($original, $dirty);

            $listingLead->save();

            $listing_leads_old_notes = ListingLeadNote::where('listing_lead_id', $request->listing_lead_id)->get();

            $obj = new ListingLeadNote();
            $obj->listing_lead_id = $request->listing_lead_id;
            $obj->note = 'The lead has been successfully converted into an owner. Ref no ' . $listing->ref_no . '.';
            $obj->created_by_id = auth()->id();
            $obj->save();

            $propertyNote = new PropertyNote();
            $propertyNote->property_id = $listing->id;
            $propertyNote->note = 'The listing created using the Listing Lead module. Listing Lead ID: ' . $listingLead->id . '.';
            $propertyNote->created_by_id = auth()->id();
            $propertyNote->save();

            foreach ($listing_leads_old_notes as $old_note) {
                $contactNote = new ContactNote();
                $contactNote->contact_id = $contact_id;
                $contactNote->note = "Note (Migrated from Listing Lead) => " . $old_note->note;
                $contactNote->created_by_id = auth()->id();
                $contactNote->status = 1;
                $contactNote->save();
            }
            DB::commit();
            $arr = ['attributes' => $dirty,
                'old' => $original, 'listing_lead_id' => $listingLead->id];
            activity()
                ->performedOn($listingLead)
                ->causedBy(auth()->user())
                ->withProperties($arr)
                ->event('updated')
                ->log(auth()->user()->first_name . ' has converted this listing lead to contact and created a listing with ref no ' . $listing->ref_no . ' using Mobile Application.');

            // return redirect()->route('admin.listing.index', ['ref_number' => $listing->ref_no]);
            return $this->sendResponse(true, 'Converted to listing', [
                'listing' => $listing,
                'listing_lead' => $listingLead,
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            info($th->getMessage());
            return $this->sendResponse(false, 'Something went wrong!!', ['error' => $th->getMessage()], 500);
            info($th->getMessage());
            return redirect()->back();
        }

    }

    /**
     *
     *
     *
     * Listing Lead Notes
     * @group Listing Leads
     * @authenticated
     * @header Accept application/json
     * @bodyParam id requried for lead id. Example: 12
     * @bodyParam agent_id integer for agent_id. Example: 12
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": 200,
    "code": 200,
    "message": "Data found",
    "data": [
    {
    "id": 329350,
    "listing_lead_id": 269096,
    "note": "Note of the lead",
    "status": 1,
    "created_by_id": 262,
    "updated_by_id": null,
    "created_at": "2024-07-25T10:34:44.000000Z",
    "updated_at": "2024-07-25T10:34:44.000000Z"
    }
    ],
    "page": 1,
    "per_page": 10
    }

     * @response 403 scenario="You are not authorized"
    {
    "status": 403,
    "code": 403,
    "message": "You are not authorized",
    "data": "Aborted",
    "page": 1,
    "per_page": 10
    }

     */

    public function getNotes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'agent_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        $lead = ListingLead::where('id', $request->id)->first();

        if (!$lead) {
            return $this->sendResponse(404, 'Listing Lead not found', [], 404);
        }

        // if($lead->agent_id != $request->agent_id){
        //     return $this->sendResponse(403, 'You are not authorized', 'Aborted', 403);
        // }

        $leadNotes = ListingLeadNote::with('createdBy')->where('listing_lead_id', $request->id)->orderBy('id', 'desc')->get();

        // create resource
        $notes = ListingLeadNoteResource::collection($leadNotes);

        return $this->sendResponse(200, 'Data found', $notes, 200);

    }

    /**
     *
     *
     *
     * Listing Lead Call Logs
     * @group Listing Leads
     * @authenticated
     * @header Accept application/json
     * @bodyParam id requried for lead id. Example: 12
     * @bodyParam model_id string for model_id. Example: listing_lead_id
    Example: abc
     *
     *
     *
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Data found"
    {
    "status": true,
    "code": 200,
    "message": "Call log found",
    "data": [
    {
    "id": 680900,
    "lead_id": null,
    "lead_follow_up_id": null,
    "contact_id": null,
    "contact_follow_up_id": null,
    "client_number": "+971508697773",
    "call_type": "OUTGOING",
    "internal_user_id": null,
    "user_id": 1596,
    "call_started_at": "2024-09-10 17:26:12",
    "call_answered_at": null,
    "call_ended_at": "2024-09-10 17:26:12",
    "call_duration": "0",
    "call_status": null,
    "call_sid": null,
    "call_recording_url": null,
    "call_recording_sid": null,
    "json_dump": "{\"id\": \"389024\", \"user_id\": \"1630\", \"call_type\": \"1\", \"entity_id\": \"1\", \"created_at\": \"2024-09-10 13:26:42+0000\", \"updated_at\": \"2024-09-10 13:26:42+0000\", \"user_email\": \"v.dubrovin@axcapital.ae\", \"call_status\": \"No Answer\", \"caller_name\": \"Unknown\", \"call_duration\": \"0\", \"call_ended_at\": \"2024-09-10 13:26:12+00\", \"client_number\": \"+971508697773\", \"call_recording\": null, \"call_started_at\": \"2024-09-10 13:26:12+00\", \"duration_mismatch\": \"0\", \"formatted_call_duration\": \"00:00:00\"}",
    "created_at": "2024-09-10T13:26:45.000000Z",
    "updated_at": "2024-09-10T13:26:45.000000Z",
    "deleted_at": null,
    "need_to_be_destroyed": 0
    }
    ],
    "page": 1,
    "per_page": 10
    }


     * @response 404 scenario="Call log not found"

    {
    "status": false,
    "code": 404,
    "message": "Call log not found",
    "data": null,
    "page": 1,
    "per_page": 10
    }

     * @response 403 scenario="You are not authorized"
    {
    "status": 403,
    "code": 403,
    "message": "You are not authorized",
    "data": "Aborted",
    "page": 1,
    "per_page": 10
    }
     */

    public function getCallLog(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'id' => 'required',
            'model_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        try {
            $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent", "Telesales", "Telesales Head"]);
            $childrenIds = getUserChildrenIds(auth()->id());
            if ($request->model_id == 'listing_lead_id') {

                $log = CallLog::whereHas(
                    'listingLead',
                    function ($q) use ($request) {
                        $q->where('listing_lead_id', $request->id);
                    }
                )->when($notAdmin, function ($q) use ($childrenIds) {
                    $q->whereIn('user_id', $childrenIds);
                })->get();

            } else {
                if (\Auth::check()) {

                    $log = CallLog::where($request->model_id, $request->id)->when($notAdmin, function ($q) use ($childrenIds) {
                        $q->whereIn('user_id', $childrenIds);
                    })->get();
                }
            }
            if (count($log) > 0) {
                return $this->sendResponse(true, 'Call log found', $log, 200);

            } else {
                return $this->sendResponse(false, 'Call log not found', null, 404);
            }
        } catch (\Exception $exception) {
            return $this->sendResponse(false, 'Something went wrong.', $exception->getMessage(), 404);
        }
    }

    /**
     *
     *
     *
     * Listing Lead reason
     * @group Listing Leads
     * @authenticated
     * @header Accept application/json
     *
     * @bodyParam id requried for lead id. Example: 12
     * @bodyParam unsuccessful_note string for message. Example: abc
    Example: abc
     * @header Accept application/json

     *
     * @return \Illuminate\Http\JsonResponse
     * @response 200 scenario="Listing Lead reason saved."
    {
    "status": true,
    "code": 200,
    "message": "Listing lead marked as unsuccessful.",
    "data": null,
    "page": 1,
    "per_page": 10
    }
     *}
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @response 500 scenario="Something Went Wring"
     * {
    "status": false,
    "code": 200,
    "message": "Something Went Wring",
    "data": []
    }
     */

    public function saveReason(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'unsuccessful_note' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendResponse(false, 'Validation Error', $validator->errors(), 422);
        }

        try {
            $lead = ListingLead::find($request->id);
            $lead->status_id = 6;
            $lead->updated_by_id = auth()->id();
            $lead->save();

            $listingLeadNote = new ListingLeadNote();
            $listingLeadNote->listing_lead_id = $lead->id;
            $listingLeadNote->note = 'The Listing lead has been marked as unsuccessful. Reason: ' . $request->unsuccessful_note;
            $listingLeadNote->created_by_id = auth()->id();
            $listingLeadNote->status = 1;
            $listingLeadNote->save();

            return $this->sendResponse(true, 'Listing lead marked as unsuccessful.', null, 200);

        } catch (\Exception $exception) {
            return $this->sendResponse(false, 'Something Went Wrong.', null, 500);
            return json_encode($result);
        }
    }
}
