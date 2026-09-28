<?php

use App\Models\Approval;
use App\Models\Bath;
use App\Models\Bed;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Contact;
use App\Models\ContactMedia;
use App\Models\ContactNote;
use App\Models\TelegramNotification;
use App\Models\Developer;
use App\Models\DeveloperProject;
use App\Models\Event;
use App\Models\GeneralSetting;
use App\Models\Group;
use App\Models\Lead;
use App\Models\LeadMedia;
use App\Models\LeadNote;
use App\Models\LeadStatus;
use App\Models\ListingLead;
use App\Models\ListingLeadNote;
use App\Models\MobileNotification;
use App\Models\ModelUpdate;
use App\Models\OffplanProperty;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\PropertyMedia;
use App\Models\PropertyNote;
use App\Models\Shortlink;
use App\Models\Source;
use App\Models\SubCommunity;
use App\Models\User;
use App\Models\UserFcm;
use App\Models\UserMeta;
use App\Models\UserOAuthToken;
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;
//use Propaganistas\LaravelPhone\PhoneNumber;
use Carbon\Carbon;
use Google\Client as Google_Client;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
// use Google_Client;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Spatie\Activitylog\Models\Activity;
function getApp($key)
{
    $arr = [
        'name' => 'AX CAPITAL - Secondary CRM',
        'website' => env('APP_WEBSITE'),
    ];
    return $arr[$key] ?? null;
}
function createMeta($table, $recordId, $metaDatas)
{
    try {
        $tableParts = explode('_', $table);
        $recordType = reset($tableParts);
        foreach ($metaDatas as $metaData) {
            $recordIdColumn = $recordType . '_id';
            DB::table($table)->updateOrInsert(
                [
                    "$recordIdColumn" => $recordId,
                    'meta_key' => $metaData['meta_key'],
                ],
                [
                    'meta_value' => $metaData['meta_value'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        return true;
    } catch (\Exception $e) {
        return false;
    }
}

function slugify($text, string $divider = '-')
{
    // replace non letter or digits by divider
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, $divider);

    // remove duplicate divider
    $text = preg_replace('~-+~', $divider, $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }

    return $text;
}

function getAvailableModels()
{
    $models = [];
    $modelsPath = app_path('Models');
    $modelFiles = File::allFiles($modelsPath);
    foreach ($modelFiles as $modelFile) {
        $models[] = "App\\Models\\" . $modelFile->getFilenameWithoutExtension();
    }
    return $models;
}

function getBootstrapColors()
{
    return [
        "primary",
        "secondary",
        "success",
        "info",
        "warning",
        "danger",
        "dark",
        "light",
    ];
}
function getNotAdmin()
{
    $notAdminRoles = getNotAdminRoles();
    $notAdmin = auth()->user()->hasRole($notAdminRoles);
    return $notAdmin;
}
function getNotAdminRoles()
{
    $notAdminRoles = array("Sales Director", "Team Leader", "Line Manager", "Agent");
    return $notAdminRoles;
}
function renderTreeView()
{
    $allUsers = \App\Models\User::get();
    $rootUsers = $allUsers->whereNull("parent_id");
    $data = [];

    foreach ($rootUsers as $rootUser) {
        $data[] = [

        ];
    }

    return $data;
}

function back_generatePropertyRefNo($increaseBy = 1, $rent = true)
{
    $lastId = \App\Models\Property::orderByDesc("ref_no")->withTrashed()->first()->ref_no ?? null;
    $lastId = ltrim(substr($lastId, -7), '0');
    if (empty($lastId)) {
        $lastId = 0;
    }
    $refNo = 'axc' . '-' . str_pad($lastId + $increaseBy, 7, '0', STR_PAD_LEFT);
    if (\App\Models\Property::where('ref_no', $refNo)->exists()) {
        return generatePropertyRefNo($increaseBy + 1, $rent);
    }
    return $refNo;
}

function generatePropertyRefNo($increaseBy = 1, $rent = true)
{
    do {
        $lastId = \App\Models\Property::orderByDesc("ref_no")->withTrashed()->first()->ref_no ?? null;
        // dd($lastId);
        if ($lastId && preg_match('/(.*?)-r\d+$/', $lastId, $matches)) {
            $lastId = $matches[1]; // Keep everything before the suffix '-r1', '-r2', etc.
        }

        $lastId = ltrim(substr($lastId, -7), '0');
        if (empty($lastId)) {
            $lastId = 0;
        }



        // dd($lastId);
        $refNo = 'axc' . '-' . str_pad($lastId + $increaseBy, 7, '0', STR_PAD_LEFT);
        $exists = \App\Models\Property::where('ref_no', $refNo)->exists();

        // dd($refNo);
        if (!$exists) {
            return $refNo;
        }
        $increaseBy++;
    } while (true);
}

function generateTempPropertyRefNo($increaseBy = 1, $rent = true)
{
    do {
        $lastId = \App\Models\TempProperty::orderByDesc("ref_no")->withTrashed()->first()->ref_no ?? null;
        // dd($lastId);
        if ($lastId && preg_match('/(.*?)-r\d+$/', $lastId, $matches)) {
            $lastId = $matches[1]; // Keep everything before the suffix '-r1', '-r2', etc.
        }

        $lastId = ltrim(substr($lastId, -7), '0');
        if (empty($lastId)) {
            $lastId = 0;
        }



        // dd($lastId);
        $refNo = 'axc' . '-' . str_pad($lastId + $increaseBy, 7, '0', STR_PAD_LEFT);
        $exists = \App\Models\TempProperty::where('ref_no', $refNo)->exists();

        // dd($refNo);
        if (!$exists) {
            return $refNo;
        }
        $increaseBy++;
    } while (true);
}

function back1_generatePropertyRefNo($increaseBy = 1, $rent = true)
{
    return DB::transaction(function () use ($increaseBy) {
        do {
            $lastId = \App\Models\Property::orderByDesc("ref_no")->withTrashed()->first()->ref_no ?? null;
            $lastId = ltrim(substr($lastId, -7), '0');
            if (empty($lastId)) {
                $lastId = 0;
            }
            $refNo = 'axc' . '-' . str_pad($lastId + $increaseBy, 7, '0', STR_PAD_LEFT);
            $exists = \App\Models\Property::where('ref_no', $refNo)->exists();

            if (!$exists) {
                // Create a new property record with the generated refNo
                $property = new \App\Models\Property();
                $property->ref_no = $refNo;
                $property->save();

                return $refNo;
            }
            $increaseBy++;
        } while (true);
    });
}

function getPropertyCriteria($ref_no)
{
    $data = [];

    if ($ref_no) {
        $property = Property::where('ref_no', $ref_no)
            ->with('community', 'subCommunity', 'communityProperty', 'price', 'bed', 'bath')
            ->firstOrFail();
        // code for get min max price based on relation return 2 prices one a little less and one a little more like 10% less and 10% more if no prices then return 0

        $minPrice = $property->price?->price - ($property->price?->price * 0.1);
        $maxPrice = $property->price?->price + ($property->price?->price * 0.1);

        $minPrice = round($minPrice, -3);
        $maxPrice = round($maxPrice, -3);

        $data['community'] = optional($property->community)->name;
        $data['community_id'] = optional($property->community)->id;
        $data['subCommunity'] = optional($property->subCommunity)->name;
        $data['subCommunity_id'] = optional($property->subCommunity)->id;
        $data['communityProperty'] = optional($property->communityProperty)->id;
        $data['price'] = optional($property->price)->price;
        $data['minPrice'] = $minPrice;
        $data['maxPrice'] = $maxPrice;
        $data['for'] = $property->for;
        $data['bed'] = optional($property->bed)->name;
        $data['bed_id'] = optional($property->bed)->id;
        $data['bath'] = optional($property->bath)->name;
        $data['bath_id'] = optional($property->bath)->id;
        $data['furnished'] = $property->furnished;
        $data['view'] = $property->view;
    }

    return $data;
}

function timeGreetings()
{
    date_default_timezone_set('Asia/Dubai');
    $h = date('G');

    if ($h >= 5 && $h <= 12) {
        return "Good Morning";
    } else {
        if ($h >= 12 && $h <= 15) {
            return "Good Afternoon";
        } else {
            return "Good Evening";
        }
    }
}

function getEmailSettings($title)
{
    $cc = [];
    $bcc = [];
    $replyTo = [];
    $roleIds = [];
    $emailSettings = \App\Models\EmailSetting::where("title", $title)->with("roles")->get();
    foreach ($emailSettings as $emailSetting) {
        if (!empty($emailSetting->cc)) {
            $cc = array_merge($cc, $emailSetting->cc);
        }
        if (!empty($emailSetting->bcc)) {
            $bcc = array_merge($bcc, $emailSetting->bcc);
        }
        if (!empty($emailSetting->reply_to)) {
            $replyTo[] = $emailSetting->reply_to;
        }
        foreach ($emailSetting->roles ?? [] as $role) {
            $roleIds[] = $role->id;
        }
    }

    return [
        "cc" => $cc,
        "bcc" => $bcc,
        "reply_to" => $replyTo,
        "roles" => $roleIds,
    ];
}

function getAdminEmail()
{
    return 'l.gapunuan@axcapital.ae';
}

function getUserLastActiveTime($user)
{
    $approval = Approval::whereJsonContains('new_data', ['updated_by_id' => $user->id])->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->first();
    $lead_created = Lead::where('created_by_id', $user->id)->orderBy('updated_at', 'desc')->orderBy('created_at', 'desc')->first();
    $lead_updated = Lead::where('updated_by_id', $user->id)->orWhere('created_by_id', $user->id)->orderBy('updated_at', 'desc')->orderBy('created_at', 'desc')->first();
    $contact = Contact::where('updated_by_id', $user->id)->orWhere('created_by_id', $user->id)->orderBy('updated_at', 'desc')->orderBy('created_at', 'desc')->first();
    $lead_note = LeadNote::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
    $property_created = Property::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
    $property_updated = Property::where('updated_by_id', $user->id)->orderBy('updated_at', 'desc')->first();
    $property_note = PropertyNote::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
    $listingLeadCreated = ListingLead::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
    $listingLeadUpdated = ListingLead::where('updated_by_id', $user->id)->orderBy('updated_at', 'desc')->first();
    $listingLead_note = ListingLeadNote::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
    $events = Event::where('created_by_id', $user->id)->orWhere('updated_by_id', $user->id)->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->first();
    $contact_media = ContactMedia::where('created_by_id', $user->id)->orWhere('updated_by_id', $user->id)->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->first();
    $lead_media = LeadMedia::where('created_by_id', $user->id)->orWhere('updated_by_id', $user->id)->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->first();
    $property_media = PropertyMedia::where('created_by_id', $user->id)->orWhere('updated_by_id', $user->id)->orderBy('created_at', 'desc')->orderBy('updated_at', 'desc')->first();
    $contact_note = ContactNote::where('created_by_id', $user->id)->orderBy('created_at', 'desc')->first();
    $model_updates = ModelUpdate::where('updated_by_id', $user->id)->orderBy('created_at', 'desc')->first();
    $activity_log = Activity::where('causer_id', $user->id)->where('causer_type', User::class)->orderBy('created_at', 'desc')->first();

    $last_time = collect([
        $user?->last_login ?? null,
        $contact?->updated_at ?? null,
        $contact?->created_at ?? null,
        $lead_updated?->updated_at ?? null,
        $lead_created?->created_at ?? null,
        $lead_note?->created_at ?? null,
        $property_updated?->updated_at ?? null,
        $property_created?->created_at ?? null,
        $property_note?->created_at ?? null,
        $listingLeadUpdated?->updated_at ?? null,
        $listingLeadCreated?->created_at ?? null,
        $listingLead_note?->created_at ?? null,
        $approval?->updated_at ?? null,
        $approval?->created_at ?? null,
        $events?->updated_at ?? null,
        $events?->created_at ?? null,
        $contact_media?->updated_at ?? null,
        $contact_media?->created_at ?? null,
        $lead_media?->updated_at ?? null,
        $lead_media?->created_at ?? null,
        $property_media?->updated_at ?? null,
        $property_media?->created_at ?? null,
        $contact_note?->created_at ?? null,
        $model_updates?->created_at ?? null,
        $activity_log?->created_at ?? null,
    ])->filter()->max();
    return $last_time;
}

function renderUserChildren($user, &$arr = [])
{
    if ($user != null) {
        if ($user->children->isNotEmpty()) {
            foreach ($user->children ?? [] as $child) {
                if (!in_array($child->id, $arr)) {
                    $arr[] = $child->id;
                    $arr = renderUserChildren($child, $arr);
                }
            }
        }
    }
    return $arr;
}
function renderUserParent($user, &$arr = [], $includeSelf = false)
{
    if ($user != null) {
        // Include the user's own ID if $includeSelf is true
        if ($includeSelf) {
            $arr[] = $user->id;
        }
        // Collect parent IDs
        if ($user->parent != null) {
            // Recursively collect parent IDs
            if (!in_array($user->parent->id, $arr)) {
                $arr[] = $user->parent->id;
                $arr = renderUserParent($user->parent, $arr, false);
            }
        }
    }
    return $arr;
}

function getUserParentOnly($userId)
{

    $user = \App\Models\User::find($userId);
    $arr = [];
    renderUserParent($user, $arr);

    // Remove the 3 from this $arr
    $key = array_search("196", $arr);
    if ($key !== false) {
        unset($arr[$key]);
    }
    return $arr;

}

function getUserChildrenIds($userId, $includeUserId = true)
{

    if (auth()?->user()?->hasRole('Super Admin')) {

        return \App\Models\User::pluck('id')->toArray();
    } else {
        $user = \App\Models\User::find($userId);
        $arr = [];
        if ($includeUserId) {
            $arr[] = $userId;
        }
        renderUserChildren($user, $arr);
        return $arr;
    }
}

function childcount($user_id)
{
    $user = \App\Models\User::find($user_id);
    $arr = [];
    renderActiveUserChildren($user, $arr);
    return count($arr);
}

function renderActiveUserChildren($user, &$arr = [])
{
    if ($user != null) {
        if ($user->activechildren->isNotEmpty()) {
            foreach ($user->activechildren ?? [] as $child) {
                if (!in_array($child->id, $arr)) {
                    $arr[] = $child->id;
                    $arr = renderActiveUserChildren($child, $arr);
                }
            }
        }
    }
    return $arr;
}

function getUserRoles($userId)
{
    $user = \App\Models\User::find($userId);
    return $user->roles->pluck('name')->implode(',');
}

function getUserChildrenIdsByID($userId, $includeUserId = true)
{
    $user = \App\Models\User::find($userId);
    $arr = [];
    if ($includeUserId) {
        $arr[] = $userId;
    }
    renderUserChildren($user, $arr);
    return $arr;

}

function getUserChildrenIdsOnly($userId)
{
    $user = \App\Models\User::find($userId);
    $arr = [];
    renderUserChildren($user, $arr);
    return $arr;

}

function formatDate($date, $format = "d-m-Y")
{
    return \Illuminate\Support\Carbon::parse($date)->format($format);
}

function getSecondsTohumanReadable($seconds, $includeDays = true)
{
    if ($seconds == 0 || $seconds == null || $seconds == "") {
        return "-";
    }
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    if ($includeDays) {
        return $dtF->diff($dtT)->format('%a day(s), %h hr(s), %i min(s) and %s sec(s)');
    } else {
        return $dtF->diff($dtT)->format('%h hr(s), %i min(s) and %s sec(s)');
    }
}

function getSecondsTohumanReadableReports($seconds, $includeDays = true)
{
    if ($seconds == 0 || $seconds == null || $seconds == "") {
        return "-";
    }
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    if ($includeDays) {
        return $dtF->diff($dtT)->format('%a day(s): %h:%i:%s');
    } else {
        return $dtF->diff($dtT)->format('%h:%i:%s');
    }
}

function getSecondsTohumanReadableReportsTimeBase($seconds, $includeDays = true)
{
    if ($seconds == 0 || $seconds == null || $seconds == "") {
        return "-";
    }
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    if ($includeDays) {
        return $dtF->diff($dtT)->format('%a day(s): %h:%i:%s');
    } else {

        if ($dtF->diff($dtT)->format('%h') <= 1) {
            return "<span class='good-time'>" . $dtF->diff($dtT)->format('%h:%i:%s') . "</span>";
        } else if ($dtF->diff($dtT)->format('%h') > 1 && $dtF->diff($dtT)->format('%h') <= 3) {
            return "<span class='better-time'>" . $dtF->diff($dtT)->format('%h:%i:%s') . "</span>";
        } else if ($dtF->diff($dtT)->format('%h') > 3 && $dtF->diff($dtT)->format('%h') <= 5) {
            return "<span class='avg-time'>" . $dtF->diff($dtT)->format('%h:%i:%s') . "</span>";
        } else {
            return "<span class='bad-time'>" . $dtF->diff($dtT)->format('%h:%i:%s') . "</span>";
        }

    }
}
function formatTimeReport($total_hours)
{
    // Calculate days, hours, and minutes
    $days = floor($total_hours / 24);
    $remaining_hours = $total_hours % 24;
    $hours = floor($remaining_hours);

    // Construct the formatted string
    $formatted_time = '';
    if ($days > 0) {
        $formatted_time .= $days . "d, ";
    }
    if ($hours > 0) {
        $formatted_time .= $hours . "h, ";
    }

    return $formatted_time;
}
function getSingleUnitTimeDiff($timestamp)
{
    $lastLogin = Carbon::parse($timestamp);
    $currentTime = Carbon::now();

    $hoursDifference = $lastLogin->diffInHours($currentTime);

// Format the output based on the difference in hours
    if ($hoursDifference >= 24) {
        // If the difference is 24 hours or more, show only the number of days
        $output = $lastLogin->diffInDays($currentTime) . 'd';
    } elseif ($hoursDifference >= 1) {
        // If the difference is between 1 and 23 hours, show only the number of hours
        $output = $hoursDifference . 'h';
    } else {
        // If the difference is less than 1 hour, show 'Less than 1 hour' or '1m' for minutes
        $output = ($lastLogin->diffInMinutes($currentTime) < 1) ? '1m' : $lastLogin->diffInMinutes($currentTime) . 'm';
    }

    return $output;
}
function timeDiffBetween2($first, $second, $includeDays = false)
{
    $firstTimestamp = Carbon::parse($first);
    $currentTime = Carbon::parse($second);

    $hoursDifference = $firstTimestamp->diffInSeconds($currentTime);

    $output = getSecondsTohumanReadableReports($hoursDifference, $includeDays);

    return $output;
}

function getIntensityOfNumber($number)
{
    if ($number <= 1) {
        return "<span class='good-time'><b>" . $number . "</b></span>";
    } elseif ($number <= 2 && $number <= 3) {
        return "<span class='avg-time'><b>" . $number . "</b></span>";
    } else {
        return "<span class='bad-time'><b>" . $number . "</b></span>";
    }
}

function getPropertyExportColumns()
{
    return [
        "id" => "id",
        "ref_no" => "Reference No",
        "title" => "Title",
        "description" => "Description",
        "for" => "Category",
        "community" => "Community",
        "sub_community" => "Sub Community",
        "community_property" => "Community Property",
        "property_category" => "Type",
        "price" => "Price",
        "bed" => "Bed",
        "bath" => "Bath",
        "unit_no" => "Unit No",
        "built_up_area" => "BUA",
        "plot_size" => "Plot Size",
        "portals" => "Portals",
        "furnished" => "Furnished",
        "featured" => "Featured",
        "exclusive" => "Exclusive",
        "publish_status" => "Publish Status",
        "agent" => "Agent",
        'manager' => 'Manager',
        "owner" => "Owner",
        "title_deed_text" => "Title Deed",
        "rera_permit_number" => "Rera Permit Number",
        "rera_permit_status" => "Rera Permit Status",
        "rera_expiry_date" => "Rera Permit Expiry Date",
        "updated_by" => "Updated By",
        "recentNote" => "Latest Note",
        "contactNo" => "Contact No",
        "contractNo" => "Contract No",
        "completion_status" => "Completion Status",
        "updated_at" => "Updated at",
        "created_at" => "Created at",
        "published_date" => "Published date",
        "created_by" => "Created by",

    ];
}

function getLeadsExportColumns()
{
    return [
        "id" => "id",
        "agent_id" => "Agent",
        "manager_id" => "Manager",
        'for' => 'Purpose',
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email',
        'mobile' => 'Mobile',
        'type_id' => 'Lead Type',
        'source_id' => 'Lead Source',
        'lead_medium_id' => 'Lead Medium',
        'lead_status_id' => 'Lead Status',
        'created_at' => 'Created at',
        'updated_at' => 'Updated at',

    ];
}

function getListingLeadsExportColumns()
{

    return [
        "id" => "id",
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email',
        'mobile' => 'Mobile',
        'bed_id' => 'Bed',
        'bath_id' => 'Bath',
        'for' => 'For',
        'unit_no' => 'Unit No',
        'type_id' => 'Type',
        'source_id' => 'Source',
        'medium_id' => 'Medium',
        'location_id' => 'Location',
        'property_category_id' => 'Property Category',
        'telesales_id' => 'Telesales',
        'community_property_id' => 'Community Property',
        'community_id' => 'Community',
        'sub_community_id' => 'Sub Community',
        'location_type' => 'Location Type',
        'recording_file' => 'Recording File',
        'note' => 'Note',
        'status_id' => 'Status',
        'contact_id' => 'Contact',
        'property_id' => 'Property',
    ];
}

function getUserExportColumns()
{
    return [
        "id" => "id",
        "name" => "Full Name",
        "first_name" => "First Name",
        "last_name" => "Last Name",
        "email" => "Email",
        "mobile" => "Mobile",
        "telegram_username" => "Telegram Username",
        "telegram_chatid" => "Telegram Chat id",
        "bio" => "Bio",
        "license_no" => "License no",
        "position" => "Position",
        "country" => "Country",
        "state" => "State",
        "image" => "Photo",
        "thumbnail" => "Thumbnail",
        "status" => "Status",
        'role' => 'Role',
        'parent' => 'Parent',
    ];
}
function propertyColumnsReadable($json_data)
{

    $data = [];
    $val = null;
    $var = null;
    if ($json_data != null) {
        foreach ($json_data as $key => $value) {
            $readable_key = $key;
            if ($key == 'ref_no') {
                $readable_key = $key == 'ref_no' ? 'Reference No.' : $key;
                $val = $value;
            } elseif ($key == 'community_id') {
                $readable_key = 'Community';
                $val = Community::where('id', $value)->pluck('path_name')->first() ?? null;
            } elseif ($key == 'community_property_id') {
                $readable_key = 'Community Property';
                $val = CommunityProperty::where('id', $value)->pluck('path_name')->first() ?? '';
            } elseif ($key == 'sub_community_id') {
                $readable_key = 'Sub Community';
                $val = SubCommunity::where('id', $value)->pluck('path_name')->first() ?? null;
            } elseif ($key == 'owner_id') {
                $readable_key = 'Owner';
                $var = Contact::where('id', $value)->select('title', 'first_name', 'last_name')->first() ?? null;
                $val = $var?->title . ' ' . $var?->first_name . ' ' . $var?->last_name;
            } elseif ($key == 'agent_id') {
                $readable_key = 'Agent';
                $val = User::where('id', $value)->pluck('name')->first() ?? null;
            } elseif ($key == 'price') {
                $readable_key = 'Price';
                $val = formatPriceToInt($value);
            } elseif ($key == 'property_category_id') {
                $readable_key = 'Type';
                $val = PropertyCategory::where('id', $value)->pluck('title')->first() ?? '';
            } elseif ($key == 'bath_id') {
                $readable_key = 'Baths';
                $val = Bath::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'bed_id') {
                $readable_key = 'Beds';
                $val = Bed::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'updated_by_id') {
                $readable_key = 'Updated By';
                $val = User::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'updated_at') {
                $readable_key = 'Updated at';
                $val = date('d/m/Y H:i', strtotime($value));
            } elseif ($key == 'created_at') {
                $readable_key = 'Created at';
                $val = date('d/m/Y H:i', strtotime($value));
            } elseif ($key == 'created_by_id') {
                $readable_key = 'Created by';
                $val = User::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'contract_start') {
                $readable_key = 'Contract Start';
                $val = date('d/F/Y', strtotime($value));
            } elseif ($key == 'contract_end') {
                $readable_key = 'Contract end';
                $val = date('d/F/Y', strtotime($value));
            } elseif ($key == 'exclusive_start') {
                $readable_key = 'Exclusive start';
                $val = date('d/F/Y', strtotime($value));
            } elseif ($key == 'exclusive_end') {
                $readable_key = 'Exclusive end';
                $val = date('d/F/Y', strtotime($value));
            } elseif ($key == 'view') {
                $readable_key = 'View';
                $val = json_decode($value) ?? '';
            } elseif ($key == 'villa_type') {
                $readable_key = 'Villa type';
                $val = json_decode($value) ?? '';
            } elseif ($key == 'property_id') {
                continue;
            } elseif ($key == 'id') {
                continue;
            } elseif ($key == 'order') {
                continue;
            } elseif ($key == 'temp_id') {
                continue;
            } elseif ($key == 'thumbnail') {
                continue;
            } else {
                $readable_key = $key;
                $val = null;
            }

            $data[ucwords(str_replace("_", " ", $readable_key))] = $val ?? $value;

        }
        return $data;
    } else {
        return [];
    }

}
function leadColumnsReadable($json_data)
{
    $data = [];
    $val = null;
    $var = null;
    if ($json_data != null) {
        foreach ($json_data as $key => $value) {

            if ($key == 'for') {
                $readable_key = $key == 'for' ? 'Lead for' : $key;
                $val = $value;
            } elseif ($key == 'agent_id') {
                $readable_key = 'Agent';
                $val = User::where('id', $value)->pluck('name')->first() ?? null;
            } elseif ($key == 'updated_at') {
                $readable_key = 'Updated at';
                $val = globalDateTime($value);
            } elseif ($key == 'created_at') {
                $readable_key = 'Created at';
                $val = globalDateTime($value);
            } elseif ($key == 'updated_by_id') {
                $readable_key = 'Updated By';
                $val = User::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'created_by_id') {
                $readable_key = 'Created by';
                $val = User::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'property_id') {
                $readable_key = 'Property';
                $val = \App\Models\Property::where('id', $value)->pluck('ref_no')->first() ?? '';
            } elseif ($key == 'nationality_id') {
                $readable_key = 'Nationality';
                $val = \App\Models\Country::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'lead_type_id') {
                $readable_key = 'Lead Type';
                $val = \App\Models\LeadType::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'lead_source_id') {
                $readable_key = 'Lead Source';
                $val = \App\Models\Source::where('id', $value)->pluck('name')->first() ?? '';
            }elseif ($key == 'source_id') {
                $readable_key = 'Lead Source';
                $val = \App\Models\Source::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'lead_status_id') {
                $readable_key = 'Lead status';
                $val = \App\Models\LeadStatus::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'lead_sub_status_id') {
                $readable_key = 'Lead sub status';
                $val = \App\Models\LeadStatus::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'max_bath_id') {
                $readable_key = 'Max Baths';
                $val = Bath::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'min_bath_id') {
                $readable_key = 'Min Baths';
                $val = Bath::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'max_bed_id') {
                $readable_key = 'Max Beds';
                $val = Bed::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'min_bed_id') {
                $readable_key = 'Min Beds';
                $val = Bed::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'lead_id') {
                continue;
            } elseif ($key == 'id') {
                continue;
            } elseif ($key == 'lead_follow_up_id') {
                continue;
            } elseif ($key == 'due_date') {
                $readable_key = 'Due date';
                $val = globalDate($value);
            } elseif ($key == 'user_id') {
                $readable_key = 'Agent';
                $val = User::where('id', $value)->pluck('name')->first() ?? null;
            } elseif ($key == 'community_ids') { // related to lead criteria
                $readable_key = 'Communities';
                $val = Community::whereIn('id', $value)->pluck('name') ?? null;
            } elseif ($key == 'sub_community_ids') {
                $readable_key = 'Sub Communities';
                $val = SubCommunity::whereIn('id', $value)->pluck('name') ?? null;
            } elseif ($key == 'community_properties_ids') {
                $readable_key = 'Community Properties';
                $val = CommunityProperty::whereIn('id', $value)->pluck('name') ?? null;
            } else {
                $readable_key = $key;
                $val = null;
            }
//        elseif ($key == 'follow_up_date') {
//            $readable_key = 'Follow up date';
//            $val = $val != null ? date('d/m/Y',strtotime($value)) : null;
//        }

            $data[ucwords(str_replace("_", " ", $readable_key))] = $val ?? $value;

        }
        return $data;
    } else {
        return [];
    }
}
function contactColumnsReadable($json_data)
{
    $data = [];
    $val = null;
    $var = null;
    if ($json_data != null) {
        foreach ($json_data as $key => $value) {

            if ($key == 'for') {
                $readable_key = $key == 'for' ? 'Lead for' : $key;
                $val = $value;
            } elseif ($key == 'agent_id') {
                $readable_key = 'Agent';
                $val = User::where('id', $value)->pluck('name')->first() ?? null;
            } elseif ($key == 'updated_at') {
                $readable_key = 'Updated at';
                $val = globalDateTime($value);
            } elseif ($key == 'created_at') {
                $readable_key = 'Created at';
                $val = globalDateTime($value);
            } elseif ($key == 'updated_by_id') {
                $readable_key = 'Updated By';
                $val = User::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'nationality_id') {
                $readable_key = 'Nationality';
                $val = \App\Models\Country::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'contact_type_id') {
                $readable_key = 'Contact Type';
                $val = \App\Models\ContactType::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'contact_source_id') {
                $readable_key = 'Contact Source';
                $val = \App\Models\ContactSource::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'contact_status_id') {
                $readable_key = 'Contact status';
                $val = \App\Models\ContactStatus::where('id', $value)->pluck('name')->first() ?? '';
            } else {
                $readable_key = $key;
                $val = null;
            }
//        elseif ($key == 'follow_up_date') {
//            $readable_key = 'Follow up date';
//            $val = $val != null ? date('d/m/Y',strtotime($value)) : null;
//        }

            $data[ucwords(str_replace("_", " ", $readable_key))] = $val ?? $value;

        }
        return $data;
    } else {
        return [];
    }
}
function shootReqColumnsReadable($json_data)
{
    $data = [];
    $val = null;
    $var = null;
    if ($json_data != null) {
        foreach ($json_data as $key => $value) {

            if ($key == 'agent_id') {
                $readable_key = 'Agent';
                $val = User::where('id', $value)->pluck('name')->first() ?? null;
            } elseif ($key == 'updated_at') {
                $readable_key = 'Updated at';
                $val = date('d/m/Y H:m', strtotime($value));
            } elseif ($key == 'created_at') {
                $readable_key = 'Created at';
                $val = date('d/m/Y H:m', strtotime($value));
            } elseif ($key == 'updated_by_id') {
                $readable_key = 'Updated By';
                $val = User::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'created_by_id') {
                $readable_key = 'Created by';
                $val = User::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'property_id') {
                $readable_key = 'Property';
                $val = \App\Models\Property::where('id', $value)->pluck('ref_no')->first() ?? '';
            } elseif ($key == 'cleaned') {
                $readable_key = 'Cleaned';
                $val = $value == 1 ? 'Yes' : 'No';
            } elseif ($key == 'dewa') {
                $readable_key = 'DEWA';
                $val = $value == 1 ? 'Yes' : 'No';
            } elseif ($key == 'furnished') {
                $readable_key = 'Furnished';
                $val = $value == 1 ? 'Yes' : 'No';
            } elseif ($key == 'is_luxury') {
                $readable_key = 'Luxury';
                $val = $value == 1 ? 'Yes' : 'No';
            } elseif ($key == 'requested_tags') {
                $readable_key = 'Requested Tags';
                $val = json_decode($value);
            } elseif ($key == 'lead_status_id') {
                $readable_key = 'Lead status';
                $val = \App\Models\LeadStatus::where('id', $value)->pluck('name')->first() ?? '';
            } elseif ($key == 'id') {
                continue;
            } elseif ($key == 'preferred_time') {
                $readable_key = 'Preferred Time';
                $val = date('d/m/Y h:i A', strtotime($value));
            } elseif ($key == 'approved_at') {
                $readable_key = 'Approved At';
                $val = date('d/m/Y h:i A', strtotime($value));
            } elseif ($key == 'rejected_at') {
                $readable_key = 'Rejected At';
                $val = date('d/m/Y h:i A', strtotime($value));
            } elseif ($key == 'agent_id') {
                $readable_key = 'Agent';
                $val = User::where('id', $value)->pluck('name')->first() ?? null;
            } elseif ($key == 'requested_by_id') {
                $readable_key = 'Requested By';
                $val = User::where('id', $value)->pluck('name')->first() ?? null;
            } elseif ($key == 'assigned_to_id') {
                $readable_key = 'Assigned To';
                $val = User::where('id', $value)->pluck('name')->first() ?? null;
            } elseif ($key == 'approved_by_id') {
                $readable_key = 'Approved By';
                $val = User::where('id', $value)->pluck('name')->first() ?? null;
            } elseif ($key == 'rejected_by_id') {
                $readable_key = 'Rejected By';
                $val = User::where('id', $value)->pluck('name')->first() ?? null;
            } else {
                $readable_key = $key;
                $val = null;
            }
//        elseif ($key == 'follow_up_date') {
//            $readable_key = 'Follow up date';
//            $val = $val != null ? date('d/m/Y',strtotime($value)) : null;
//        }

            $data[ucwords(str_replace("_", " ", $readable_key))] = $val ?? $value;

        }
        return $data;
    } else {
        return [];
    }
}
function numbers_only($value)
{
    return preg_replace("/[^0-9]/", "", $value);
}

function imageToBase64($url)
{
    if ($url == null) {
        return null;
    }
    $image = file_get_contents($url);
    if ($image !== false) {
        return 'data:image/jpg;base64,' . base64_encode($image);

    }
}

function delete_general($id, $model)
{

    $id = $id;
    $model = $model;
    try
    {
        $count_relation_data = [];
        $data = $model::find($id);
        $relationships = $data->relationships;

        // $count_relations = $data->with($relationships)->count();
        if ($relationships) {
            foreach ($relationships as $key => $relation) {
                if ($data->$relation()->count() == 0) {
                    continue;

                }
                $count_relation_data[$key] = $data->$relation()->count();
            }

            if (count($count_relation_data) > 0) {
                return Response::json(['error_related_data' => $count_relation_data, 201]);

            }

        }

        $data->delete();

    } catch (\Illuminate\Database\QueryException $e) {
        if ($e->getCode() == "23000") { //23000 is sql code for integrity constraint violation
            // return error to user here
            $message = 'Cannot Delete! Relational Data found';
            return Response::json(['error' => $message, 201]);
        }
    } catch (Exception $e) {
        $message = 'Cannot Delete! An Error occured';
        return Response::json(['error' => $message, 201]);
    }

    $message = 'Deleted Successfully';
    return Response::json(['success' => $message, 200]);
}
function array_match_and_remove($first_array, $second_array)
{
    // this will remove value from first array if it exists in second array

    if (is_array($first_array) && is_array($second_array)) {

        if (count($first_array) > 0 && count($second_array) > 0) {
            foreach ($first_array as $key => $value) {
                if (in_array($value, $second_array)) {
                    unset($first_array[$key]);
                }
            }
            return array_values($first_array);

        } else {
            return $first_array;
        }
    } else {
        return $first_array;
    }
}

function mobileNumberFixer($mobile)
{
    $mobile = intval(preg_replace('/[^0-9]+/', '', $mobile));
    if (Str::length($mobile) < 10) {
        if (preg_match("~^00\d+$~", $mobile)) { //check if starting with double 0
            $mobile = ltrim($mobile, $mobile[1]); //remove double 0
            $mobile = '971' . $mobile;
        } elseif (preg_match("~^(?:0|04|05)\d+$~", $mobile)) { //check if starting with single 0
            $mobile = ltrim($mobile, $mobile[0]); //remove single 0
            $mobile = '971' . $mobile;
        } elseif (preg_match("~^(?:4|5)\d+$~", $mobile)) { //check if starting with 4 or 5
            $mobile = '971' . $mobile;
        } else {
            $mobile = $mobile;
        }
    } elseif (Str::length($mobile) > 10 && preg_match("~^00971\d+$~", $mobile)) {
        $mobile = ltrim($mobile, $mobile[1]); //remove 00
    } else {
        $mobile = $mobile;
    }
    return $mobile;
}
function checkNumberValidity($inputNumber, $countryCode = 'AE')
{
    try {
        $inputNumber = mobileNumberFixer($inputNumber);

        $number = PhoneNumber::parse('+' . $inputNumber);
//        echo $number->getRegionCode(); // GB
//        echo $number->getCountryCode(); // 44
        if ($number->isValidNumber()) {
            return true;
        } else {
            return false;
        }

    } catch (PhoneNumberParseException $e) {

        return false;
    }
}
function getBetweenText($string, $start = "", $end = "")
{
    if (strpos($string, $start)) { // required if $start not exist in $string
        $startCharCount = strpos($string, $start) + strlen($start);
        $firstSubStr = substr($string, $startCharCount, strlen($string));
        $endCharCount = strpos($firstSubStr, $end);
        if ($endCharCount == 0) {
            $endCharCount = strlen($firstSubStr);
        }
        return trim(substr($firstSubStr, 0, $endCharCount), " \t\n\r\0\x0B\xC2\xA0");
    } else {
        return '';
    }
}
function getFirstValue($sentence)
{
    // this will return the first value from a string if it has comma separated values
    $values = explode(',', $sentence);
    if (count($values) === 1) {
        return $values[0];
    }
    return trim($values[0]);
}

/// Bitrix sources
function bitrixSource($id)
{
    // this is the source list from our crm mapped to bitrix source id
    $sr = array(
        "6" => "AX CAPITAL",
        "79" => "Facebook",
        "7" => "Instagram",
        "116" => "Referral",
        "8" => "Whatsapp",
        "9" => "LinkedIn",
        "10" => "Twitter",
        "11" => "Email campaign",
        "12" => "Roadshow",
        "13" => "Personal Referral",
        "14" => "Referral",
        "160" => "Whatsapp",
        "165" => "Whatsapp",
        "166" => "Live chat",
        "164" => "Whatsapp",
        "151" => "Chat2Desk",
        "152" => "Whatsapp",
        "153" => "Live chat",
        "144" => "Telegram",
        "145" => "Live chat",
        "146" => "Live chat",
        "147" => "Instagram",
        "148" => "Whatsapp",
        "149" => "Whatsapp",
        "15" => "AX CAPITAL",
        "16" => "Listing Portal",
        "80" => "Live chat",
        "105" => "Emaar Stand",
        "106" => "Facebook",
        "107" => "Instagram",
        "108" => "Denis",
        "109" => "Victoria",
        "110" => "Denis",
        "111" => "Victoria",
        "113" => "PRIAN",
        "130" => "HOMESOVERSEAS",
        "138" => "TRANIO",
        "101" => "AX CAPITAL",
        "117" => "AX CAPITAL",
        "118" => "Property Finder",
        "115" => "Bayut",
        "167" => "Houza",
        "127" => "Bayut",
        "128" => "Property Finder",
        "137" => "Dubizzle",
        "119" => "Referral",
        "126" => "Telesales",
        "17" => "Telesales",
        "129" => "Agent External",
        "131" => "Referral",
        "135" => "VK",
        "139" => "Youtube",
        "140" => "Whitewill",
        "142" => "Emirates.Estate",
        "143" => "AX CAPITAL",
        "150" => "Whatsapp",
        "154" => "Stand",
        "155" => "Whatsapp",
        "157" => "Whatsapp",
        "158" => "Whatsapp",
        "159" => "Whatsapp",
        "168" => "Walk-in",
        "169" => "AX Corporate",
    );
    return $sr[$id] ?? 'Bitrix-unspecified';
}
function bitrixSourceName($id)
{
    $name = array(
        "CALL" => "AXCAPITAL",
        "EMAIL" => "Instagram",
        "WEB" => "Whatsapp",
        "ADVERTISING" => "LinkedIn",
        "PARTNER" => "Twitter",
        "RECOMMENDATION" => "Emails",
        "TRADE_SHOW" => "Third Party",
        "WEBFORM" => "Personal Lead",
        "CALLBACK" => "Company Referral",
        "RC_GENERATOR" => "Website Visitor",
        "STORE" => "LISTINGS PORTAL",
        "FACE_TRACKER" => "Facebook",
        "1|OPENLINE" => "Live chat - Open Channel AX CAPITAL",
        "2" => "AXCAPITAL UKRAIN",
        "3" => "EMAAR STAND",
        "4" => "Facebook Chat",
        "5" => "Instagram Chat",
        "6" => "Denis sent over WhatsApp",
        "7" => "Victoria sent over WhatsApp",
        "8" => "Denis gave as call in",
        "9" => "Victoria gave as call in",
        "11" => "PRIAN",
        "12" => "BAYUT",
        "13" => "Referral",
        "14" => "AXCAPITAL UZBEKISTAN",
        "15" => "PROPERTYFINDER",
        "16" => "FOREIGN AGENCY",
        "17" => "TELESALES",
        "18" => "BAYUT CPL",
        "19" => "PF CPL",
        "20" => "EXTERNAL AGENT",
        "21" => "HOMESOVERSEAS",
        "22" => "OFPP",
        "23" => "VK",
        "24" => "DUBIZZLE",
        "25" => "TRANIO",
        "26" => "YOUTUBE",
        "27" => "WHITEWILL",
        "28" => "Emirates.Estate",
        "29" => "AXCAPITAL.AE",
        "4|TELEGRAM" => "Telegram - TELEGRAM",
        "5|OPENLINE" => "Live chat - Online Chat",
        "11|OPENLINE" => "Live chat - Online Chat RU",
        "4|WZ_INSTAGRAM_D7B38BE12F532B91F4E490341AF7B000" => "WAZZUP: Instagram - TELEGRAM",
        "1|WZ_WHATSAPP_CD7B38BE12F532B91F4E490341AF7B000" => "WAZZUP: WhatsApp - Open Channel AX CAPITAL",
        "5|WZ_WHATSAPP_CD7B38BE12F532B91F4E490341AF7B000" => "WAZZUP: WhatsApp - Online Chat EN",
        "WZ8a4d1514-d639-43ce-a747-ed772857656f" => "Whatsapp New WA channel 971522156205",
        "17|C2D_1GT_CONNECTOR" => "Chat2Desk - Open Channel 24",
        "18|WZ_WHATSAPP_CD7B38BE12F532B91F4E490341AF7B000" => "WAZZUP: WhatsApp - Sales Office",
        "20|OPENLINE" => "Live chat - JivoChat Test",
        "30" => "STAND",
        "WZe0f24496-78ba-4aca-bd7d-4e1cc93d0b80" => "Whatsapp WhatsApp Mail -  971522361272",
        "WZ78478ade-36e1-4dcf-beca-3c3a706130c3" => "Whatsapp EN_REGULAR - 971521718769",
        "WZ8092d5f9-f91a-4394-8739-f091bbe2b0fb" => "Whatsapp EN_i - 97145710426",
        "WZ54da09a3-34fa-434b-bdab-0162046758e7" => "Whatsapp RU_i - 97145710425",
        "29|WAPPIM_INSTANCE_7728" => "    Wapp.im     WhatsApp #7728 - Operator Space",
        "1|BITRIX_WHATCRM_NET_70680444" => "    whatcrm     WhatsApp - Open Channel AX CAPITAL",
        "19|BITRIX_WHATCRM_NET_70680444" => "    whatcrm     WhatsApp - Generic",
        "30|OPENLINE" => "Live chat - OnlineChat_RU(v2)",
        "31" => "HOUZA",
        "32" => "WALK-IN",
        "33" => "AX CORPORATE",
    );
    return $name[$id] ?? 'Bitrix-unspecified';
}
function bitrixMediumById($id)
{

    //this is the medium list from our crm mapped to bitrix source id
    $med = array(
        "6" => "Call",
        "79" => "Api",
        "7" => "Api",
        "116" => "Api",
        "8" => "WhatsApp",
        "9" => "Api",
        "10" => "Api",
        "11" => "Email",
        "12" => "Api",
        "13" => "Website Form",
        "14" => "Call",
        "160" => "WhatsApp",
        "165" => "WhatsApp",
        "166" => "Api",
        "164" => "WhatsApp",
        "151" => "Api",
        "152" => "WhatsApp",
        "153" => "Api",
        "144" => "Telegram",
        "145" => "Api",
        "146" => "Api",
        "147" => "Api",
        "148" => "WhatsApp",
        "149" => "WhatsApp",
        "15" => "Api",
        "16" => "Unspecified",
        "80" => "Api",
        "105" => "Api",
        "106" => "Api",
        "107" => "Api",
        "108" => "WhatsApp",
        "109" => "WhatsApp",
        "110" => "Call",
        "111" => "Call",
        "113" => "Api",
        "130" => "Api",
        "138" => "Api",
        "101" => "Website Form",
        "117" => "Website Form",
        "118" => "Email",
        "115" => "Email",
        "167" => "Email",
        "127" => "Call",
        "128" => "Call",
        "137" => "Email",
        "119" => "Api",
        "126" => "Call",
        "129" => "Call",
        "131" => "Call",
        "135" => "Api",
        "139" => "Api",
        "140" => "Api",
        "142" => "Api",
        "143" => "Api",
        "150" => "WhatsApp",
        "154" => "Unspecified",
        "155" => "WhatsApp",
        "157" => "WhatsApp",
        "158" => "WhatsApp",
        "159" => "WhatsApp",
        "168" => "Unspecified",
        "169" => "Api",
    );
    return $med[$id] ?? 'Api';

}
function bitrixMediumGetId($text)
{
    $med = array(
        "33" => "169",
        "32" => "168",
        "31" => "167",
        "30|OPENLINE" => "166",
        "19|BITRIX_WHATCRM_NET_70680444" => "165",
        "1|BITRIX_WHATCRM_NET_70680444" => "164",
        "29|WAPPIM_INSTANCE_7728" => "160",
        "WZ54da09a3-34fa-434b-bdab-0162046758e7" => "159",
        "WZ8092d5f9-f91a-4394-8739-f091bbe2b0fb" => "158",
        "WZ78478ade-36e1-4dcf-beca-3c3a706130c3" => "157",
        "WZe0f24496-78ba-4aca-bd7d-4e1cc93d0b80" => "155",
        "30" => "154",
        "20|OPENLINE" => "153",
        "WhatsApp" => "152",
        "17|C2D_1GT_CONNECTOR" => "151",
        "WZ8a4d1514-d639-43ce-a747-ed772857656f" => "150",
        "WhatsApp" => "149",
        "WhatsApp" => "148",
        "4|WZ_INSTAGRAM_D7B38BE12F532B91F4E490341AF7B000" => "147",
        "11|OPENLINE" => "146",
        "5|OPENLINE" => "145",
        "Telegram" => "144",
        "29" => "143",
        "28" => "142",
        "27" => "140",
        "26" => "139",
        "25" => "138",
        "24" => "137",
        "23" => "135",
        "22" => "131",
        "21" => "130",
        "20" => "129",
        "19" => "128",
        "18" => "127",
        "17" => "126",
        "16" => "119",
        "15" => "118",
        "14" => "117",
        "13" => "116",
        "12" => "115",
        "11" => "113",
        "9" => "111",
        "8" => "110",
        "7" => "109",
        "6" => "108",
        "5" => "107",
        "4" => "106",
        "3" => "105",
        "2" => "101",
        "1|OPENLINE" => "80",
        "FACE_TRACKER" => "79",
        "STORE" => "16",
        "RC_GENERATOR" => "15",
        "CALLBACK" => "14",
        "Website Form" => "13",
        "TRADE_SHOW" => "12",
        "RECOMMENDATION" => "11",
        "PARTNER" => "10",
        "ADVERTISING" => "9",
        "WEB" => "8",
        "EMAIL" => "7",
        "CALL" => "6",
    );
    return $med[$text] ?? null;
}
function bitrixMediumToSysMedium($text)
{

    $med = array(
        "CALL" => "Call",
        "FACE_TRACKER" => "Api",
        "EMAIL" => "Api",
        "13" => "Api",
        "WEB" => "WhatsApp",
        "ADVERTISING" => "Api",
        "PARTNER" => "Api",
        "RECOMMENDATION" => "Email",
        "TRADE_SHOW" => "Api",
        "WEBFORM" => "Website Form",
        "CALLBACK" => "Call",
        "29|WAPPIM_INSTANCE_7728" => "WhatsApp",
        "19|BITRIX_WHATCRM_NET_70680444" => "WhatsApp",
        "30|OPENLINE" => "Api",
        "1|BITRIX_WHATCRM_NET_70680444" => "WhatsApp",
        "17|C2D_1GT_CONNECTOR" => "Api",
        "18|WZ_WHATSAPP_CD7B38BE12F532B91F4E490341AF7B000" => "WhatsApp",
        "20|OPENLINE" => "Api",
        "4|TELEGRAM" => "Telegram",
        "5|OPENLINE" => "Api",
        "11|OPENLINE" => "Api",
        "4|WZ_INSTAGRAM_D7B38BE12F532B91F4E490341AF7B000" => "Api",
        "1|WZ_WHATSAPP_CD7B38BE12F532B91F4E490341AF7B000" => "WhatsApp",
        "5|WZ_WHATSAPP_CD7B38BE12F532B91F4E490341AF7B000" => "WhatsApp",
        "RC_GENERATOR" => "Api",
        "STORE" => "Unspecified",
        "1|OPENLINE" => "Api",
        "3" => "Api",
        "4" => "Api",
        "5" => "Api",
        "6" => "WhatsApp",
        "7" => "WhatsApp",
        "8" => "Call",
        "9" => "Call",
        "11" => "Api",
        "21" => "Api",
        "25" => "Api",
        "2" => "Website Form",
        "14" => "Website Form",
        "15" => "Email",
        "12" => "Email",
        "31" => "Email",
        "18" => "Call",
        "19" => "Call",
        "24" => "Email",
        "16" => "Api",
        "17" => "Call",
        "20" => "Call",
        "22" => "Call",
        "23" => "Api",
        "26" => "Api",
        "27" => "Api",
        "28" => "Api",
        "29" => "Api",
        "WZ8a4d1514-d639-43ce-a747-ed772857656f" => "WhatsApp",
        "30" => "Unspecified",
        "WZe0f24496-78ba-4aca-bd7d-4e1cc93d0b80" => "WhatsApp",
        "WZ78478ade-36e1-4dcf-beca-3c3a706130c3" => "WhatsApp",
        "WZ8092d5f9-f91a-4394-8739-f091bbe2b0fb" => "WhatsApp",
        "WZ54da09a3-34fa-434b-bdab-0162046758e7" => "WhatsApp",
        "32" => "Unspecified",
        "33" => "Api",

    );
    return $med[$text] ?? 'Api';
}

function findMediumForBitrix($source_id)
{
    if ($source_id == null || $source_id == '') {
        return 'Api';
    }
    if (is_numeric($source_id)) {
        $bitrixMediumText = bitrixMediumById($source_id);
        return bitrixMediumToSysMedium($bitrixMediumText);
    } else {
        return bitrixMediumToSysMedium($source_id);
    }
}
function findSourceForBitrix($source_id)
{
    if ($source_id == null || $source_id == '') {
        return 'Bitrix-unspecified';
    }
    if (is_numeric($source_id)) {
        return bitrixSource($source_id);
    } else {
        //if its text sometime bitrix is showing STATUS_ID instead of SOURCE_ID which is text
        $source_id = bitrixMediumGetId($source_id);
        return bitrixSource($source_id);
    }
}
function findSourceNameForBitrix($source_id)
{
    if ($source_id == null || $source_id == '') {
        return 'Bitrix-unspecified';
    } else {
        //if its text sometime bitrix is showing STATUS_ID instead of SOURCE_ID which is text
        return bitrixSourceName($source_id);
    }
}

function getReleventDateRange($dateRanges)
{
    // this will return most relevant date range for current date
    $currentDate = new DateTime(date('Y-m-d'));
    $currentTimestamp = $currentDate->getTimestamp();

    $validDateRanges = array_filter($dateRanges, function ($dateRange) use ($currentTimestamp) {
        //only ranges with an end date greater than or equal to the current timestamp are included.
        $endDate = new DateTime($dateRange['endDate']);
        $endTimestamp = $endDate->getTimestamp();
        return $endTimestamp >= $currentTimestamp;
    });

    $sortedDateRanges = array_values($validDateRanges); // reset array keys
    usort($sortedDateRanges, function ($a, $b) use ($currentTimestamp) {
        $startATimestamp = (new DateTime($a['startDate']))->getTimestamp();
        $startBTimestamp = (new DateTime($b['startDate']))->getTimestamp();
        $diffA = abs($startATimestamp - $currentTimestamp);
        $diffB = abs($startBTimestamp - $currentTimestamp);
        return $diffA - $diffB;
    });

    $mostRelevantDateRange = $sortedDateRanges[0];
    return $mostRelevantDateRange;
}
function getTimeDifference($time)
{
//set timezone to dubai

    $time = Carbon::parse($time);
    $hours = Carbon::now()->diffInHours($time);
    $minutesDiff = Carbon::now()->diffInMinutes($time) % 60;
    $result = '';
    if ($hours > 0) {
        $result .= $hours . 'hrs';
    }
    if ($minutesDiff > 0) {
        if ($hours > 0) {
            $result .= ', ';
        }
        $result .= $minutesDiff . 'min';
    }

    return $result;
}
function eventTitleBadgeColor($event_title)
{
    $arr = [
        "Viewing" => "bg-soft-success text-success",
        "Meeting" => " bg-soft-info text-info",
        "Task" => "bg-soft-warning text-warning",
        "Schedule a call" => "bg-soft-danger text-danger",
        "Photographer Booking" => "bg-soft-primary text-primary",
        "Holiday/Leave" => "bg-soft-success text-success",
        "Other" => "bg-soft-info text-info",
    ];
    return $arr[$event_title] ?? "bg-soft-primary text-primary";

}
function randomBadgeColors()
{
    $arr = [
        "bg-soft-danger text-danger",
        "bg-soft-success text-success",
        "bg-soft-info text-info",
        "bg-soft-warning text-warning",
        "bg-soft-primary text-primary",
        "bg-soft-secondary text-secondary",
    ];
    $randomKey = array_rand($arr);
    return $arr[$randomKey];
}
function generateTimeSlots($start, $end)
{
    $startTime = new DateTime($start);
    $endTime = new DateTime($end);

    $timeSlots = [];

    $current = clone $startTime;
    while ($current <= $endTime) {
        $timeSlots[] = $current->format('H:i');
        $current->add(new DateInterval('PT1H'));
    }

    return $timeSlots;
}

function checkownership($id)
{
    $media = Media::find($id);
    $property = Property::find($media->property_id);
    $user = Auth::user();
    if ($property->agent_id == $user->id) {
        return true;
    }
    if (auth()->user()->hasRole(['Admin', 'Super Admin'])) {
        return true;
    }

    return false;
}

//format phone number to UAE format
function formatUAEPhoneNumber($phoneNumber)
{

    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

    if (strlen($phoneNumber) < 8 || strlen($phoneNumber) > 14) {
        return $phoneNumber;
    }

    if (substr($phoneNumber, 0, 3) === '971') {
        $phoneNumber = substr($phoneNumber, 3);
        $formattedNumber = '+971' . $phoneNumber;
        return strlen($formattedNumber) == 13 || strlen($formattedNumber) == 12 ? $formattedNumber : 'invalid';
    }
    if (substr($phoneNumber, 0, 5) === '00971') {
        $phoneNumber = substr($phoneNumber, 5);
        $formattedNumber = '+971' . $phoneNumber;
        return strlen($formattedNumber) == 13 || strlen($formattedNumber) == 12 ? $formattedNumber : 'invalid';
    }

    if (substr($phoneNumber, 0, 3) === '050' || substr($phoneNumber, 0, 3) === '052' || substr($phoneNumber, 0, 3) === '054' || substr($phoneNumber, 0, 3) === '055' || substr($phoneNumber, 0, 3) === '056' || substr($phoneNumber, 0, 3) === '058') {
        $phoneNumber = substr($phoneNumber, 1);
        $formattedNumber = '+971' . $phoneNumber;
        return strlen($formattedNumber) == 13 ? $formattedNumber : 'invalid';
    }

    if (substr($phoneNumber, 0, 2) === '50' || substr($phoneNumber, 0, 2) === '52' || substr($phoneNumber, 0, 2) === '54' || substr($phoneNumber, 0, 2) === '55' || substr($phoneNumber, 0, 2) === '56' || substr($phoneNumber, 0, 2) === '58') {
        $formattedNumber = '+971' . $phoneNumber;
        return strlen($formattedNumber) == 13 ? $formattedNumber : 'invalid';
    }

    if (substr($phoneNumber, 0, 2) === '01' || substr($phoneNumber, 0, 2) === '02' || substr($phoneNumber, 0, 2) === '03' || substr($phoneNumber, 0, 2) === '04' || substr($phoneNumber, 0, 2) === '06' || substr($phoneNumber, 0, 2) === '07' || substr($phoneNumber, 0, 2) === '09') {
        $phoneNumber = substr($phoneNumber, 1);
        $formattedNumber = '+971' . $phoneNumber;
        return strlen($formattedNumber) == 12 ? $formattedNumber : 'invalid';
    }

    if (substr($phoneNumber, 0, 1) === '1' || substr($phoneNumber, 0, 1) === '2' || substr($phoneNumber, 0, 1) === '3' || substr($phoneNumber, 0, 1) === '4' || substr($phoneNumber, 0, 1) === '6' || substr($phoneNumber, 0, 1) === '7' || substr($phoneNumber, 0, 1) === '9') {
        $formattedNumber = '+971' . $phoneNumber;
        return strlen($formattedNumber) == 12 ? $formattedNumber : 'invalid';
    }

    return $phoneNumber;
    // If the phone number doesn't start with above matching conditions, return the original phone number
    return 'invalids';
}

function formatGlobalPhoneNumber($phoneNumber, $countryCode)
{
    $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

    if (strlen($phoneNumber) < 8 || strlen($phoneNumber) > 14) {
        return 'invalid';
    }

    if (substr($phoneNumber, 0, strlen($countryCode)) === $countryCode) {
        $phoneNumber = substr($phoneNumber, strlen($countryCode));
        $formattedNumber = '+' . $countryCode . $phoneNumber;
        return (strlen($formattedNumber) == strlen($countryCode) + 12 || strlen($formattedNumber) == strlen($countryCode) + 13) ? $formattedNumber : 'invalid';
    }

    // If the phone number doesn't start with the specified country code, return 'invalid'
    return 'invalid';
}

function validatePhoneNumber($number)
{
    if (strpos($number, '+') === 0) {
        $number = substr($number, 1);
    }

    // Remove leading '00'
    if (strpos($number, '00') === 0) {
        $number = substr($number, 2);
    }

    // Remove leading '0' if any
    if (strpos($number, '0') === 0) {
        $number = substr($number, 1);
    }

    // Format the number
    $number = preg_replace('/[^0-9+]/', '', $number);

    // Add country code if necessary
    if (strlen($number) >= 8 && strlen($number) <= 9) {
        $number = '971' . $number;
    }

    // Add '+' if necessary
    if (strpos($number, '+') !== 0) {
        $number = '+' . $number;
    }

    $result = format_and_validate_number($number);

    if ($result['valid']) {
        return $result['formatted_number'];
    } else {
        $number = preg_replace('/[^A-Za-z0-9\-]/', '', $number);
        $number = str_replace('-', '', $number);
        $number = preg_replace('/[^+0-9]/', '', $number);
        if (strpos($number, '+') !== 0) {
            $number = '+' . $number;
        }
        if ($number == '+') {
            return null;
        }

        return $number;
    }

}

function format_and_validate_number($number)
{
    $phone_number_util = PhoneNumberUtil::getInstance();

    try {
        $phone_number = $phone_number_util->parse($number, null);
        $is_valid = $phone_number_util->isValidNumber($phone_number);
        $formatted_number = $phone_number_util->format($phone_number, PhoneNumberFormat::E164);

        return array(
            'valid' => $is_valid,
            'formatted_number' => $formatted_number,
        );
    } catch (NumberParseException $e) {
        return array(
            'valid' => false,
            'formatted_number' => null,
        );
    }

}

function checkformat($number)
{
    return format_and_validate_number_check($number);

}

function format_and_validate_number_check($number)
{
    $phone_number_util = PhoneNumberUtil::getInstance();

    try {
        $phone_number = $phone_number_util->parse($number, null);
        $is_valid = $phone_number_util->isValidNumber($phone_number);
        $formatted_number = $phone_number_util->format($phone_number, PhoneNumberFormat::E164);
        if ($is_valid) {
            return 'TRUE';
        } else {
            return 'FALSE';
        }
        return $is_valid;
        return array(
            'valid' => $is_valid,
            'formatted_number' => $formatted_number,
        );
    } catch (NumberParseException $e) {
        return 'FALSE';
        return array(
            'valid' => false,
            'formatted_number' => null,
        );
    }

}

function axAcademyUrl($email)
{
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://axcapital.talentlms.com/api/v1/users/email:' . $email,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic dThVdVdsYWFmVUszVUdxUDd4Vk1BVk5XSlZlUEUyOg==',
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $data = json_decode($response, true);

    return $data['login_key'] ?? null;
}

function getLocation($type, $id)
{

    return $type::find($id)->name;
}

function formatPrice($price)
{
    return number_format($price, 2, '.', ',');
}
function formatPriceToInt($price)
{
    return number_format($price, 0, '.', ',');
}

function generateMessage($req, $modal)
{
    $message = 'A Duplicate entry of ' . $modal . ' is recorded with following details: <br>';
    if ($req['first_name']) {
        $message .= '<b>First Name</b>: ' . $req['first_name'] . '<br>';
    }
    if ($req['last_name']) {
        $message .= '<b>Last Name</b>: ' . $req['last_name'] . '<br>';
    }
    if ($req['email']) {
        $message .= '<b>Email</b>: ' . $req['email'] . '<br>';
    }
    if ($req['mobile']) {
        $message .= '<b>mobile</b>: ' . $req['mobile'] . '<br>';
    }
    if ($req['property_category_id']) {
        $message .= '<b>Property Category</b>: ' . PropertyCategory::find($req['property_category_id'])->name . '<br>';
    }
    if ($req['for']) {
        $message .= '<b>Purpose</b>: ' . $req['for'] . '<br>';
    }
    if ($req['source_id']) {
        $source = explode('-', $req['source_id']);
        $message .= '<b>Source</b>: ' . Source::find($source[1])->name . '<br>';
    }
    if ($req['unit_no']) {
        $message .= '<b>Unit No</b>: ' . $req['unit_no'] . '<br>';
    }
    if ($req['bed_id']) {
        $message .= '<b>Beds</b>: ' . Bed::find($req['bed_id'])->name . '<br>';
    }
    if ($req['bath_id']) {
        $message .= '<b>Baths</b>: ' . Bath::find($req['bath_id'])->name . '<br>';
    }
    if ($req['note']) {
        $message .= '<b>Note</b>: ' . $req['note'] . '<br>';
    }
    return $message;

}

function getParentableClass($type)
{
    if ($type == 'CommunityProperty') {
        return CommunityProperty::class;
    } elseif ($type == 'community') {
        return Community::class;
    } elseif ($type == 'sub_community') {
        return SubCommunity::class;
    }

}

if (!function_exists('generateAlpha')) {

    function generateAlpha($length = 20)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        // $random1 = rand(100, 999);
        $alphanumeric = $randomString;
        return $alphanumeric;
    }
}

if (!function_exists('getAvatar')) {

    function getAvatar($id)
    {
        $user = User::find($id);
        return $user->thumbnail ?? 'https://ui-avatars.com/api/?name=' . $user->name . '&background=0D8ABC&color=fff&size=128';
    }
}

if (!function_exists('getRoles')) {

    function getRoles($id)
    {
        $roles = '';
        foreach (User::find($id)->roles as $role) {
            $roles .= $role->name . ', ';
        }
        $roles = rtrim($roles, ', ');
        return $roles;
    }
}

if (!function_exists('statusUi')) {
    function statusUi($id)
    {
        $arr = [
            'PUBLISHED' => [
                'icon' => 'bx bx-check-circle text-success',
                'color' => 'bg-soft-success',
            ],
            'PENDING' => [
                'icon' => 'bx bx-hourglass text-warning',
                'color' => 'bg-soft-warning',
            ],
            'DRAFT' => [
                'icon' => 'bx bx-pencil text-primary',
                'color' => 'bg-soft-primary',
            ],

            'POCKET' => [
                'icon' => 'bx bx-briefcase-alt text-secondary',
                'color' => 'bg-soft-secondary',
            ],
            'UNPUBLISHED' => [
                'icon' => ' bx bx-power-off text-danger',
                'color' => 'bg-soft-danger',
            ],
            'ARCHIVED' => [
                'icon' => ' bx bx-archive-in text-danger',
                'color' => 'bg-soft-danger',
            ],
            'REFUSED' => [
                'icon' => 'bx bx-x-circle text-danger',
                'color' => 'bg-soft-danger',
            ],

        ];
        if ($id != '' || $id != null) {
            $ui = $arr[$id];
            $color = $ui['color'] ?? 'bg-success';
            $icon = $ui['icon'] ?? 'bx bx-check-circle text-success';
            $html = '<div class="flex-shrink-0 avatar-xxs ms-1"> <div class="avatar-title ' . $color . ' rounded-circle shadow"> <i style="" class="text-primary ' . $icon . '"></i> </div> </div>';
            return $html;
        }

    }
}
if (!function_exists('convertToDubaiTimezone')) {
    function convertToDubaiTimezone($utcDatetime)
    {
        $datetime = new DateTime($utcDatetime, new DateTimeZone('UTC'));
        $dubaiTimezone = new DateTimeZone('Asia/Dubai');
        $datetime->setTimezone($dubaiTimezone);
        return $datetime->format('Y-m-d H:i:s');
    }
}

if (!function_exists('createPublishStatusBadge')) {

    function createPublishStatusBadge($status)
    {
        $badgeClass = '';
        switch ($status) {
            case 'PUBLISHED':
                $badgeClass = 'success';
                break;
            case 'DRAFT':
                $badgeClass = 'primary';
                break;
            case 'PENDING':
                $badgeClass = 'warning';
                break;
            case 'POCKET':
                $badgeClass = 'secondary';
                break;
            default:
                $badgeClass = 'danger';
        }

        return '<span class="badge badge-soft-' . $badgeClass . '">' . $status . '</span>';
    }
}
if (!function_exists('mapStatusToBadgeClass')) {

    function mapStatusToBadgeClass($status)
    {
        switch ($status) {
            case 'PUBLISHED':
                return 'success';
            case 'DRAFT':
                return 'primary';
            case 'PENDING':
                return 'warning';
            case 'POCKET':
                return 'secondary';
            default:
                return 'danger';
        }
    }
}
if (!function_exists('formatPrice2')) {

    function formatPrice2($price)
    {
        return number_format($price, 0, '.', ',');
    }

    function replaceSpaces($string)
    {
        return str_replace(' ', '-', $string);
    }

}
if (!function_exists('trimSpaces')) {

    function trimSpaces($string)
    {
        $string = str_replace('+', '', $string);
        return preg_replace('/\s+/', ' ', $string);
    }
}

if (!function_exists('getSingleImage')) {

    function getSingleImage($value)
    {
        $value = json_decode($value);

        foreach ($value as $key => $v) {
            return $v;
        }

    }
}

if (!function_exists('fromSettings')) {
    function fromSettings(string $module, string $key, $alternative = null)
    {
        return GeneralSetting::where('module', $module)->where('key', $key)->first()->value ?? $alternative;
    }
}

if (!function_exists('setSettings')) {
    function setSettings(string $key, string $value)
    {
        GeneralSetting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        return true;
    }
}

if (!function_exists('formatText')) {
    function formatText($value)
    {

        return ucwords(str_replace('_', ' ', $value));
    }
}

if (!function_exists('cleanData')) {
    function cleanData($value)
    {
        $value = str_replace('-', '', $value);
        $value = str_replace('_', '', $value);
        $value = str_replace(',', '', $value);
        $value = str_replace("'", '', $value);
        $value = str_replace('"', '', $value);
        $value = str_replace('(', '', $value);
        $value = str_replace(')', '', $value);
        $value = str_replace('.', '', $value);
        if (is_numeric($value)) {
            return $value;
        } else {
            return null;
        }
        return $value;

    }
}

if (!function_exists('locationIdentifierV1')) {
    function locationIdentifierV1($value)
    {
        $parts = array_reverse(explode(',', $value));

        // Arezzo 1,Arezzo,Tuscan Residence,JVC District 10,Jumeirah Village Circle (JVC),Dubai,UAE
        // Initialize variables for each component
        $property = '';
        $community_property = '';
        $community = '';
        $sub_community = '';
        $state = '';
        $country = '';
        // dd(count($parts) );
        if (count($parts) == 7) {
            $country = trim($parts[0]);
            $state = trim($parts[1]);
            $community = trim($parts[2]);
            $sub_community = trim($parts[3]);
            $community_property = trim($parts[4]);
            $property = trim($parts[5]);
            $tower = trim($parts[6]);
            $locationInfo = [
                'tower' => $tower, // 'tower' is used in the frontend to refer to 'property
                'property' => $property,
                'community_property' => $community_property,
                'sub_community' => $sub_community,
                'community' => $community,
                'state' => $state,
                'country' => $country,
            ];
            // return $locationInfo;
            $community_id = null;
            $sub_community_id = null;
            $community_property_id = null;

            //    search community
            $searchTerm = preg_replace('/\([^)]+\)/', '', $locationInfo['community']);
            $com = Community::where('name', 'like', '%' . trim($searchTerm) . '%')->first();

            $sub = SubCommunity::where('name', $locationInfo['sub_community'])->orWhere('alternate_name', $locationInfo['sub_community'])->first();
            $sub = SubCommunity::where('name', 'like', '%' . $locationInfo['community_property'] . '%')->orWhere('alternate_name', 'like', '%' . $locationInfo['community_property'] . '%')->first();

            $prop = CommunityProperty::where('name', 'like', '%' . $locationInfo['property'] . '%')->orWhere('alternate_name', 'like', '%' . $locationInfo['property'] . '%')->first();

            if ($prop) {

                $similarity = similarityPercentage($value, $prop->path_name);
                if ($similarity > 60) {
                    $prop = CommunityProperty::where('name', 'like', '%' . $locationInfo['property'] . '%')->orWhere('alternate_name', 'like', '%' . $locationInfo['property'] . '%')->first();
                } else {
                    $prop = CommunityProperty::where('name', 'like', '%' . $locationInfo['property'] . '%')->orWhere('alternate_name', 'like', '%' . $locationInfo['property'] . '%')->first();
                }
                $data = [
                    'similarity' => $similarity,
                    'Prop' => $prop,
                ];
                return $data;

            }

            $location = [
                'community_id' => $com->id ?? $community,
                'sub_community_id' => $sub->id ?? $sub_community,
                'community_property_id' => $prop->id ?? $community_property,
            ];

            return $location;
        }
        if (count($parts) == 6) {

        }

        // Assign values based on the number of parts in the reversed exploded array
        // if (count($parts) >= 1) {
        //     $country = trim($parts[0]);
        // }
        // if (count($parts) >= 2) {
        //     $state = trim($parts[1]);
        // }
        // if (count($parts) >= 3) {
        //     $community = trim($parts[2]);
        // }
        // if (count($parts) >= 4) {
        //     $sub_community = trim($parts[3]);
        // }
        // if (count($parts) >= 5) {
        //     $community_property = trim($parts[4]);
        // }
        // if (count($parts) >= 6) {
        //     $property = trim($parts[6]);
        // }

        // Create an associative array to store the values

    }
}

function similarityPercentage($str1, $str2)
{
    $length = max(strlen($str1), strlen($str2));
    $distance = levenshtein($str1, $str2);
    $similarity = 1 - ($distance / $length);
    $percentageSimilarity = $similarity * 100;
    return $percentageSimilarity;
}

if (!function_exists('locationIdentifier')) {
    function locationIdentifier($value)
    {
        $parts = explode(',', $value);
        if (count($parts) >= 2) {
            $tower = trim($parts[0]);
            $name = CommunityProperty::where('name', $tower)->orWhere('alternate_name', $tower)->first();

            if ($name) {
                $name = CommunityProperty::where('name', 'like', '%' . $tower . '%')->orWhere('alternate_name', 'like', '%' . $tower . '%')->first();
            }
            if ($name) {
                $data = [
                    'type' => 'CommunityProperty',
                    'data' => $name,
                ];
                return $data;
            }
            $name = Community::where('name', $tower)->orWhere('alternate_name', $tower)->first();
            return $data = [
                'type' => 'Community',
                'data' => $name,
            ];

        }
    }
}

if (!function_exists('__locationIdentifier')) {
    function __locationIdentifier($value)
    {
        $parts = explode(',', $value);

        $property = '';
        $community_property = '';
        $community = '';
        $sub_community = '';
        $state = '';
        $country = '';
        $additional = '';

        if (count($parts) >= 2) {
            $tower = trim($parts[0]);
            $community_property = CommunityProperty::where('name', 'like', '%' . $tower . '%')->orWhere('alternate_name', 'like', '%' . $tower . '%')->get();
            if (count($community_property) > 1) {
                $community_property = CommunityProperty::where('name', 'like', '%' . $tower . '%')->orWhere('alternate_name', 'like', '%' . $tower . '%')->first();
            } else {
                $community_property = $community_property->first();
            }
            return $community_property;
        }

        if (count($parts) >= 2) {
            $community_property = trim($parts[1]);
        }
        if (count($parts) >= 3) {
            $community = trim($parts[2]);
        }
        if (count($parts) >= 4) {
            $sub_community = trim($parts[3]);
        }
        if (count($parts) >= 5) {
            $state = trim($parts[4]);
        }
        if (count($parts) >= 6) {
            $country = trim($parts[5]);
        }
        if (count($parts) >= 7) {
            $additional = trim($parts[6]);
        }

        // Create an associative array to store the values
        $locationInfo = [
            'property' => $property,
            'community_property' => $community_property,
            'community' => $community,
            'sub_community' => $sub_community,
            'state' => $state,
            'country' => $country,
            'additional' => $additional,
        ];

        return $locationInfo;
    }
}

if (!function_exists('locFinder')) {
    function locFinder($value)
    {

        $parts = explode(',', $value);
        $count = count($parts);
        if ($count > 3) {

            $first = $parts[0];
            list($last1, $last2, $last3) = array_slice($parts, -3);
            // dd($last1, $last2, $last3);
            $prop = CommunityProperty::where('name', 'like', '%' . $first . '%')->orWhere('alternate_name', 'like', '%' . $first . '%')->first();

            info('Property ' . $prop);
            if ($prop) {
                $searchTerm = preg_replace('/\([^)]+\)/', '', $last1);

                info('serach term ' . $searchTerm);
                $confirm = Community::where('name', 'like', '%' . $searchTerm . '%')->orWhere('alternate_name', 'like', '%' . trim($searchTerm) . '%')->first();
                info($confirm);
                if ($confirm) {
                    $data = [
                        'type' => 'Community Property',
                        'data' => $prop,
                        'value' => $confirm,
                    ];
                    return $data;
                }
            } else {
                $community = Community::where('name', 'like', '%' . $first . '%')->orWhere('alternate_name', 'like', '%' . $first . '%')->first();
                info($community);
                return $data = [
                    'type' => 'Community',
                    'data' => $community,
                    'value' => $value,
                ];
            }

        }

    }
}

if (!function_exists('replaceNumbersWithAsterisks')) {
    function replaceNumbersWithAsterisks($text)
    {

        $pattern = '/\d{6,}/';
        $replacement = '*******';
        $result = preg_replace($pattern, $replacement, $text);
        return $result;
    }
}

if (!function_exists('getUserMeta')) {
    function getUserMeta(string $tag, string $key, $user_id)
    {
        return UserMeta::where('user_id', $user_id)->where('tag', $tag)->where('meta_key', $key)->first()->meta_value;
    }
}

if (!function_exists('saveUserMeta')) {
    function saveUserMeta(string $tag, string $key, string $value, $user_id)
    {

        $meta = UserMeta::where('user_id', $user_id)->where('tag', $tag)->where('meta_key', $key)->first();
        if ($meta) {
            $meta->meta_value = $value;
            $meta->save();
            return true;
        } else {
            UserMeta::create([
                'user_id' => $user_id,
                'tag' => $tag,
                'meta_key' => $key,
                'meta_value' => $value,
            ]);
            return true;
        }

        return true;
    }
}

if (!function_exists('languagelevel')) {
    function languagelevel($level)
    {
        if ($level > 0 && $level <= 20) {
            return '<i class=" bx bxs-star golden_star"></i><i class="bx bx-star golden_star"></i><i class="bx bx-star golden_star"></i><i class="bx bx-star golden_star"></i><i class="bx bx-star golden_star"></i>';
        } else if ($level > 20 && $level <= 40) {
            return '<i class=" bx bxs-star golden_star"></i><i class=" bx bxs-star golden_star"></i><i class="bx bx-star golden_star"></i><i class="bx bx-star golden_star"></i><i class="bx bx-star golden_star"></i>';
        } else if ($level > 40 && $level <= 60) {
            return '<i class=" bx bxs-star golden_star"></i><i class=" bx bxs-star golden_star"></i><i class=" bx bxs-star golden_star"></i><i class="bx bx-star golden_star"></i><i class="bx bx-star golden_star"></i>';
        } else if ($level > 60 && $level <= 80) {
            return '<i class=" bx bxs-star golden_star"></i><i class=" bx bxs-star golden_star"></i><i class=" bx bxs-star golden_star"></i><i class=" bx bxs-star golden_star"></i><i class="bx bx-star golden_star"></i>';
        } else if ($level > 80 && $level <= 100) {
            return '<i class=" bx bxs-star golden_star"></i><i class=" bx bxs-star golden_star"></i><i class=" bx bxs-star golden_star"></i><i class=" bx bxs-star golden_star"></i><i class=" bx bxs-star golden_star"></i>';
        } else {
            return '<i class="bx bx-star golden_star"></i><i class="bx bx-star golden_star"></i><i class="bx bx-star golden_star"></i><i class="bx bx-star golden_star"></i><i class="bx bx-star golden_star"></i>';
        }

    }

}

if (!function_exists('shortNameGenerator')) {
    function shortNameGenerator($name)
    {
        // if $name is 3 or more words then return the first letter of each word
        $words = explode(' ', $name);
        if (count($words) >= 3) {
            $shortName = '';
            foreach ($words as $word) {
                $shortName .= $word[0];
            }
            return $shortName;
        } else {
            return $name;
        }

    }
}

if (!function_exists('checkOAuthTokenExists')) {
    function checkOAuthTokenExists($userId, $type = "google")
    {
        return UserOAuthToken::where("user_id", $userId)->where("type", $type)->exists();
    }
}

if (!function_exists('imageReplacer')) {
    function imageReplacer($path)
    {

        $path = str_replace('thumb_', '_custom', $path);
        return $path;

    }
}

if (!function_exists('emaarlocationIdentifier')) {
    function emaarlocationIdentifier($name)
    {
        $community = Community::where('name', $name)->orWhere('alternate_name', $name)->first();
        if ($community) {
            $data = [
                'type' => 'community',
                'id' => $community->id,
            ];
            return $data;
        }
        $sub_community = SubCommunity::where('name', $name)->orWhere('alternate_name', $name)->first();
        if ($sub_community) {

            $data = [
                'type' => 'sub_community',
                'id' => $sub_community->id,
            ];
            return $data;

        }
        $community_property = CommunityProperty::where('name', $name)->orWhere('alternate_name', $name)->first();
        if ($community_property) {
            $data = [
                'type' => 'community_property',
                'id' => $community_property->id,
            ];
            return $data;
        } else {
            return 'Nothing Found';
        }

    }
}

if (!function_exists('revampedEmaarlocationIdentifier')) {
    function revampedEmaarlocationIdentifier($name)
    {
        $location = json_decode($name);

        $building_name = $location->building;
        $location_name = $location->location;
        $property_name = $location->property;

        // ----- finding building name in all Location-------
        $building = CommunityProperty::where('name', $building_name)->orWhere('alternate_name', $building_name)->first();
        if ($building) {
            $data = [
                'type' => 'community_property',
                'community_property_id' => $building->id,
                'sub_community_id' => $building->sub_community_id,
                'community_id' => $building?->sub_community?->community_id,
            ];
            return $data;
        }
        $building = SubCommunity::where('name', $building_name)->orWhere('alternate_name', $building_name)->first();
        if ($building) {
            $data = [
                'type' => 'sub_community',
                'community_property_id' => null,
                'sub_community_id' => $building->id,
                'community_id' => $building?->community_id,
            ];
            return $data;
        }
        $building = Community::where('name', $building_name)->orWhere('alternate_name', $building_name)->first();
        if ($building) {
            $data = [
                'type' => 'community',
                'community_property_id' => null,
                'sub_community_id' => null,
                'community_id' => $building?->id,
            ];
            return $data;
        }

        // ----- finding location name in all Location-------
        $location = CommunityProperty::where('name', $location_name)->orWhere('alternate_name', $location_name)->first();
        if ($location) {
            $data = [
                'type' => 'community_property',
                'community_property_id' => $location->id,
                'sub_community_id' => $location->sub_community_id,
                'community_id' => $location?->sub_community?->community_id,
            ];
            return $data;
        }
        $location = SubCommunity::where('name', $location_name)->orWhere('alternate_name', $location_name)->first();
        if ($location) {
            $data = [
                'type' => 'sub_community',
                'community_property_id' => null,
                'sub_community_id' => $location->id,
                'community_id' => $location?->community_id,
            ];
            return $data;
        }
        $location = Community::where('name', $location_name)->orWhere('alternate_name', $location_name)->first();
        if ($location) {
            $data = [
                'type' => 'community',
                'community_property_id' => null,
                'sub_community_id' => null,
                'community_id' => $location->id,
            ];
            return $data;
        }

        // ----- finding property name in all Location-------
        $property = CommunityProperty::where('name', $property_name)->orWhere('alternate_name', $property_name)->first();
        if ($property) {
            $data = [
                'type' => 'community_property',
                'community_property_id' => $property->id,
                'sub_community_id' => $property->sub_community_id,
                'community_id' => $property?->sub_community?->community_id,
            ];
            return $data;
        }
        $property = SubCommunity::where('name', $property_name)->orWhere('alternate_name', $property_name)->first();
        if ($property) {
            $data = [
                'type' => 'SubCommunity',
                'community_property_id' => null,
                'sub_community_id' => $property->id,
                'community_id' => $property?->community_id,
            ];
            return $data;
        }
        $property = Community::where('name', $property_name)->orWhere('alternate_name', $property_name)->first();
        if ($property) {
            $data = [
                'type' => 'Community',
                'community_property_id' => null,
                'sub_community_id' => null,
                'community_id' => $property->id,
            ];
            return $data;
        }

        return 'NOT FOUND';
    }

}

if (!function_exists('processUnitNo')) {
    function processUnitNo($unit)
    {

        $unit = explode(' ', $unit);
        $unit = $unit[count($unit) - 1];
        return $unit;
    }
}

if (!function_exists('descriptionRewriter')) {
    function descriptionRewriter($description, $developer, $project_name)
    {
        try {
            $apiKey = env('CHAT_API');
            $apiUrl = 'https://api.openai.com/v1/chat/completions';
            $message = $description . '. Rewrite this. Maximum character limit is 600 - 700 characters do not increase this. Do not use any symbol or special character.';
            $response = Http::timeout(300)->withHeaders([
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
            // info($responseData);
            $msg = $responseData['choices'][0]['message']['content'];
            $msg = str_replace('"', '', $msg);

            $msg = str_replace('+', '|', $msg);
            $msg = str_replace('&', 'and', $msg);

            return $msg;
        } catch (\Throwable $th) {
            info($th);
            return $description;
        }

    }
}

if (!function_exists('titleGenerator')) {
    function titleGenerator($title, $developer_id, $project_name)
    {
        try {
            $developer = Developer::find($developer_id);
            $apiKey = env('CHAT_API');
            $apiUrl = 'https://api.openai.com/v1/chat/completions';

            $message = $title . 'Project name is' . $project_name . ' by developer ' . $developer->full_name . '. Rewrite the following title for property listing title for AX Capital company not more than 50 characters and not less than 40. Minimum 4 words. Do not use any symbol or special character. Just use | do not use - , +, & or any special characters in the titles  in title';

            $response = Http::timeout(300)->withHeaders([
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
            $msg = $responseData['choices'][0]['message']['content'];
            $msg = str_replace('"', '', $msg);

            $msg = str_replace('-', '|', $msg);
            $msg = str_replace('+', '|', $msg);
            $msg = str_replace('&', 'and', $msg);
            $msg = str_replace(':', '|', $msg);
            $msg = str_replace(';', '|', $msg);
            return $msg;
        } catch (\Throwable $th) {
            info($th);
            $title = str_replace('-', '|', $title);
            $title = str_replace('+', '|', $title);
            $title = str_replace('&', 'and', $title);
            $title = str_replace(':', '|', $title);
            $title = str_replace(';', '|', $title);

            return $title;
        }

    }
}

if (!function_exists('processDescription')) {

    function processDescription($id, $developer_id)
    {
        $developer = Developer::find($developer_id);
        $offplan = OffplanProperty::find($id);
        $devproj = DeveloperProject::where('developer_id', $developer->id)->where('project_name', $offplan->project_name)->first();
        $project_description = $devproj->description ?? 'Project Name is ' . $offplan->project_name . ' By ' . $developer->full_name . '.';

        $description = descriptionRewriter($project_description, $developer->full_name, $offplan->project_name);
        $process_description = $description;
        $amenities = $devproj?->amenities ?? "\nAMENITIES:\n- Infinity pool\n- Wet deck\n- Shaded pool deck\n- Sun loungers\n- Shaded kids’ pool area and play area\n- Raised lawn\n- Shaded communal area\n- Ground level boutiques and cafes\n";
        $features = $devproj?->additional_description ?? "FEATURES:\n- Multiple Units Available\n- 1BR - 4BR bedroom apartments\n- 4BR – 6BR bedroom penthouses\n- BUA ranging from 4,462 sq. ft – 11,683 sq. ft.\n- Private balconies\n";
        $payment_plans = $devproj?->payment_plans ?? "PAYMENT PLANS:\n- 10% On Booking\n- 90% on construction\n- 10% on completion\n- Handover in July 2028\n";

        $process_description .= "\n\n" . $amenities;
        $process_description .= "\n\n" . $features;
        $process_description .= "\n\n" . $payment_plans;

        $fixed_text = $devproj->signature ?? "\nAX CAPITAL was built upon a desire to create a real estate agency of the modern era where the customer is central to everything we do. With our values clearly defined, we are committed tofoundation of AX CAPITAL and makes us different.\n\nExplore Our Services:\n-AX MANAGEMENT\n-AX DESIGN\n-AX CORPORATE\n-AX HOLIDAYS";

        $process_description .= "\n" . $fixed_text;
        return $process_description;

    }
}

if (!function_exists('simpleDescriptionWriter')) {

    function simpleDescriptionWriter($id, $developer_id)
    {
        $developer = Developer::find($developer_id);
        $offplan = OffplanProperty::find($id);
        $devproj = DeveloperProject::where('developer_id', $developer->id)->where('project_name', $offplan->project_name)->first();
        $project_description = $devproj->description ?? 'Project Name is ' . $offplan->project_name . ' By ' . $developer->full_name . '.';

        // $description = descriptionRewriter($project_description, $developer->full_name, $offplan->project_name);
        $process_description = $project_description;
        $amenities = $devproj?->amenities ?? "\nAMENITIES:\n- Infinity pool\n- Wet deck\n- Shaded pool deck\n- Sun loungers\n- Shaded kids’ pool area and play area\n- Raised lawn\n- Shaded communal area\n- Ground level boutiques and cafes\n";
        $features = $devproj?->additional_description ?? "FEATURES:\n- Multiple Units Available\n- 1BR - 4BR bedroom apartments\n- 4BR – 6BR bedroom penthouses\n- BUA ranging from 4,462 sq. ft – 11,683 sq. ft.\n- Private balconies\n";
        $payment_plans = $devproj?->payment_plans ?? "PAYMENT PLANS:\n- 10% On Booking\n- 90% on construction\n- 10% on completion\n- Handover in July 2028\n";

        $process_description .= "\n\n" . $amenities;
        $process_description .= "\n\n" . $features;
        $process_description .= "\n\n" . $payment_plans;

        $fixed_text = $devproj->signature ?? "\n\nAX CAPITAL was built upon a desire to create a real estate agency of the modern era where the customer is central to everything we do. With our values clearly defined, we are committed tofoundation of AX CAPITAL and makes us different.\n\nExplore Our Services:\n-AX MANAGEMENT\n-AX DESIGN\n-AX CORPORATE\n-AX HOLIDAYS";

        $process_description .= "\n\n" . $fixed_text;
        return $process_description;

    }
}

if (!function_exists('getLeadStatusName')) {

    function getLeadStatusName($id)
    {
        $status = LeadStatus::find($id);
        return $status->name ?? 'N/A';
    }
}

if (!function_exists('getGroup')) {

    function getGroup($id)
    {
        $group = Group::find($id);

        return `<div class="content">
        <div class="d-flex">
            <div class="flex-shrink-0 ">
                <img src="` . $group->user->thumb_url . `" alt="" class="avatar-sm rounded-circle">
            </div>
            <div class="flex-grow-1  ms-3 my-auto">
                <h4 class="">` . $group->name . `  |<small class="text-muted fs-15 fw-normal">` . $group->user->full_name . `</small></h4>
            </div>
        </div>
    </div>`;

    }
}

if (!function_exists('getSubGroups')) {

    function getSubGroups($id)
    {

        return User::where('status', 1)->whereHas("roles", fn($q) => $q->whereNotIn("name", ["Agent"]))->where('parent_id', $id)->get();

    }
}

if (!function_exists('getGroupName')) {

    function getGroupName($name, $counter)
    {
        return $name . '-' . getAlphabetByNumber($counter);

    }
}

if (!function_exists('getAlphabetByNumber')) {

    function getAlphabetByNumber($number)
    {
        if ($number < 1 || $number > 26) {
            return null; // Handle out-of-range numbers as needed.
        }
        $alphabetLetter = chr(64 + $number);

        return $alphabetLetter;
    }
}

if (!function_exists('countDirectChildren')) {

    function countDirectChildren($id)
    {
        return User::where('status', true)->where('parent_id', $id)->count();
    }
}

if (!function_exists('parentFinder')) {

    function parentFinder($string)
    {
        // check how my many commas are in the string
        $count = substr_count($string, ',');
        if ($count == 0) {
            return null;
        }
        // if there is only one comma then get the first word after the comma
        if ($count == 1) {
            $parts = explode(',', $string);
            return $parts[1];
        }
        // if there are 2 commas then get the first word after the second comma
        if ($count == 2) {
            $parts = explode(',', $string);
            return $parts[2];
        }

    }

}
if (!function_exists('reportTemplate')) {

    function reportTemplate($agent_name, $agent_image, $count)
    {
        $template = `
        <li class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0">
                                                <img src="` . $agent_image . `" alt=""
                                                    class="avatar-xs rounded" />
                                            </div>
                                            <div class="flex-shrink-0 ms-2">
                                                <h6 class="fs-14 mb-0">` . $agent_name . `</h6>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="text">` . $count . `</span>
                                    </div>
                                </div>
                            </li>`;
        return $template;
    }
}

if (!function_exists('teamFinder')) {

    function teamFinder($string)
    {
        // return the first word before the first comma
        $parts = explode(',', $string);
        return $parts[0];

    }
}
if (!function_exists('removeUnderScore')) {
    function removeUnderScore($str)
    {
        return ucwords(str_replace('_', ' ', $str));
    }
}

if (!function_exists('priceFormatter')) {
    function priceFormatter($str)
    {
        return number_format($str, 0, '.', ',');
    }
}

if (!function_exists('emojiDecider')) {
    function emojiDecider($str)
    {
        switch ($str) {
            case 'Residential for sale':
                return '🔴';
            case 'Commercial for sale':
                return '🔴';
            case 'Commercial for rent':
                return '🟢';
            case 'Residential for rent':
                return '🟢';
            default:
                return '🔴';
        }
    }
}

if (!function_exists('removeUnderScoreAndCapitalize')) {
    function removeUnderScoreAndCapitalize($str)
    {
        return ucwords(str_replace('_', ' ', $str));
    }
}

if (!function_exists('containsUrl')) {
    function containsUrl($str)
    {
        // check if key contains _url
        if (strpos($str, '_url') !== false) {
            return true;
        }
        return false;
    }
}

if (!function_exists('getPercenatge')) {
    function getPercenatge($total, $value)
    {
        $per = ($value / $total) * 100;
        return number_format($per, 2);
    }

}

if (!function_exists('roundOff')) {
    function roundOff($number)
    {

        return round($number);
    }

}
if (!function_exists('processStatus')) {
    function processStatus($number)
    {

        if ($number == 1) {
            return `<span class="badge bg-success">Processed</span>`;
        }
        if ($number == 2) {
            return `<span class="badge bg-success">In Progress</span>`;
        }
        if ($number == 0) {
            return `<span class="badge bg-success">Pending</span>`;
        }
        return `<span class="badge bg-success">Pending</span>`;
    }

}

if (!function_exists('getKpiModel')) {
    function getKpiModel($kpi)
    {
        $leads = ['New Lead', 'Successful Leads','offers', 'leads'];

        if (in_array($kpi, $leads)) {
            return "Lead";
        }
        $listingLeads = ['listing_leads'];
        if (in_array($kpi, $listingLeads)) {
            return "ListingLead";
        }

        $listings = ['New Listing', 'Total Listings', 'Tru Check', 'Tru Check Ready', 'Tru Check Offplan', 'PF Verified', 'PF Verified Ready', 'PF Verified Offplan','listings', 'new_listings', 'live_listings'];
        if (in_array($kpi, $listings)) {
            return "Listing";
        }

        $messages = ['WhatsApp Messages', 'Successful WhatsApp Messages'];
        if (in_array($kpi, $messages)) {
            return "Message";
        }

        $meetings = ['Total Meetings', 'Total Viewings', 'Total Event','viewings', 'meetings', 'follow-up'];
        if (in_array($kpi, $meetings)) {
            return "Event";
        }

        $calls = ['Call Logs', 'Connected Call Logs', 'Outgoing Call Logs', 'Incoming Call Logs', 'Missed Call Logs', 'Incoming Missed Call Logs', 'Outgoing Missed Call Logs'];
        if (in_array($kpi, $calls)) {
            return "Call";
        }
        $deals = ['rental_deals','sales_deals','gross_revenue','avg_rental_commission','avg_sales_commission','avg_rental_revenue', 'avg_sales_revenue', 'deals', 'sales_target'];
        if (in_array($kpi, $deals)) {
            return "Deal";
        }
        $portal_credits = ['bayut_credit_limit','dubizzle_credit_limit','pf_credit_limit'];
        if (in_array($kpi, $portal_credits)) {
            return "Listing";
        }
        $targets = ['pipelines'];
        if (in_array($kpi, $targets)) {
            return "Pipeline";
        }



    }
}
if (!function_exists('getKpiFullText')) {
    function getKpiFullText($kpi)
    {
        $arr = [
            'avg_company_commission' => 'Avg. Company Commission',
            'company_revenue' => 'Company Revenue',
            'rental_revenue' => 'Rental Revenue',
            'sales_revenue' => 'Sales Revenue',
            'rental_commission' => 'Rental Commission',
            'sales_commission' => 'Sales Commission',
            'avg_gross_commission' => 'Avg. Gross Commission',
            'total_deals' => 'Total Deals',
            'rental_deals' => 'Rental Deals',
            'sales_value' => 'Sales Value',
            'sales_deals' => 'Sales Deals',
            'sales_deal_count' => 'Sales Deal Count',
            'rental_deals_count' => 'Rental Deal Count',
            'gross_revenue' => 'Gross Revenue',
            'avg_rental_commission' => 'Average Rental Commission',
            'avg_sales_commission' => 'Average Sales Commission',
            'avg_rental_revenue' => 'Average Rental Revenue',
            'avg_sales_revenue' => 'Average Sales Revenue',
            'average_transaction_value' => 'Average Transaction Value',
            'avg_gross_commission_amount' => 'Average Gross Commission Amt.',
            'gross_commission_amount' => 'Gross Commission Amount.',
            'pipelines' => 'Pipelines',
            'sales_target' => 'Sales Target',
            'live_listings' => 'Live Listings',
            'new_listings' => 'New Listings',
            'tru_broker' => 'Tru Broker',
            'super_agent' => 'Super Agent',
            'pf_credit_limit' => 'PF Credit Limit',
            'bayut_credit_limit' => 'Bayut Credit Limit',
            'dubizzle_credit_limit' => 'Dubizzle Credit Limit',
        ];
        return $arr[$kpi] ?? '';
    }
}
if (!function_exists('getProgressbarBgColor')) {
    function getProgressbarBgColor($percentage)
    {
        if ($percentage >= 75) {
            return "success";
        } else if ($percentage >= 50) {
            return "warning";
        } else {
            return "danger";
        }
    }

}

if (!function_exists('urlShortner')) {

    function urlShortner($url)
    {
        try {

            $client = new Client();
            $response = $client->request('GET', env('SHORTNER_BASE_URL', ''), [
                'query' => [
                    'username' => env('SHORTNER_USERNAME', ''),
                    'password' => env('SHORTNER_PASSWORD', ''),
                    'action' => 'shorturl',
                    'format' => 'json',
                    'url' => $url,
                ],
            ]);

            $response = json_decode($response->getBody()->getContents(), true);
            return $response ? $response['shorturl'] : '';
        } catch (\Throwable $th) {
            Log::error("Url Shortner Error " . $th->getMessage());
            return "";
        }

    }
}

if (!function_exists('AXurlShortner')) {

    function AXurlShortner($url)
    {

        try {
            $slug = rand(10, 999) . '-' . generateAlpha(5) . '-' . rand(10, 999);
            $shortlink = route('public.smsShortlink', $slug);
            $client = new Shortlink();

            $client->slug = $slug;
            $client->link = $url;
            $client->short_link = $shortlink;

            $client->save();

            return $shortlink;
        } catch (\Throwable $th) {
            dd("Url Shortner Error " . $th->getMessage());
            return "";
        }

    }
}

if (!function_exists('calculatePercentage')) {
    function calculatePercentage($count, $total)
    {
        return round($total > 0 ? ($count / $total) : 0, 2);
    }
}

if (!function_exists('formatNumberMetricPrefixes')) {

    function formatNumberMetricPrefixes($number)
    {
        $number = (float) $number;
        if ($number >= 1000000) {
            return round($number / 1000000, 2) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 2) . 'K';
        } else {
            return $number;
        }
    }
}

if (!function_exists('sendNotificationToMobile')) {

    function sendNotificationToMobile($payload, $flag)
    {
        try {

            $accessToken = getAccessToken();
            // info('Access Token ' . $accessToken);
            $serverKey = 'AAAA_OxfTQE:APA91bE22wzYji8aEerbkVtrYsbtJFoxM-spY1PTFWw1sf0jo0utgl06sJsvayFuyNo772o1TYjiLz44XcRr1JrrUJ9yvmsDVHqmBgVFa_B_l4uPXpG_tnUIk_BBUuYMjHAGSyvPfxvs';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/v1/projects/axsecondaryapp-5afbd/messages:send', $payload);

            $user = UserFcm::where('fcm_token', $payload['message']['token'])->first();

            $notification = new MobileNotification();
            $notification->payload = json_encode($payload);
            $notification->user_id = $user->user_id;
            $notification->title = $payload['message']['notification']['title'];
            $notification->body = $payload['message']['notification']['body'];
            $notification->token = $payload['message']['token'];
            $notification->save();

            return $response->json();
        } catch (\Throwable $th) {
            info($th);
            return $th->getMessage();
        }
    }
}
if (!function_exists('getAccessToken')) {

    function getAccessToken()
    {
        // Path to your service account JSON file
        $keyFilePath = public_path('api/secondary.json');

        // Initialize the Google Client
        $client = new Google_Client();
        $client->setAuthConfig($keyFilePath);
        $client->addScope('https://www.googleapis.com/auth/cloud-platform');

        // Fetch the access token
        $accessToken = $client->fetchAccessTokenWithAssertion();

        if (isset($accessToken['access_token'])) {
            return $accessToken['access_token'];
        } else {
            throw new \Exception('Failed to generate access token');
        }
    }
}

if (!function_exists('getUserLoggedOut')) {
    function getUserLoggedOut($user_id)
    {
        $fcms = UserFcm::where('user_id', $user_id)->get();
        if ($fcms->count() > 0) {
            foreach ($fcms as $fcm) {
                $deviceToken = $fcm->fcm_token;
                $title = 'AX Secondary CRM';

                $data = [
                    'title' => (string) 'User Logged Out',
                    'message' => (string) 'User Logged Out',
                    'type' => "Test",
                    'user_id' => (string) $user_id, // Convert to string if it's not already
                    'flag' => (string) 'Logout', // Convert to string if it's not already
                    'model' => (string) 'Logout', // Convert to string if it's not already
                    'model_id' => (string) $user_id, // Convert to string if it's not already
                ];

                $notification = [
                    'title' => 'User Logged Out',
                    'body' => 'User Logged Out',
                ];

                $payload = [
                    'message' => [
                        'token' => $deviceToken, // FCM registration token
                        'notification' => $notification, // Notification payload (title and body)
                        'data' => $data, // Custom data payload
                    ],
                ];

                $user = UserFcm::where('fcm_token', $payload['message']['token'])->first();

                $notification = new MobileNotification();
                $notification->payload = json_encode($payload);
                $notification->user_id = $user->user_id;
                $notification->title = $payload['message']['notification']['title'];
                $notification->body = $payload['message']['notification']['body'];
                $notification->token = $payload['message']['token'];
                $notification->save();
                // info($user_id . ' Sending Payload', $payload);
                $function = sendNotificationToMobile($payload, $flag = "Test");
            }
        }

    }
}

if (!function_exists('makeGetRequest')) {
    function makeGetRequest($url)
    {
        // Array of common User-Agent strings
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Safari/605.1.15',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:85.0) Gecko/20100101 Firefox/85.0',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36 Edg/90.0.818.51',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_4_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Linux; Android 11; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.105 Mobile Safari/537.36',
            'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:88.0) Gecko/20100101 Firefox/88.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.159 Safari/537.36',
        ];

        // Randomly select a User-Agent
        $randomUserAgent = $userAgents[array_rand($userAgents)];

        try {
            // Define headers with the random User-Agent
            $headers = [
                'User-Agent' => $randomUserAgent,
                'Accept' => 'application/json', // Adjust if you expect a different content type
            ];

            $response = Http::withHeaders($headers)
                ->timeout(10) // Timeout after 10 seconds
                ->retry(3, 100) // Retry up to 3 times, with 100ms delay between retries
                ->get($url);

            // Check for a successful response
            if ($response->successful()) {
                return $response->json(); // Or use $response->body() if you're expecting plain text
            } else {
                // Log and handle any non-successful response
                Log::error("GET request to {$url} failed with status code: " . $response->status());
                return null;
            }

        } catch (Exception $e) {
            // Log the exception and return null
            Log::error("Error during GET request to {$url}: " . $e->getMessage());
            return null;
        }
    }

}

if (!function_exists('propertyLiveCounter')) {
    function propertyLiveCounter($user_id, $portal_id)
    {
        $user = User::find($user_id);
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds($user_id);
        }

        $portal = App\Models\Portal::find($portal_id);
        $properties = Property::whereNot('completion_status', 'Off-Plan Primary')
            ->where('region_id', 1)
            ->whereHas('portals', function ($q) use ($portal) {
                $q->where('portal_id', $portal->id);
            })
            ->where('is_live', '1')
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count();

        return $properties;

    }
}

if (!function_exists('propertyCounter')) {
    function propertyCounter($user_id, $portal)
    {
        $user = User::find($user_id);
        $notAdmin = $user->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds($user_id);
        }
        if (is_numeric($portal)) {
            $portal = App\Models\Portal::where('id', $portal)->first();
        } else {
            $portal = App\Models\Portal::where('name', $portal)->first();
        }

        $properties = Property::whereNot('completion_status', 'Off-Plan Primary')
            ->where('region_id', 1)
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })
            ->where('is_live', '1')
            ->pluck('id');

        $propertyPortals = App\Models\PropertyPortal::whereIn('property_id', $properties)
            ->where('portal_id', $portal->id)
            ->count();

        return $propertyPortals;
    }
}

