<?php

use App\Models\Bath;
use App\Models\Bed;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\SubCommunity;
use App\Models\User;


function returnRentalDocumentList($for = "Other", $isCompany = false)
{
    if ($for == "Contact") {
        $contactDocuments = [
            [
                'id' => 'Passport, Visa or EID',
                'name' => 'Passport, Visa or EID',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ],
            [
                'id' => 'Title Deed',
                'name' => 'Title Deed',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ]
        ];
        if ($isCompany) {
            $companyDocuments = [
                [
                    'id' => 'Trade License',
                    'name' => 'Trade License',
                    'extensions' => 'pdf',
                    'size' => '3MB',
                    'required' => true,
                ],
                [
                    'id' => 'Authorized Signatory PP, Visa, EID',
                    'name' => 'Authorized Signatory PP, Visa, EID',
                    'extensions' => 'pdf',
                    'size' => '3MB',
                    'required' => true,
                ],
                [
                    'id' => 'POA (if applicable)',
                    'name' => 'POA (if applicable)',
                    'extensions' => 'pdf',
                    'size' => '3MB',
                    'required' => false,
                ],
            ];
            return array_merge($contactDocuments, $companyDocuments);
        }
        return $contactDocuments;
    }

    if ($for == "Lead") {
        $leadDocuments = [
            [
                'id' => 'Passport, Visa or EID',
                'name' => 'Passport, Visa or EID',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ],
        ];
        if ($isCompany) {
            $companyDocuments = [
                [
                    'id' => 'Trade License',
                    'name' => 'Trade License',
                    'extensions' => 'pdf',
                    'size' => '3MB',
                    'required' => true,
                ],
                [
                    'id' => 'Authorized Signatory PP, Visa, EID',
                    'name' => 'Authorized Signatory PP, Visa, EID',
                    'extensions' => 'pdf',
                    'size' => '3MB',
                    'required' => true,
                ],
                [
                    'id' => 'POA (if applicable)',
                    'name' => 'POA (if applicable)',
                    'extensions' => 'pdf',
                    'size' => '3MB',
                    'required' => false,
                ],
                [
                    'id' => 'KYC (if net comm is 50k above)',
                    'name' => 'KYC (if net comm is 50k above)',
                    'extensions' => 'pdf',
                    'size' => '3MB',
                    'required' => false,
                ],
            ];
            return array_merge($leadDocuments, $companyDocuments);
        }
        return $leadDocuments;
    }

    return [
        [
            'id' => 'Signed Tenancy Contract',
            'name' => 'Signed Tenancy Contract',
            'extensions' => 'pdf',
            'size' => '3MB',
            'required' => true,
        ],
        [
            'id' => 'Form I (A2A)',
            'name' => 'Form I (A2A)',
            'extensions' => 'pdf',
            'size' => '3MB',
            'required' => false,
        ],
        [
            'id' => 'Referral Form / Agreement',
            'name' => 'Referral Form / Agreement',
            'extensions' => 'pdf',
            'size' => '3MB',
            'required' => false,
        ],
    ];
}

