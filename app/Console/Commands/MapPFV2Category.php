<?php

namespace App\Console\Commands;

use App\Models\Portal;
use Illuminate\Console\Command;
use App\Models\PropertyCategoryPortal;
use App\Models\PropertyCategory;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MapPFV2Category extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'map:pf-category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {

            $this->info('Starting PF category mapping...');

            $portal = Portal::where('short_name', 'PF')->first();
            $user = User::where('email', 'system@admin.ae')->first();

            if (!$portal) {
                $this->error('Portal PF not found.');
                return 1;
            }

            if (!$user) {
                $this->error('User system@admin.ae not found.');
                return 1;
            }
            // land done
            // labor camp done
            // office done

            $propertyTypes = [
                "compound",
                "whole-building",
                "factory",
                "land",
                "rest-house",
                "apartment",
                "shop",
                "warehouse",
                "chalet",
                "office-space",
                "full-floor",
                "villa",
                "farm",
                "show-room",
                "bulk-sale-unit",
                "restaurant",
                "roof",
                "half-floor",
                "twin-house",
                "cabin",
                "bulk-rent-unit",
                "ivilla",
                "co-working-space",
                "hotel-apartment",
                "retail",
                "duplex",
                "townhouse",
                "staff-accommodation",
                "medical-facility",
                "palace",
                "penthouse",
                "clinic",
                "cafeteria",
                "bungalow",
                "labor-camp",
                "business-center",
            ];
            DB::transaction(function () use ($propertyTypes, $portal, $user) {
                foreach ($propertyTypes as $type) {
                    $category = PropertyCategory::where('slug', $type)
                        ->orWhere('title', $type)
                        ->first();

                    if (!$category) {
                        $category = PropertyCategory::create([
                            'title' => ucwords(str_replace('-', ' ', $type)),
                            'slug' => $type,
                            'status' => 0,
                            'created_by_id' => $user->id,
                        ]);
                        Log::channel('bitrix_alert')->info("🆕 Created new category: {$type} → ID: {$category->id}");
                        $this->info("🆕 Created new category: {$type} → ID: {$category->id}");
                        
                    }

                    $exists = PropertyCategoryPortal::where('property_category_id', $category->id)
                        ->where('portal_id', $portal->id)
                        ->exists();

                    if (!$exists) {
                        PropertyCategoryPortal::create([
                            "property_category_id" => $category->id,
                            "portal_id" => $portal->id,
                            "title" => $type,
                            "created_by_id" => $user->id,
                        ]);
                        Log::channel('bitrix_alert')->info("✅ Mapped: {$type} to portal {$portal->short_name}");
                        $this->info("✅ Mapped: {$type} to portal {$portal->short_name}");
                        
                    } else {

                        $existingPropertyCatgory = PropertyCategoryPortal::where('property_category_id', $category->id)
                        ->where('portal_id', $portal->id)
                        ->first();
                        $existingPropertyCatgory->title = $type;
                        $existingPropertyCatgory->save();
                        Log::channel('bitrix_alert')->info("ℹ️ Already mapped: {$type}");
                        $this->info("ℹ️ Already mapped: {$type}");
                    }
                }
            });
            $this->info('Property types mapped successfully.');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