if (!function_exists('propertyTotalCounter')) {
    function propertyTotalCounter($user_id)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        // $portal = App\Models\Portal::find($portal_id);
        $properties = Property::whereNot('completion_status', 'Off-Plan Primary')
            ->where('region_id', 1)
            ->whereHas('portals')
            ->where('is_live', '1')
            ->when($notAdmin, function ($q) use ($childrenIds) {
                $q->whereIn("agent_id", $childrenIds);
            })->count();

        return $properties;
    }



}

if (!function_exists('telegramNotification')) {
    function telegramNotification($model, $id,$chat_id, $user_id, $msg)
    {
        $not = new TelegramNotification();
        $not->model = $model;
        $not->model_id = $id;
        $not->chat_id = $chat_id;
        $not->user_id = $user_id;
        $not->message = $msg;
        $not->user_status = 1;
        $not->type = 'Notification';
        $not->save();
        return true;
    }
}
if (!function_exists('getDaysInMonth')) {
function getDaysInMonth($dateString) {
    // 2024-12
    if (!preg_match('/^\d{4}-\d{2}$/', $dateString)) {
        return false;
    }
    $date = new DateTime($dateString);
    $daysInMonth = $date->format('t');

    return $daysInMonth;
}
}


if (!function_exists('endsWithNumberInBrackets')) {

 function endsWithNumberInBrackets($message)
{
    $hasSec = stripos($message, 'Sec-') !== false; // Check for "Sec-"
    $hasNumberInBrackets = preg_match('/\(\d+\)/', $message); // Check for (number)

    // Return true only if both conditions are met
    return $hasSec && $hasNumberInBrackets;
}
}