function returnSecondarySaleDocumentList($for = "Other", $isCompany = false)
{
    if ($for == "Contact") {
        $contactDocuments = [
            [
                'id' => 'Form A',
                'name' => 'Form A',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => false,
            ],
            [
                'id' => 'Passport, Visa or EID',
                'name' => 'Passport, Visa or EID',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ],
            [
                'id' => 'Title Deed',
                'name' => 'Title Deed',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ],
        ];
        if ($isCompany) {
            $companyDocuments = [
                [
                    'id' => 'Trade License',
                    'name' => 'Trade License',
                    'extensions' => 'pdf',
                    'size' => '3MB',
                    'required' => true,
                ],
                [
                    'id' => 'Authorized Signatory PP, Visa, EID',
                    'name' => 'Authorized Signatory PP, Visa, EID',
                    'extensions' => 'pdf',
                    'size' => '3MB',
                    'required' => true,
                ],
                [
                    'id' => 'POA (if applicable)',
                    'name' => 'POA (if applicable)',
                    'extensions' => 'pdf',
                    'size' => '3MB',
                    'required' => false,
                ],
            ];
            return array_merge($contactDocuments, $companyDocuments);
        }
        return $contactDocuments;
    }

    if ($for == "Lead") {
        $leadDocuments = [
            [
                'id' => 'Form B',
                'name' => 'Form B',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => false,
            ],
            [
                'id' => 'Passport, Visa or EID',
                'name' => 'Passport, Visa or EID',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ],
            [
                'id' => 'KYC',
                'name' => 'KYC',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ],
        ];
        if ($isCompany) {
            $companyDocuments = [
                [
                    'id' => 'Trade License',
                    'name' => 'Trade License',
                    'extensions' => 'pdf',
                    'size' => '3MB',
                    'required' => true,
                ],
                [
                    'id' => 'Authorized Signatory PP, Visa, EID',
                    'name' => 'Authorized Signatory PP, Visa, EID',
                    'extensions' => 'pdf',
                    'size' => '3MB',
                    'required' => true,
                ],
                [
                    'id' => 'POA (if applicable)',
                    'name' => 'POA (if applicable)',
                    'extensions' => 'pdf',
                    'size' => '3MB',
                    'required' => false,
                ],
            ];
            return array_merge($leadDocuments, $companyDocuments);
        }
        return $leadDocuments;
    }

    return [
        [
            'id' => 'Form F',
            'name' => 'Form F',
            'extensions' => 'pdf',
            'size' => '3MB',
            'required' => true,
        ],
        [
            'id' => 'Form I (A2A) (if applicable)',
            'name' => 'Form I (A2A) (if applicable)',
            'extensions' => 'pdf',
            'size' => '3MB',
            'required' => false,
        ],
        [
            'id' => 'Referral Form / Agreement (if applicable)',
            'name' => 'Referral Form / Agreement (if applicable)',
            'extensions' => 'pdf',
            'size' => '3MB',
            'required' => false,
        ],
        [
            'id' => 'Compliance Declaration Form (if applicable)',
            'name' => 'Compliance Declaration Form (if applicable)',
            'extensions' => 'pdf',
            'size' => '3MB',
            'required' => false,
        ],
        [
            'id' => 'Receipts / Cheque Copies',
            'name' => 'Receipts / Cheque Copies',
            'extensions' => 'pdf',
            'size' => '3MB',
            'required' => false,
        ],
        [
            'id' => 'Invoice',
            'name' => 'Invoice',
            'extensions' => 'pdf',
            'size' => '3MB',
            'required' => false,
        ],
    ];
}

function returnPrimaryResaleDocumentList($for = "Other")
{
    if ($for == "Contact") {
        return [
            [
                'id' => 'Booking Form / SPA or any Initial Contract of Sale',
                'name' => 'Booking Form / SPA or any Initial Contract of Sale',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ],
        ];
    }

    if ($for == "Lead") {
        return [
            [
                'id' => 'Passport, Visa or EID',
                'name' => 'Passport, Visa or EID',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ],
            [
                'id' => 'Signed KYC',
                'name' => 'Signed KYC',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ],
        ];
    }

    return [];
}

if (!function_exists('getDealRequiredDocumentList')) {
    function getDealRequiredDocumentList($type = null, $for = "Other", $isCompany = false)
    {

        if ($type == "Rental") {
            return returnRentalDocumentList($for, $isCompany);
        }

        if ($type == "Secondary Sale") {
            return returnSecondarySaleDocumentList($for, $isCompany);
        }

        if ($type == "Primary Resale") {
            return returnPrimaryResaleDocumentList($for);
        }
        return [];
    }
}

