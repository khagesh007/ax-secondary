<?php

namespace App\Console\Commands;

use App\Models\Portal;
use Illuminate\Console\Command;
use App\Models\PropertyAmenityPortal;
use App\Models\PropertyAmenity;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MapPFV2Amenity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'map:pf-amenity';

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
            $this->info('Starting PF amenity mapping...');
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
            
            // vastu done
            // center ac 
            // kitch appliances // done
            // concierge done
            // view of water/sea done
            // network done
            $features = [
                "central-ac",
                "built-in-wardrobes",
                "kitchen-appliances",
                "security",
                "concierge",
                "private-gym",
                "shared-gym",
                "private-jacuzzi",
                "shared-spa",
                "covered-parking",
                "maids-room",
                "barbecue-area",
                "shared-pool",
                "childrens-pool",
                "private-garden",
                "private-pool",
                "view-of-water",
                "walk-in-closet",
                "lobby-in-building",
                "electricity",
                "waters",
                "sanitation",
                "no-services",
                "fixed-phone",
                "fibre-optics",
                "flood-drainage",
                "balcony",
                "networked",
                "view-of-landmark",
                "dining-in-building",
                "conference-room",
                "study",
                "maid-service",
                "childrens-play-area",
                "pets-allowed",
                "vastu-compliant",
            ];
            
            DB::transaction(function () use ($features, $portal, $user) {
                foreach ($features as $feature) {
                    $amenity = PropertyAmenity::where('slug', $feature)
                        ->orWhere('title', $feature)
                        ->first();
                    
                    if (!$amenity) {
                        $amenity = PropertyAmenity::create([
                            'title' => ucwords(str_replace('-', ' ', $feature)),
                            'slug' => $feature,
                            'status' => 0,
                            'created_by_id' => $user->id,
                        ]);
                       
                        Log::channel('bitrix_alert')->info("🆕 Created new feature: {$feature} → ID: {$amenity->id}");
                        $this->info("🆕 Created new feature: {$feature} → ID: {$amenity->id}");
                    }
                   
                    $exists = PropertyAmenityPortal::where('property_amenity_id', $amenity->id)
                        ->where('portal_id', $portal->id)
                        ->exists();

                    if (!$exists) {
                        PropertyAmenityPortal::create([
                            "property_amenity_id" => $amenity->id,
                            "portal_id" => $portal->id,
                            "title" => $feature,
                            "created_by_id" => $user->id,
                        ]);
                        Log::channel('bitrix_alert')->info("✅ Mapped: {$feature} to portal {$portal->short_name}");
                        $this->info("✅ Mapped: {$feature} to portal {$portal->short_name}");
                        
                    } else {
                        $existingPropertyAmenity = PropertyAmenityPortal::where('property_amenity_id', $amenity->id)
                        ->where('portal_id', $portal->id)
                        ->first();
                        $existingPropertyAmenity->title = $feature;
                        $existingPropertyAmenity->save();
                        Log::channel('bitrix_alert')->info("ℹ️ Already mapped: {$feature}");
                        $this->info("ℹ️ Already mapped: {$feature}");
                    }
                }
            });
            $this->info('end PF amenity');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