if (!function_exists('getKpiReportIcon')) {

    function getKpiReportIcon($key)
    {
        if ($key == "live_listings" || $key == "new_listings"){
            return "ri-home-line";
        }
        if ($key == "sales_target"){
            return "ri-bank-card-line";
        }
        if ($key == "offers"){
            return "ri-bank-card-line";
        }
        if ($key == "calls"){
            return "ri-phone-line";
        }
        if ($key == "leads"){
            return "ri-account-box-line";
        }
        if ($key == "deals"){
            return "ri-bank-card-2-line";
        }
        if ($key == "viewings"){
            return "ri-eye-line";
        }
        if ($key == "meetings"){
            return "ri-group-line";
        }
        if ($key == "follow-up"){
            return "ri-calendar-line";
        }
        return "ri-earth-line";
    }

    function formatSeconds($seconds) {
        if ($seconds < 0) {
            return "Invalid input"; // Handle negative values
        }

        $hours = floor($seconds / 3600); // Calculate hours
        $seconds %= 3600; // Remove hours from seconds
        $minutes = floor($seconds / 60); // Calculate minutes
        $seconds %= 60; // Remove minutes from seconds

        $result = '';
        if ($hours > 0) {
            $result .= $hours . 'hr'; // Append hours
        }
        if ($minutes > 0) {
            $result .= $minutes . 'm'; // Append minutes
        }
        if ($seconds > 0) {
            $result .= $seconds . 's'; // Append seconds
        }

        return $result ?: '0s'; // Return '0s' if input is 0
    }
    ///to get data from UserMeta

    function getUserMetaValue($user_id,$key)
    {
        try {
            $userMeta = UserMeta::where('user_id', $user_id)->where('meta_key', $key)->first();
            return $userMeta->meta_value ?? '';
        }catch (\Exception $e){
            return '';
        }
    }

    function calculateValuesAverages($achievedPercentages) {
        //ex. parameter ['10','20','30','40','50']
        $total = array_sum($achievedPercentages);

        if ($total == 0) {
            return 0;
        }
        return number_format($total / count($achievedPercentages), 2, '.', '');
    }
//    function getBgColorBasedOnPercentage($percentage) {
//        if ($percentage >= 75) {
//            return "success";
//            } else if ($percentage >= 50) {
//            return "warning";
//            } else {
//            return "danger";
//            }
//    }
}