if (!function_exists('getDealRequiredDocumentList_bkp')) {
    function getDealRequiredDocumentList_bkp($dealType = null, $type = null)
    {

        if ($dealType == "Rental") {
            if ($type == "Owner") {
                return [
                    [
                        'id' => 'Signed Tenancy Contract',
                        'name' => 'Signed Tenancy Contract',
                        'extensions' => 'pdf',
                        'size' => '3MB',
                        'required' => false,
                    ],
                    [
                        'id' => 'Passport, Visa or EID of Landlord',
                        'name' => 'Passport, Visa or EID of Landlord',
                        'extensions' => 'pdf',
                        'size' => '3MB',
                        'required' => true,
                    ],
                    [
                        'id' => 'Title Deed / Oqood / SPA',
                        'name' => 'Title Deed / Oqood / SPA',
                        'extensions' => 'pdf',
                        'size' => '3MB',
                        'required' => false,
                    ],
                ];
            }

            if ($type == "Tenant") {
                return [
                    [
                        'id' => 'Passport, Visa or EID of Tenant',
                        'name' => 'Passport, Visa or EID of Tenant',
                        'extensions' => 'pdf',
                        'size' => '3MB',
                        'required' => false,
                    ],
                    [
                        'id' => 'Passport, Visa or EID of Landlord',
                        'name' => 'Passport, Visa or EID of Landlord',
                        'extensions' => 'pdf',
                        'size' => '3MB',
                        'required' => true,
                    ],
                    [
                        'id' => 'Trade License if Tenant is a Company',
                        'name' => 'Trade License if Tenant is a Company',
                        'extensions' => 'pdf',
                        'size' => '3MB',
                        'required' => false,
                    ],
                    [
                        'id' => 'Passport, Visa or EID of the authorized signatory if Tenant is a Company',
                        'name' => 'Passport, Visa or EID of the authorized signatory if Tenant is a Company',
                        'extensions' => 'pdf',
                        'size' => '3MB',
                        'required' => false,
                    ],
                    [
                        'id' => 'Filled KYC Form',
                        'name' => 'Filled KYC Form',
                        'extensions' => 'pdf',
                        'size' => '3MB',
                        'required' => false,
                    ],
                ];
            }
        }

        if ($dealType == "Primary Sale") {
        }



        $primarySaleDocumentList = [
            [
                'id' => 'Buyers PP Copy',
                'name' => 'Buyers PP Copy',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ],
            [
                'id' => 'KYC Form',
                'name' => 'KYC Form',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ],
            [
                'id' => 'Booking / Reservation Form',
                'name' => 'Booking / Reservation Form',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ],
        ];

        $secondarySaleDocumentList = [
            [
                'id' => 'Signed Manual MOU or Form F with password',
                'name' => 'Signed Manual MOU or Form F with password',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ],
            [
                'id' => 'Form A and B with password',
                'name' => 'Form A and B with password',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ],
            [
                'id' => 'Passport, Visa or EID Seller',
                'name' => 'Passport, Visa or EID Seller',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => false,
            ],
            [
                'id' => 'Passport, Visa or EID Buyer',
                'name' => 'Passport, Visa or EID Buyer',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => false,
            ],
            [
                'id' => 'Title Deed / Oqood / SPA',
                'name' => 'Title Deed / Oqood / SPA',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ],
            [
                'id' => 'Trade License if Buyer is a Company',
                'name' => 'Trade License if Buyer is a Company',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ],
            [
                'id' => 'Passport, Visa or EID of the authorized signatory if Buyer is a Company',
                'name' => 'Passport, Visa or EID of the authorized signatory if Buyer is a Company',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => true,
            ],
            [
                'id' => 'Filled KYC Form',
                'name' => 'Filled KYC Form',
                'extensions' => 'pdf',
                'size' => '3MB',
                'required' => false,
            ],
        ];

        if ($dealType == "Rental") {
            return $rentalDocumentList;
        }

        if ($dealType == "Primary Sale") {
            return $primarySaleDocumentList;
        }

        if ($dealType == "Secondary Sale") {
            return $secondarySaleDocumentList;
        }


        return [];
    }
}

