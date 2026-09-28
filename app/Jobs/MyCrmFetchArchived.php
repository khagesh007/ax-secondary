<?php

namespace App\Jobs;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class MyCrmFetchArchived implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
public $page;
public $perpage;
public $token;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($page, $perpage, $token)
    {
        $this->page = $page;
        $this->perpage = $perpage;
        $this->token = $token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $header  = array();
        $header[] = 'Content-Type: application/json';
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->get('https://api-v2.mycrm.com/properties?per_page=1&page='.$this->page.'&fields=id,reference,price,user,status,location,created_at,updated_at,import,state,type,marked_as_seen,publication,bedrooms,bathrooms,size,languages,images,furnished,landlord,available_from,charges,parking,amenities,unit_number,developer,metadata,hash,quality_score,licenses,freehold,build_year,plot_size,built_up_area,floors,occupancy,renovation,project_status,financial_status,transaction,listing_level,verifications,duplicated_list,furnished,listing_status,listing_expiry_date,rega_permit,listing_auto_renew&filters[state]=archived&sort=updated&sort_order=desc&_=1678856220087');
        $data = $response->json();
        foreach ($data['properties'] as $property){
            //$obj = Property::where('my_crm_id',$property['id'])->first() ?? new Property();
            $obj = new Property();
            $obj->my_crm_id     = $property['id'];
            $obj->ref_no        = $property['reference'];
            $obj->is_archived   = true;
            if($obj->save()){
                MyCrmSyncArchived::dispatch($property);
                \Log::info($property);
            }
        }
    }
}
