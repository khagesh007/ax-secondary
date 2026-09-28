<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionTableSeederSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                "name" => "Dashboard",
                "group_name" => "Dashboard",
            ],
            //User
            [
                "name" => "Users List",
                "group_name" => "Users",
            ],
            [
                "name" => "Users Create",
                "group_name" => "Users",
            ],
            [
                "name" => "Users Edit",
                "group_name" => "Users",
            ],
            [
                "name" => "Users Delete",
                "group_name" => "Users",
            ],
            [
                "name" => "Users View",
                "group_name" => "Users",
            ],
            [
                "name" => "Users Tree View",
                "group_name" => "Users",
            ],
            [
                "name" => "Users View Permission",
                "group_name" => "Users",
            ],
            [
                "name" => "Users Change Permission",
                "group_name" => "Users",
            ],
            [
                "name" => "Users Logged In Filter",
                "group_name" => "Users",
            ],

            //Roles
            [
                "name" => "Roles List",
                "group_name" => "Roles",
            ],
            [
                "name" => "Roles Create",
                "group_name" => "Roles",
            ],
            [
                "name" => "Roles Edit",
                "group_name" => "Roles",
            ],
            [
                "name" => "Roles Delete",
                "group_name" => "Roles",
            ],
            [
                "name" => "Roles View",
                "group_name" => "Roles",
            ],

            //Permissions
            [
                "name" => "Permissions List",
                "group_name" => "Permissions",
            ],
            [
                "name" => "Permissions Create",
                "group_name" => "Permissions",
            ],
            [
                "name" => "Permissions Edit",
                "group_name" => "Permissions",
            ],
            [
                "name" => "Permissions Delete",
                "group_name" => "Permissions",
            ],
            [
                "name" => "Permissions View",
                "group_name" => "Permissions",
            ],

            //Traccar
            [
                "name" => "User Traccar Settings",
                "group_name" => "User Traccar",
            ],
            //App
            [
                "name" => "App Logs List",
                "group_name" => "App",
            ],

            //Listings
            [
                'name' => 'Properties Generate Social Media Post',
                'group_name' => 'Listings',
            ],
            [
                'name' => 'Properties Generate Brochure',
                'group_name' => 'Listings',
            ],
            [
                "name" => "Properties List",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties Create",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties Edit",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties Delete",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties View",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties Status Change",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties Publish Status Change",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties Assign Agents",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties Duplicate",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties View",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties Publish",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties Un-publish",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties Pending",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties Draft",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties Draft",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties Archive",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties Refuse",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties Assign Agents",
                "group_name" => "Listings",
            ],
            [
                "name" => "Properties Hide Price",
                "group_name" => "Listings",
            ],
            [
                "name" => "Listing Duplicate Check",
                "group_name" => "Listings",
            ],
            [
                "name" => "Upload Without Watermark",
                "group_name" => "Listings",
            ],
            [
                "name" => "Delete Media",
                "group_name" => "Listings",
            ],
            [
                "name" => "Add Media",
                "group_name" => "Listings",
            ],
            [
                "name" => "Change Media Order",
                "group_name" => "Listings",
            ], [
                "name" => "Listing Rera Check",
                "group_name" => "Listings",
            ],
            [
                "name" => "Download Bulk Without Watermark",
                "group_name" => "Listings",
            ],
            [
                "name" => "Download Bulk With Watermark",
                "group_name" => "Listings",
            ],

            //Leads
            [
                "name" => "Leads List",
                "group_name" => "Leads",
            ],
            [
                'name'=>'Leads Transfer to Leadpool',
                'group_name'=>'Leads'

            ],
            [
                "name" => "Leads Self Assign",
                "group_name" => "Leads",
            ],
            [
                "name" => "Convert to Listing Lead",
                "group_name" => "Leads",
            ],

            [
                "name" => "Leads Create",
                "group_name" => "Leads",
            ],
            [
                "name" => "Leads Edit",
                "group_name" => "Leads",
            ],
            [
                "name" => "Leads Delete",
                "group_name" => "Leads",
            ],
            [
                "name" => "Leads View",
                "group_name" => "Leads",
            ],
            [
                "name" => "Lead status change",
                "group_name" => "Leads",
            ],
            [
                "name" => "Lead sub status change",
                "group_name" => "Leads",
            ],
            [
                "name" => "Leads Additional Agents Add",
                "group_name" => "Leads",
            ],
            [
                "name" => "Leads Additional Agents Remove",
                "group_name" => "Leads",
            ],
            [
                "name" => "Leads Additional Agents Bulk Assign",
                "group_name" => "Leads",
            ],
            [
                "name" => "Leads View My Recordings",
                "group_name" => "Leads",
            ],
            [
                'name' => 'Leads Assign Agent',
                "group_name" => "Leads",
            ],
            [
                'name' => 'Lead Task Status Change',
                "group_name" => "Leads",
            ],
            [
                'name' => 'Contact Task Status Change',
                "group_name" => "Contacts",
            ],
            [
                'name' => 'Lead Meeting Status Change',
                "group_name" => "Leads",
            ],
            [
                'name' => 'Contact Meeting Status Change',
                "group_name" => "Contacts",
            ],
            [
                'name' => 'Dial other lead',
                "group_name" => "Leads",
            ],
            [
                'name' => 'Dial my lead',
                "group_name" => "Leads",
            ],
            [
                'name' => 'Leads Archive',
                "group_name" => "Leads",
            ],

            [
                "name" => "Call Logs List",
                "group_name" => "Call Logs",
            ],
            [
                "name" => "Call Logs Create",
                "group_name" => "Call Logs",
            ],
            [
                "name" => "Call Logs Edit",
                "group_name" => "Call Logs",
            ],
            [
                "name" => "Call Logs Delete",
                "group_name" => "Call Logs",
            ],
            [
                "name" => "Call Logs View",
                "group_name" => "Call Logs",
            ],

            //Lead Sources
            [
                "name" => "Lead Sources List",
                "group_name" => "Lead Sources",
            ],
            [
                "name" => "Lead Sources Create",
                "group_name" => "Lead Sources",
            ],
            [
                "name" => "Lead Sources Edit",
                "group_name" => "Lead Sources",
            ],
            [
                "name" => "Lead Sources Delete",
                "group_name" => "Lead Sources",
            ],
            [
                "name" => "Lead Sources View",
                "group_name" => "Lead Sources",
            ],

            //Lead Statuses
            [
                "name" => "Lead Statuses List",
                "group_name" => "Lead Statuses",
            ],
            [
                "name" => "Lead Statuses Create",
                "group_name" => "Lead Statuses",
            ],
            [
                "name" => "Lead Statuses Edit",
                "group_name" => "Lead Statuses",
            ],
            [
                "name" => "Lead Statuses Delete",
                "group_name" => "Lead Statuses",
            ],
            [
                "name" => "Lead Statuses View",
                "group_name" => "Lead Statuses",
            ],

            //Lead Sub Statuses
            [
                "name" => "Lead Sub Statuses List",
                "group_name" => "Lead Sub Statuses",
            ],
            [
                "name" => "Lead Sub Statuses Create",
                "group_name" => "Lead Sub Statuses",
            ],
            [
                "name" => "Lead Sub Statuses Edit",
                "group_name" => "Lead Sub Statuses",
            ],
            [
                "name" => "Lead Sub Statuses Delete",
                "group_name" => "Lead Sub Statuses",
            ],
            [
                "name" => "Lead Sub Statuses View",
                "group_name" => "Lead Sub Statuses",
            ],

            //Lead Types
            [
                "name" => "Lead Types List",
                "group_name" => "Lead Types",
            ],
            [
                "name" => "Lead Types Create",
                "group_name" => "Lead Types",
            ],
            [
                "name" => "Lead Types Edit",
                "group_name" => "Lead Types",
            ],
            [
                "name" => "Lead Types Delete",
                "group_name" => "Lead Types",
            ],
            [
                "name" => "Lead Types View",
                "group_name" => "Lead Types",
            ],

            //Deals
            [
                "name" => "Deals List",
                "group_name" => "Deals",
            ],
            [
                "name" => "Deals Create",
                "group_name" => "Deals",
            ],
            [
                "name" => "Deals Edit",
                "group_name" => "Deals",
            ],
            [
                "name" => "Deals Delete",
                "group_name" => "Deals",
            ],
            [
                "name" => "Deals View",
                "group_name" => "Deals",
            ],

            //Deal Types
            [
                "name" => "Deal Types List",
                "group_name" => "Deal Types",
            ],
            [
                "name" => "Deal Types Create",
                "group_name" => "Deal Types",
            ],
            [
                "name" => "Deal Types Edit",
                "group_name" => "Deal Types",
            ],
            [
                "name" => "Deal Types Delete",
                "group_name" => "Deal Types",
            ],
            [
                "name" => "Deal Types View",
                "group_name" => "Deal Types",
            ],

            // Developer
            [
                "name" => "Developers List",
                "group_name" => "Developers",
            ],
            [
                "name" => "Developer Create",
                "group_name" => "Developers",
            ],
            [
                "name" => "Developer Edit",
                "group_name" => "Developers",
            ],
            [
                "name" => "Developer Delete",
                "group_name" => "Developers",
            ],
            [
                "name" => "Developer View",
                "group_name" => "Developers",
            ],
            //Transaction Types
            [
                "name" => "Transaction Types List",
                "group_name" => "Transaction Types",
            ],
            [
                "name" => "Transaction Types Create",
                "group_name" => "Transaction Types",
            ],
            [
                "name" => "Transaction Types Edit",
                "group_name" => "Transaction Types",
            ],
            [
                "name" => "Transaction Types Delete",
                "group_name" => "Transaction Types",
            ],
            [
                "name" => "Transaction Types View",
                "group_name" => "Transaction Types",
            ],

            //Deal Sources
            [
                "name" => "Deal Sources List",
                "group_name" => "Deal Sources",
            ],
            [
                "name" => "Deal Sources Create",
                "group_name" => "Deal Sources",
            ],
            [
                "name" => "Deal Sources Edit",
                "group_name" => "Deal Sources",
            ],
            [
                "name" => "Deal Sources Delete",
                "group_name" => "Deal Sources",
            ],
            [
                "name" => "Deal Sources View",
                "group_name" => "Deal Sources",
            ],

            //Deal Statuses
            [
                "name" => "Deal Statuses List",
                "group_name" => "Deal Statuses",
            ],
            [
                "name" => "Deal Statuses Create",
                "group_name" => "Deal Statuses",
            ],
            [
                "name" => "Deal Statuses Edit",
                "group_name" => "Deal Statuses",
            ],
            [
                "name" => "Deal Statuses Delete",
                "group_name" => "Deal Statuses",
            ],
            [
                "name" => "Deal Statuses View",
                "group_name" => "Deal Statuses",
            ],

            //Deal Sub Statuses
            [
                "name" => "Deal Sub Statuses List",
                "group_name" => "Deal Sub Statuses",
            ],
            [
                "name" => "Deal Sub Statuses Create",
                "group_name" => "Deal Sub Statuses",
            ],
            [
                "name" => "Deal Sub Statuses Edit",
                "group_name" => "Deal Sub Statuses",
            ],
            [
                "name" => "Deal Sub Statuses Delete",
                "group_name" => "Deal Sub Statuses",
            ],
            [
                "name" => "Deal Sub Statuses View",
                "group_name" => "Deal Sub Statuses",
            ],

            //Contacts
            [
                "name" => "Contacts List",
                "group_name" => "Contacts",
            ],
            [
                "name" => "Contacts Create",
                "group_name" => "Contacts",
            ],
            [
                "name" => "Contacts Edit",
                "group_name" => "Contacts",
            ],
            [
                "name" => "Contacts Delete",
                "group_name" => "Contacts",
            ],
            [
                "name" => "Contacts View",
                "group_name" => "Contacts",
            ],
            [
                "name" => "Contacts Additional Agents Add",
                "group_name" => "Contacts",
            ],
            [
                "name" => "Contacts Additional Agents Remove",
                "group_name" => "Contacts",
            ],
            [
                "name" => "Contacts Additional Agents Bulk Assign",
                "group_name" => "Contacts",
            ],

            //Contact Sources
            [
                "name" => "Contact Sources List",
                "group_name" => "Contact Sources",
            ],
            [
                "name" => "Contact Sources Create",
                "group_name" => "Contact Sources",
            ],
            [
                "name" => "Contact Sources Edit",
                "group_name" => "Contact Sources",
            ],
            [
                "name" => "Contact Sources Delete",
                "group_name" => "Contact Sources",
            ],
            [
                "name" => "Contact Sources View",
                "group_name" => "Contact Sources",
            ],

            //Contact Statuses
            [
                "name" => "Contact Statuses List",
                "group_name" => "Contact Statuses",
            ],
            [
                "name" => "Contact Statuses Create",
                "group_name" => "Contact Statuses",
            ],
            [
                "name" => "Contact Statuses Edit",
                "group_name" => "Contact Statuses",
            ],
            [
                "name" => "Contact Statuses Delete",
                "group_name" => "Contact Statuses",
            ],
            [
                "name" => "Contact Statuses View",
                "group_name" => "Contact Statuses",
            ],

            //Contact Types
            [
                "name" => "Contact Types List",
                "group_name" => "Contact Types",
            ],
            [
                "name" => "Contact Types Create",
                "group_name" => "Contact Types",
            ],
            [
                "name" => "Contact Types Edit",
                "group_name" => "Contact Types",
            ],
            [
                "name" => "Contact Types Delete",
                "group_name" => "Contact Types",
            ],
            [
                "name" => "Contact Types View",
                "group_name" => "Contact Types",
            ],
            //Calendar
            [
                "name" => "Calendar List",
                "group_name" => "Calendar",
            ],
            [
                "name" => "Calendar Create",
                "group_name" => "Calendar",
            ],
            [
                "name" => "Calendar Edit",
                "group_name" => "Calendar",
            ],
            [
                "name" => "Calendar Delete",
                "group_name" => "Calendar",
            ],
            [
                "name" => "Calendar View",
                "group_name" => "Calendar",
            ],
            [
                "name" => "Contact Task Status Change",
                "group_name" => "Calendar",
            ],
            [
                "name" => "Contact Meeting Status Change",
                "group_name" => "Calendar",
            ],
            [
                "name" => "Lead Task Status Change",
                "group_name" => "Calendar",
            ],
            [
                "name" => "Lead Meeting Status Change",
                "group_name" => "Calendar",
            ],

            //Regions
            [
                "name" => "Regions List",
                "group_name" => "Regions",
            ],
            [
                "name" => "Regions Create",
                "group_name" => "Regions",
            ],
            [
                "name" => "Regions Edit",
                "group_name" => "Regions",
            ],
            [
                "name" => "Regions Delete",
                "group_name" => "Regions",
            ],
            [
                "name" => "Regions View",
                "group_name" => "Regions",
            ],

            //Communities
            [
                "name" => "Communities List",
                "group_name" => "Communities",
            ],
            [
                "name" => "Communities Create",
                "group_name" => "Communities",
            ],
            [
                "name" => "Communities Edit",
                "group_name" => "Communities",
            ],
            [
                "name" => "Communities Delete",
                "group_name" => "Communities",
            ],
            [
                "name" => "Communities View",
                "group_name" => "Communities",
            ],

            //Sub Communities
            [
                "name" => "Sub Communities List",
                "group_name" => "Sub Communities",
            ],
            [
                "name" => "Sub Communities Create",
                "group_name" => "Sub Communities",
            ],
            [
                "name" => "Sub Communities Edit",
                "group_name" => "Sub Communities",
            ],
            [
                "name" => "Sub Communities Delete",
                "group_name" => "Sub Communities",
            ],
            [
                "name" => "Sub Communities View",
                "group_name" => "Sub Communities",
            ],

            //Community Properties

            //Listing Leads

            [
                "name" => "Listing Leads List",
                "group_name" => "Listing Leads",
            ],

            [
                "name" => "Listing Leads Create",
                "group_name" => "Listing Leads",
            ],
            [
                "name" => "Listing Leads Generate Listing",
                "group_name" => "Listing Leads",
            ],
            [
                "name" => "Leads Additional Agents Bulk Assign",
                "group_name" => "Listing Leads",
            ],
            [
                "name" => "Listing Leads Assign Agent",
                "group_name" => "Listing Leads",
            ],
            [
                "name" => "Listing Leads Archive",
                "group_name" => "Listing Leads",
            ],
            [
                "name" => "Listing Leads Delete",
                "group_name" => "Listing Leads",
            ],
            [
                "name" => "Listing Leads View",
                "group_name" => "Listing Leads",
            ],
            [
                "name" => "Bulk Delete Listing Leads",
                "group_name" => "Listing Leads",
            ],
            [
                "name" => "Listing Leads Notes List",
                "group_name" => "Listing Leads",
            ],
            [
                "name" => "Listing Leads Notes Create",
                "group_name" => "Listing Leads",
            ],

            [
                "name" => "Community Properties List",
                "group_name" => "Community Properties",
            ],
            [
                "name" => "Community Properties Create",
                "group_name" => "Community Properties",
            ],
            [
                "name" => "Community Properties Edit",
                "group_name" => "Community Properties",
            ],
            [
                "name" => "Community Properties Delete",
                "group_name" => "Community Properties",
            ],
            [
                "name" => "Community Properties View",
                "group_name" => "Community Properties",
            ],

            //Property Amenities
            [
                "name" => "Property Amenities List",
                "group_name" => "Property Amenities",
            ],
            [
                "name" => "Property Amenities Create",
                "group_name" => "Property Amenities",
            ],
            [
                "name" => "Property Amenities Edit",
                "group_name" => "Property Amenities",
            ],
            [
                "name" => "Property Amenities Delete",
                "group_name" => "Property Amenities",
            ],
            [
                "name" => "Property Amenities View",
                "group_name" => "Property Amenities",
            ],

            //Property Categories
            [
                "name" => "Property Categories List",
                "group_name" => "Property Categories",
            ],
            [
                "name" => "Property Categories Create",
                "group_name" => "Property Categories",
            ],
            [
                "name" => "Property Categories Edit",
                "group_name" => "Property Categories",
            ],
            [
                "name" => "Property Categories Delete",
                "group_name" => "Property Categories",
            ],
            [
                "name" => "Property Categories View",
                "group_name" => "Property Categories",
            ],

            //Property Types
            [
                "name" => "Property Types List",
                "group_name" => "Property Types",
            ],
            [
                "name" => "Property Types Create",
                "group_name" => "Property Types",
            ],
            [
                "name" => "Property Types Edit",
                "group_name" => "Property Types",
            ],
            [
                "name" => "Property Types Delete",
                "group_name" => "Property Types",
            ],
            [
                "name" => "Property Types View",
                "group_name" => "Property Types",
            ],

            //Property Statuses
            [
                "name" => "Property Statuses List",
                "group_name" => "Property Statuses",
            ],
            [
                "name" => "Property Statuses Create",
                "group_name" => "Property Statuses",
            ],
            [
                "name" => "Property Statuses Edit",
                "group_name" => "Property Statuses",
            ],
            [
                "name" => "Property Statuses Delete",
                "group_name" => "Property Statuses",
            ],
            [
                "name" => "Property Statuses View",
                "group_name" => "Property Statuses",
            ],

            //Beds
            [
                "name" => "Beds List",
                "group_name" => "Beds",
            ],
            [
                "name" => "Beds Create",
                "group_name" => "Beds",
            ],
            [
                "name" => "Beds Edit",
                "group_name" => "Beds",
            ],
            [
                "name" => "Beds Delete",
                "group_name" => "Beds",
            ],
            [
                "name" => "Beds View",
                "group_name" => "Beds",
            ],

            //Baths
            [
                "name" => "Baths List",
                "group_name" => "Baths",
            ],
            [
                "name" => "Baths Create",
                "group_name" => "Baths",
            ],
            [
                "name" => "Baths Edit",
                "group_name" => "Baths",
            ],
            [
                "name" => "Baths Delete",
                "group_name" => "Baths",
            ],
            [
                "name" => "Baths View",
                "group_name" => "Baths",
            ],

            //Property Notes
            [
                "name" => "Property Notes List",
                "group_name" => "Property Notes",
            ],
            [
                "name" => "Property Notes Create",
                "group_name" => "Property Notes",
            ],
            [
                "name" => "Property Notes Edit",
                "group_name" => "Property Notes",
            ],
            [
                "name" => "Property Notes Delete",
                "group_name" => "Property Notes",
            ],
            [
                "name" => "Property Notes View",
                "group_name" => "Property Notes",
            ],

            //Lead Notes
            [
                "name" => "Lead Notes List",
                "group_name" => "Lead Notes",
            ],
            [
                "name" => "Lead Notes Create",
                "group_name" => "Lead Notes",
            ],
            [
                "name" => "Lead Notes Edit",
                "group_name" => "Lead Notes",
            ],
            [
                "name" => "Lead Notes Delete",
                "group_name" => "Lead Notes",
            ],
            [
                "name" => "Lead Notes View",
                "group_name" => "Lead Notes",
            ],

            //Deal Notes
            [
                "name" => "Deal Notes List",
                "group_name" => "Deal Notes",
            ],
            [
                "name" => "Deal Notes Create",
                "group_name" => "Deal Notes",
            ],
            [
                "name" => "Deal Notes Edit",
                "group_name" => "Deal Notes",
            ],
            [
                "name" => "Deal Notes Delete",
                "group_name" => "Deal Notes",
            ],
            [
                "name" => "Deal Notes View",
                "group_name" => "Deal Notes",
            ],

            //Contact Notes
            [
                "name" => "Contact Notes List",
                "group_name" => "Contact Notes",
            ],
            [
                "name" => "Contact Notes Create",
                "group_name" => "Contact Notes",
            ],
            [
                "name" => "Contact Notes Edit",
                "group_name" => "Contact Notes",
            ],
            [
                "name" => "Contact Notes Delete",
                "group_name" => "Contact Notes",
            ],
            [
                "name" => "Contact Notes View",
                "group_name" => "Contact Notes",
            ],

            //Portals
            [
                "name" => "Portals List",
                "group_name" => "Portals",
            ],
            [
                "name" => "Portals Create",
                "group_name" => "Portals",
            ],
            [
                "name" => "Portals Edit",
                "group_name" => "Portals",
            ],
            [
                "name" => "Portals Delete",
                "group_name" => "Portals",
            ],
            [
                "name" => "Portals View",
                "group_name" => "Portals",
            ],

            //Email Settings
            [
                "name" => "Email Settings List",
                "group_name" => "Email Settings",
            ],
            [
                "name" => "Email Settings Create",
                "group_name" => "Email Settings",
            ],
            [
                "name" => "Email Settings Edit",
                "group_name" => "Email Settings",
            ],
            [
                "name" => "Email Settings Delete",
                "group_name" => "Email Settings",
            ],
            [
                "name" => "Email Settings View",
                "group_name" => "Email Settings",
            ],

            //Events Settings
            [
                "name" => "Events List",
                "group_name" => "Events",
            ],
            [
                "name" => "Events Create",
                "group_name" => "Events",
            ],
            [
                "name" => "Events Edit",
                "group_name" => "Events",
            ],
            [
                "name" => "Events Delete",
                "group_name" => "Events",
            ],
            [
                "name" => "Events View",
                "group_name" => "Events",
            ],
            //event type
            [
                "name" => "Event Types List",
                "group_name" => "Event Types",
            ],
            [
                "name" => "Event Types Create",
                "group_name" => "Event Types",
            ],
            [
                "name" => "Event Types Edit",
                "group_name" => "Event Types",
            ],
            [
                "name" => "Event Types Delete",
                "group_name" => "Event Types",
            ],
            [
                "name" => "Event Types Delete Multiple",
                "group_name" => "Event Types",
            ],
            [
                "name" => "Event Types View",
                "group_name" => "Event Types",
            ],

            //Jobs Settings
            [
                "name" => "Jobs List",
                "group_name" => "Jobs",
            ],
            [
                "name" => "Jobs Create",
                "group_name" => "Jobs",
            ],
            [
                "name" => "Jobs Edit",
                "group_name" => "Jobs",
            ],
            [
                "name" => "Jobs Delete",
                "group_name" => "Jobs",
            ],
            [
                "name" => "Jobs View",
                "group_name" => "Jobs",
            ],
            [
                "name" => "Jobs Approve",
                "group_name" => "Jobs",
            ],
            [
                "name" => "Jobs Reject",
                "group_name" => "Jobs",
            ],
            // Property Shoot
            [
                "name" => "Property Shoot List",
                "group_name" => "Property Shoot",
            ],
            [
                "name" => "Property Shoot Create",
                "group_name" => "Property Shoot",
            ],
            [
                "name" => "Property Shoot Edit",
                "group_name" => "Property Shoot",
            ],
            [
                "name" => "Property Shoot Delete",
                "group_name" => "Property Shoot",
            ],
            [
                "name" => "Property Shoot Multi Delete",
                "group_name" => "Property Shoot",
            ],
            [
                "name" => "Property Shoot View",
                "group_name" => "Property Shoot",
            ],
            [
                "name" => "Property Shoot Approve",
                "group_name" => "Property Shoot",
            ],
            [
                "name" => "Property Shoot Reject",
                "group_name" => "Property Shoot",
            ],
            [
                "name" => "Property Shoot Note Create",
                "group_name" => "Property Shoot",
            ],
            [
                "name" => "Property Shoot Note View",
                "group_name" => "Property Shoot",
            ], [
                "name" => "Property Shoot Status Change",
                "group_name" => "Property Shoot",
            ],
            [
                "name" => "Property Shoot Cancel",
                "group_name" => "Property Shoot",
            ],

            // Mediums
            [
                "name" => "Mediums List",
                "group_name" => "Mediums",
            ],
            [
                "name" => "Mediums Delete",
                "group_name" => "Mediums",
            ],
            [
                "name" => "Mediums View",
                "group_name" => "Mediums",
            ],
            [
                "name" => "Mediums Edit",
                "group_name" => "Mediums",
            ],

            // Sources

            [
                "name" => "Sources List",
                "group_name" => "Sources",
            ],
            [
                "name" => "Sources Delete",
                "group_name" => "Sources",
            ],
            [
                "name" => "Sources View",
                "group_name" => "Sources",
            ],
            [
                "name" => "Sources Edit",
                "group_name" => "Sources",
            ],
            // Sources
            // AX Apps
            [
                "name" => "AX Academy Login",
                "group_name" => "AX Apps",
            ],
            [
                "name" => "AX Contacts Login",
                "group_name" => "AX Apps",
            ],
            [
                "name" => "Bitrix CRM Login",
                "group_name" => "AX Apps",
            ],
            [
                "name" => "AX Telelog Login",
                "group_name" => "AX Apps",
            ],
            [
                "name" => "AX Spot Login",
                "group_name" => "AX Apps",
            ],
        

            // Type
            [
                "name" => "Type List",
                "group_name" => "Type",
            ],
            [
                "name" => "Type Delete",
                "group_name" => "Type",
            ],
            [
                "name" => "Type View",
                "group_name" => "Type",
            ],
            [
                "name" => "Type Edit",
                "group_name" => "Type",
            ],

            // Unassigned

            [
                "name" => "Lead Pool Delete",
                "group_name" => "Lead Pool",
            ],
            [
                "name" => "Lead Pool List",
                "group_name" => "Lead Pool",
            ],

            [
                "name" => "Lead Pool View",
                "group_name" => "Lead Pool",
            ],
            [
                "name" => "Lead Pool Edit",
                "group_name" => "Lead Pool",
            ],
            [
                'name' => 'Assign Telesales',
                'group_name' => 'Lead Pool',
            ],
            [
                'name' => 'Edit Telesales Head',
                'group_name' => 'Lead Pool',
            ],
            [
                'name' => 'Assign Telesales Head',
                'group_name' => 'Lead Pool',

            ],
            [
                'name' => 'Assign Telesales',
                'group_name' => 'Lead Pool',

            ],
            [
                'name' => 'Bulk Telesales Head Assignments',
                'group_name' => 'Lead Pool',
            ]
            ,
            [
                'name' => 'Bulk Telesales Assignments',
                'group_name' => 'Lead Pool',
            ],
            [
                'name' => 'Ax Calculator',
                'group_name' => 'Calculator',
            ],


        ];
        foreach ($permissions as $permission) {
            Permission::updateOrCreate([
                "name" => $permission["name"],
            ], array_merge($permission, ["slug" => Str::slug($permission["name"], "-"), "guard_name" => "web"]));
        }
    }

    public function getSalesDirectorPermissions()
    {
        $permissions = [
            "Users List",
            "Users Create",
            "Users Edit",
            "Users View",
            "Users Tree View",

            "Properties View",
            "Properties Draft",

            "Property Notes Create",
            "Property Notes List",

            "Properties List",
            "Properties Create",
            "Properties Edit",
            "Properties View",

            "Leads List",
            "Leads Create",
            "Leads Edit",
            "Leads View",
            "Lead status change",
            "Lead sub status change",

            "Lead Notes Create",
            "Lead Notes List",

            "Deals List",
            "Deals Create",
            "Deals Edit",
            "Deals View",

            "Deal Notes Create",
            "Deal Notes List",

            "Contacts List",
            "Contacts Create",
            "Contacts Edit",
            "Contacts View",

            "Contact Notes Create",
            "Contact Notes List",

            "Events List",
            "Events Create",
            "Events Edit",
            "Events View",
        ];
        return Permission::whereIn("name", $permissions)->pluck("id")->all();
    }

    public function getLineManagerPermissions()
    {
        $permissions = [
            "Users List",
            "Users Create",
            "Users Edit",
            "Users View",
            "Users Tree View",

            "Properties View",
            "Properties Draft",

            "Property Notes Create",
            "Property Notes List",

            "Properties List",
            "Properties Create",
            "Properties Edit",
            "Properties View",

            "Leads List",
            "Leads Create",
            "Leads Edit",
            "Leads View",

            "Lead Notes Create",
            "Lead Notes List",

            "Deals List",
            "Deals Create",
            "Deals Edit",
            "Deals View",

            "Deal Notes Create",
            "Deal Notes List",

            "Contacts List",
            "Contacts Create",
            "Contacts Edit",
            "Contacts View",

            "Contact Notes Create",
            "Contact Notes List",

            "Events List",
            "Events Create",
            "Events Edit",
            "Events View",
        ];
        return Permission::whereIn("name", $permissions)->pluck("id")->all();
    }

    public function getAgentPermissions()
    {
        $permissions = [
            // "Users List",
            // "Users Create",
            // "Users Edit",
            // "Users View",
            // "Users Tree View",

            "Properties View",
            "Properties Draft",

            "Property Notes Create",
            "Property Notes List",

            "Properties List",
            "Properties Create",
            "Properties Edit",
            "Properties View",
            "Properties Archive",

            "Leads List",
            "Leads Create",
            "Leads Edit",
            "Leads View",

            "Lead Notes Create",
            "Lead Notes List",

            "Deals List",
            "Deals Create",
            "Deals Edit",
            "Deals View",

            "Deal Notes Create",
            "Deal Notes List",

            "Contacts List",
            "Contacts Create",
            "Contacts Edit",
            "Contacts View",

            "Contact Notes Create",
            "Contact Notes List",

            "Events List",
            "Events Create",
            "Events Edit",
            "Events View",
        ];
        return Permission::whereIn("name", $permissions)->pluck("id")->all();
    }

    public function getTelesalePermissions()
    {
        $permissions = [
            "Leads List",
            "Leads Create",
            "Leads Edit",
            "Leads View",

            // 'Unassigned List',
            // 'Unassigned View',
            // 'Unassigned Edit',
            // 'Unassigned Delete',

        ];
        return Permission::whereIn("name", $permissions)->pluck("id")->all();
    }
    public function getTelesaleHeadPermissions()
    {
        $permissions = [
            "Leads List",
            "Leads Create",
            "Leads Edit",
            "Leads View",

            // 'Unassigned List',
            // 'Unassigned View',
            // 'Unassigned Edit',
            // 'Unassigned Delete',
            // 'Assign Telesales',

            // 'Bulk Telesales Assignments'
        ];
        return Permission::whereIn("name", $permissions)->pluck("id")->all();
    }

}