if (!function_exists('dealColumnsReadable')) {
    function dealColumnsReadable($json_data)
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
                    $val = User::where('id', $value)->pluck('name')->first() ?? "";
                } elseif ($key == 'community_ids') { // related to lead criteria
                    $readable_key = 'Communities';
                    $val = Community::whereIn('id', $value)->pluck('name') ?? "";
                } elseif ($key == 'sub_community_ids') {
                    $readable_key = 'Sub Communities';
                    $val = SubCommunity::whereIn('id', $value)->pluck('name') ?? "";
                } elseif ($key == 'community_properties_ids') {
                    $readable_key = 'Community Properties';
                    $val = CommunityProperty::whereIn('id', $value)->pluck('name') ?? "";
                } elseif ($key == 'lead_type') {
                    $readable_key = 'Lead Type';
                    $val = $value;
                } elseif ($key == 'commission_amount') {
                    $readable_key = 'Commission Amount';
                    $val = $value;
                } elseif ($key == 'commission_percentage') {
                    $readable_key = 'Commission Percentage';
                    $val = $value;
                } elseif ($key == 'commission_source') {
                    $readable_key = 'Commission Source';
                    $val = $value;
                } elseif ($key == 'is_vat_applicable') {
                    $readable_key = 'VAT Applicable';
                    $val = $value ? "Yes" : "No";
                } elseif ($key == 'vat_amount') {
                    $readable_key = 'VAT Amount';
                    $val = $value;
                } elseif ($key == 'vat_percentage') {
                    $readable_key = 'VAT Percentage';
                    $val = $value;
                } elseif ($key == 'split_amount') {
                    $readable_key = 'Split Amount';
                    $val = $value;
                } elseif ($key == 'split_percentage') {
                    $readable_key = 'Split Percentage';
                    $val = $value;
                } elseif ($key == 'commission_value') {
                    $readable_key = 'Commission Value';
                    $val = $value;
                } elseif ($key == 'commission_category') {
                    $readable_key = 'Commission Category';
                    $val = $value;
                } elseif ($key == 'commission_type') {
                    $readable_key = 'Commission Type';
                    $val = $value;
                } elseif ($key == 'commission') {
                    $readable_key = 'Commission';
                    $val = $value;
                } elseif ($key == 'vat_percentage') {
                    $readable_key = 'VAT Percentage';
                    $val = $value;
                } else {
                    $readable_key = $key;
                    $val = "";
                }

                $data[ucwords(str_replace("_", " ", $readable_key))] = $val ?? $value;
            }
            return $data;
        } else {
            return [];
        }
    }
}


if (!function_exists('dealTestingChatId')) {
    function dealTestingChatId()
    {
        return  \App\Models\GeneralSetting::where('slug', 'deal-telegram-chat-id')->first()->value ?? null;
    }
}
if (!function_exists('dealTestingAdminChatId')) {
    function dealTestingAdminChatId()
    {
        return  \App\Models\GeneralSetting::where('slug', 'deal-telegram-admin-chat-id')->first()->value ?? null;
    }
}
//*****Bitrix deal data and functions */

if(!function_exists('getBitrixDealStage')){
    function getBitrixDealStage()
    {
        /*
C2:PREPAYMENT_INVOICE - AML
*/
        $list = [
            'NEW' => 'Pending',
            '1' => 'Approved',
            '2' => 'SPA Signed',
            '3' => 'In Progress',
            '4' => 'Developer Invoiced',
            '5' => 'Comm Partially Received',
            '6' => 'Comm Received',
            'WON' => 'Close deal',
            'LOSE' => 'Deal lost',
            'C2:NEW' => 'Pending',
            'C2:PREPARATION' => 'In Progress',
            'C2:EXECUTING' => 'Approved',
            'C2:PREPAYMENT_INVOICE' => 'Prepayment Invoice or AML',
            'C2:STARTED' => 'Commenced',
            'C2:WORK_IN_PROGRESS' => 'Work in Progress',
            'C2:COMPLETED' => 'Completed',
            'C2:PREPAYMENT_RECEIVED' => 'Prepayment Received',
            'C2:INVOICE' => 'Invoice',
            'C2:PARTIALLY_PAID' => 'Partially Paid',
            'C2:PAID' => 'Paid',
            'C2:RECONCILED' => 'Reconciled',
            'C2:REOPENED' => 'Reopened',
            'C2:POSTPONED' => 'Postponed',
            'C2:CANCELLED' => 'Cancelled',
            'C2:FINAL_INVOICE' => 'Pending Transfer',
            'C2:5' => 'Invoice Issued',
            'C2:1' => 'Transfer Complteted',
            'C2:2' => 'Invoice Issued',
            'C2:3' => 'Commission Received',
            'C2:4' => 'Commission Partially Cleared',
            'C2:WON' => 'Close deal',
            'C2:LOSE' => 'Deal lost'
        ];
        return $list;
        // return $list[$stage] ?? $stage;
    }
}
if(!function_exists('getBitrixSourceList')){
    function getBitrixSourceList()
    {
        $list  = array(
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

        return $list;
    }
}