if (!function_exists('getSqlQuery')) {
    function getSqlQuery($query, $includeBindings = false)
    {
        $querySql = $query->toSql();

        if ($includeBindings) {
            $queryBindings = $query->getBindings();
            $querySql = vsprintf(str_replace('?', "'%s'", $querySql), $queryBindings);
        }

        return $querySql;
    }
}
function getPfCreditDurationDates($today = null)
{
    $today = $today ? Carbon::parse($today) : Carbon::today();

    if ($today->lte(Carbon::parse("2025-06-25"))) {
        if ($today->day >= 26) {
            // Start date: 26th of this month
            $start_date = $today->copy()->startOfMonth()->addDays(25); // 1st + 25 days = 26th
            // End date: 25th of next month
            $end_date = $today->copy()->addMonth()->startOfMonth()->addDays(24); // 1st of next month + 24 days = 25th
        } else {
            // Start date: 26th of last month
            $start_date = $today->copy()->subMonth()->startOfMonth()->addDays(25);
            // End date: 25th of this month
            $end_date = $today->copy()->startOfMonth()->addDays(24);
        }
    }else{
        if ($today->day >= 3) {
            // Start date: 3rd of this month
            $start_date = $today->copy()->startOfMonth()->addDays(2); // 1st + 2 days = 3rd
            // End date: 2nd of next month
            $end_date = $today->copy()->addMonth()->startOfMonth()->addDays(1); // 1st of next month + 1 day = 2nd
        } else {
            // Start date: 3rd of last month
            $start_date = $today->copy()->subMonth()->startOfMonth()->addDays(2);
            // End date: 2nd of this month
            $end_date = $today->copy()->startOfMonth()->addDays(1);
        }
    }

    return [
        'start_date' => $start_date->toDateString(),
        'end_date' => $end_date->toDateString(),
    ];
}
//function getPfCreditDurationDates($today = null)
//{
//    $today = $today ? Carbon::parse($today) : Carbon::today();
//
//    if ($today->day >= 26) {
//        // Start date: 26th of this month
//        $start_date = $today->copy()->startOfMonth()->addDays(25); // 1st + 25 days = 26th
//        // End date: 25th of next month
//        $end_date = $today->copy()->addMonth()->startOfMonth()->addDays(24); // 1st of next month + 24 days = 25th
//    } else {
//        // Start date: 26th of last month
//        $start_date = $today->copy()->subMonth()->startOfMonth()->addDays(25);
//        // End date: 25th of this month
//        $end_date = $today->copy()->startOfMonth()->addDays(24);
//    }
//
//    return [
//        'start_date' => $start_date->toDateString(),
//        'end_date' => $end_date->toDateString(),
//    ];
//}

