<?php

namespace App\Jobs;

use App\Models\Property;
use App\Traits\FileTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Log;

class bulkReraReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use FileTrait;
    protected $ids;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::channel('discord')->info('Bulk Rera Report Job Started');

        $properties = Property::whereIn('id', $this->ids)->get();
        foreach ($properties as $property) {
            try {
                // $property = Property::find($request->id);
                $header = array();
                $header[] = 'Content-Type: application/json';
                $header[] = 'Content-Length: 4000';
                $header[] = 'Connection: keep-alive';

                $response = Http::post('https://secondary.axcrm.ae/api/testing/rera/checkPermit', [
                    'permit_number' => $property->rera_permit_number,
                    'for' => $property->for,
                    'api_token' => 'fYlQO814toCEdmdp',
                ]);
                $res = $response;
                if (empty($res)) {
                    $response = Http::post('https://secondary.axcrm.ae/api/testing/rera/checkPermitWithoutQr', [
                        'permit_number' => $property->rera_permit_number,
                        'for' => $property->for,
                        'api_token' => 'fYlQO814toCEdmdp',
                    ]);
                    $res = $response->json();
                }

                if (!empty($res)) {
                    $qr = $res['qr'] ?? '';
                    if ($qr != '') {
                        $url = $this->base64ToImage($qr);
                        $property->qr_link = $url ?? null;
                    }
                    $property->rera_issue_date = date('Y-m-d', strtotime($res['start_date']));
                    $property->rera_expiry_date = date('Y-m-d', strtotime($res['end_date']));
                    $property->rera_validation_url = $res['validation_url'] ?? null;
                    $property->withoutApproval()->save();
                    $res['qr_link'] = $property->qr_link ?? null;

                    // info('Property RERA Check Successfully', ['property_id' => $property->id, 'rera_permit_number' => $property->rera_permit_number, 'rera_issue_date' => $property->rera_issue_date, 'rera_expiry_date' => $property->rera_expiry_date, 'rera_validation_url' => $property->rera_validation_url]);
                    // return response()->json(['status'=>'1','message' => 'Property RERA Check Successfully', 'data' => $res], 200);
                } else {
                    Property::where('id', $property->id)->update(['unable_to_generate_qr' => 1]);
                    // info('Listing not found at RERA', ['property_id' => $property->id, 'rera_permit_number' => $property->rera_permit_number]);
                    // return response()->json(['status'=>'0','message' => 'Listing not found at RERA', 'data' => $res], 200);
                }

            } catch (\Exception $e) {
                info($e);
                continue;
                // return response()->json(['status'=>'0','message' => 'Something went wrong!!','error'=>$e->getMessage(),'error_line'=>$e->getLine()], 500);
            }
            break;
        }

        Log::channel('discord')->info('Bulk Rera Report Job Ended');
    }

    public function base64ToImage($base64String)
    {
        $disk = "s3";
        $image = explode('base64,', $base64String);
        $image = end($image);
        $image = str_replace(' ', '+', $image);
        $fileName = uniqid() . '.png';
        $path = 'images/listings/qr';
        $original = Image::make(base64_decode($image))->encode('png')->stream();
        $originalPath = Storage::disk($disk)->put((!empty($path) ? $path . "/" : "") . "original_" . $fileName, $original->__toString());
        $originalUrl = Storage::disk($disk)->url((!empty($path) ? $path . "/" : "") . "original_" . $fileName, $original->__toString());
        return $originalUrl;
    }
}