function getBayutCreditDurationDates($today = null)
{
    $today = $today ? Carbon::parse($today) : Carbon::today();

    if ($today->lte(Carbon::parse("2025-06-20"))) {
        if ($today->day >= 21) {
            // Start date: 21th of this month
            $start_date = $today->copy()->startOfMonth()->addDays(20); // 1st + 20 days = 21th
            // End date: 20th of next month
            $end_date = $today->copy()->addMonth()->startOfMonth()->addDays(19); // 1st of next month + 24 days = 20th
        } else {
            // Start date: 21th of last month
            $start_date = $today->copy()->subMonth()->startOfMonth()->addDays(20);
            // End date: 20th of this month
            $end_date = $today->copy()->startOfMonth()->addDays(19);
        }
    }else{
        if ($today->day >= 27) {
            // Start date: 27th of this month
            $start_date = $today->copy()->startOfMonth()->addDays(26); // 1st + 26 days = 27th
            // End date: 26th of next month
            $end_date = $today->copy()->addMonth()->startOfMonth()->addDays(25); // 1st of next month + 25 days = 26th
        } else {
            // Start date: 27th of last month
            $start_date = $today->copy()->subMonth()->startOfMonth()->addDays(26);
            // End date: 26th of this month
            $end_date = $today->copy()->startOfMonth()->addDays(25);
        }
    }

    return [
        'start_date' => $start_date->toDateString(),
        'end_date' => $end_date->toDateString(),
    ];
}
//function getBayutCreditDurationDates($today = null)
//{
//    $today = $today ? Carbon::parse($today) : Carbon::today();
//
//    if ($today->day >= 21) {
//        // Start date: 21th of this month
//        $start_date = $today->copy()->startOfMonth()->addDays(20); // 1st + 20 days = 21th
//        // End date: 20th of next month
//        $end_date = $today->copy()->addMonth()->startOfMonth()->addDays(19); // 1st of next month + 24 days = 20th
//    } else {
//        // Start date: 21th of last month
//        $start_date = $today->copy()->subMonth()->startOfMonth()->addDays(20);
//        // End date: 20th of this month
//        $end_date = $today->copy()->startOfMonth()->addDays(19);
//    }
//
//    return [
//        'start_date' => $start_date->toDateString(),
//        'end_date' => $end_date->toDateString(),
//    ];
//}

function getExemptedAgentIdsFromLeadsTransfer()
{
    return [
        232, //Darya Grechanik
    ];
}

function getTelelogEntities()
{
    return [
        "AX Secondary",
        "Primary",
        "Back Office",
        "AX DESIGN"
    ];
}


