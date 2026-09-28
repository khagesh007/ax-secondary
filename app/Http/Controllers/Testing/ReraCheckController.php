<?php

namespace App\Http\Controllers\Testing;

use App\Models\Contact;
use App\Models\ContactNote;
use App\Models\Location;
use App\Models\Medium;
use App\Models\Property;
use App\Models\Route;
use App\Models\Source;
use App\Models\TempDump;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

use App\Models\Lead;

use App\Models\LeadNote;
use Illuminate\Support\Str;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Traits\FileTrait;
use Intervention\Image\Facades\Image;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Illuminate\Http\UploadedFile;

class ReraCheckController extends Controller
{
    use FileTrait;
   public function base64ToImage($base64String)
    {
        $disk = "s3";
        $image = explode('base64,',$base64String);
        $image = end($image);
        $image = str_replace(' ', '+', $image);
        $fileName =  uniqid() . '.png';
        $path = 'images/listings/qr';
    //upload this on s3 disk and get url
        $original = Image::make(base64_decode($image))->encode('png')->stream();
       $originalPath = Storage::disk($disk)->put((!empty($path) ? $path . "/" : "") . "original_" . $fileName, $original->__toString());
       $originalUrl = Storage::disk($disk)->url((!empty($path) ? $path . "/" : "") . "original_" . $fileName, $original->__toString());
       return $originalUrl;
    }
    public function reraCheckWithQr($permit_number,$for)
    {
        if ($for == "Residential for sale" || $for == "Commercial for sale") {
            $purposeType = 2;
        } else {
            $purposeType = 1;
        } // Purpose Type 1 = Rent 2 = Sale
        try {
            $response = Http::withHeaders([
                'authorizationkey' => env('RERA_PROD_KEY'),
            ])->timeout(60)->get('https://b2bgateway.dubailand.gov.ae/permit/validate/listing/authority/' . $permit_number . '?isGenerateQrCode=true&purposeType=' . $purposeType);
            $json = $response->json();

            if (is_array($json) && !empty($json['result'])) {
                $collection = collect($json['result']);
                $collection2 = $collection->toArray();
                $selectedOne = getReleventDateRange($collection2);

                return [
                    'start_date' => $selectedOne['startDate'],
                    'end_date' => $selectedOne['endDate'],
                    'validation_url' => $selectedOne['validationUrl'],
                    'qr' => $selectedOne['validationQr'],
                    'rera_validation_url' => $selectedOne['validationUrl'],
                ];
            } else {
                return [];
                return response()->json(['message' => 'Invalid listing or listing not found at RERA'], 422);
            }
            // }
        }catch (\Exception $e){
            info($e->getMessage());
            info($e->getLine());
            return [];
            return response()->json(['message' => 'Something went wrong!!','error'=>$e->getMessage(),'error_line'=>$e->getLine()], 500);
        }

    }
    public function reraCheckWithoutQr($permit_number,$for)
    {
        if ($for == "Residential for sale" || $for == "Commercial for sale") {
            $purposeType = 2;
        } else {
            $purposeType = 1;
        } // Purpose Type 1 = Rent 2 = Sale
        try {
            $response = Http::withHeaders([
                'authorizationkey' => env('RERA_PROD_KEY'),
            ])->timeout(60)->get('https://b2bgateway.dubailand.gov.ae/permit/validate/listing/authority/' . $permit_number . '?isGenerateQrCode=true&purposeType=' . $purposeType);
            $json = $response->json();

            if (is_array($json) && !empty($json['result'])) {
                $collection = collect($json['result']);
                $collection2 = $collection->toArray();
                $selectedOne = getReleventDateRange($collection2);

                return [
                    'start_date' => $selectedOne['startDate'],
                    'end_date' => $selectedOne['endDate'],
                    'validation_url' => $selectedOne['validationUrl'],
                    'qr' => $selectedOne['validationQr'],
                    'rera_validation_url' => $selectedOne['validationUrl'],
                ];
            } else {
                return [];
                return response()->json(['message' => 'Invalid listing or listing not found at RERA'], 422);
            }
            // }
        }catch (\Exception $e){
            info($e->getMessage());
            info($e->getLine());
            return [];
            return response()->json(['message' => 'Something went wrong!!','error'=>$e->getMessage(),'error_line'=>$e->getLine()], 500);
        }

    }
    public function reraCheckResPrint(Request $request)
    {
        $request->validate([
            'for' => 'required',
            'permit_number' => 'required',
            'api_token'  => 'required'
        ]);
        abort_if($request->api_token != "fYlQO814toCEdmdp", 404);
        $for = $request->for;
        $permit_number = $request->permit_number;
        if ($for == "Residential for sale" || $for == "Commercial for sale") {
            $purposeType = 2;
        } else {
            $purposeType = 1;
        } // Purpose Type 1 = Rent 2 = Sale
        try {
            $response = Http::withHeaders([
                'authorizationkey' => env('RERA_PROD_KEY'),
            ])->timeout(60)->get('https://b2bgateway.dubailand.gov.ae/permit/validate/listing/authority/' . $permit_number . '?isGenerateQrCode=true&purposeType=' . $purposeType);
            
            $json = $response->json();

            print_r($response);
            // }
        }catch (\Exception $e){
            info($e->getMessage());
            info($e->getLine());
            return [];
            return response()->json(['message' => 'Something went wrong!!','error'=>$e->getMessage(),'error_line'=>$e->getLine()], 500);
        }

    }
    public function reraCheckWithQrForApi(Request $request)
    {
        $request->validate([
            'for' => 'required',
            'permit_number' => 'required',
            'api_token'  => 'required'
        ]);
        abort_if($request->api_token != "fYlQO814toCEdmdp", 404);
        $for = $request->for;
        $permit_number = $request->permit_number;
        if ($for == "Residential for sale" || $for == "Commercial for sale") {
            $purposeType = 2;
        } else {
            $purposeType = 1;
        } // Purpose Type 1 = Rent 2 = Sale
        try {
            $response = Http::withHeaders([
                'authorizationkey' => env('RERA_PROD_KEY'),
            ])->timeout(60)->get('https://b2bgateway.dubailand.gov.ae/permit/validate/listing/authority/' . $permit_number . '?isGenerateQrCode=true&purposeType=' . $purposeType);
            info('Rera Check With Qr For Api');
            info($response);
            info('----------------------');

            $json = $response->json();

            if (is_array($json) && !empty($json['result'])) {
                $collection = collect($json['result']);
                $collection2 = $collection->toArray();
                $selectedOne = getReleventDateRange($collection2);

                return [
                    'start_date' => $selectedOne['startDate'],
                    'end_date' => $selectedOne['endDate'],
                    'validation_url' => $selectedOne['validationUrl'],
                    'validationUrl' => $selectedOne['validationUrl'],
                    'qr' => $selectedOne['validationQr'],
                ];
            } else {
                return [];
                return response()->json(['message' => 'Invalid listing or listing not found at RERA'], 422);
            }
            // }
        }catch (\Exception $e){
            info($e->getMessage());
            info($e->getLine());
            return [];
            return response()->json(['message' => 'Something went wrong!!','error'=>$e->getMessage(),'error_line'=>$e->getLine()], 500);
        }

    }
    public function reraCheckWithoutQrForApi(Request $request)
    {
        $request->validate([
            'for' => 'required',
            'permit_number' => 'required',
            'api_token'  => 'required'
        ]);
        abort_if($request->api_token != "fYlQO814toCEdmdp", 404);
        $for = $request->for;
        $permit_number = $request->permit_number;
        if ($for == "Residential for sale" || $for == "Commercial for sale") {
            $purposeType = 2;
        } else {
            $purposeType = 1;
        } // Purpose Type 1 = Rent 2 = Sale
        try {
            $response = Http::withHeaders([
                'authorizationkey' => env('RERA_PROD_KEY'),
            ])->timeout(60)->get('https://b2bgateway.dubailand.gov.ae/permit/validate/listing/authority/' . $permit_number);
            info('Rera Check WithOut Qr For Api');
            info($response);
            info('----------------------');
            $json = $response->json();
            if (is_array($json) && !empty($json['result'])) {
                $collection = collect($json['result']);
                $collection2 = $collection->toArray();
                $selectedOne = getReleventDateRange($collection2);

                return [
                    'start_date' => $selectedOne['startDate'],
                    'end_date' => $selectedOne['endDate'],
                    'validation_url' => $selectedOne['validationUrl'],
                    'validationUrl' => $selectedOne['validationUrl'],
                ];
            } else {
                return [];
                return response()->json(['message' => 'Invalid listing or listing not found at RERA'], 422);
            }
            // }
        }catch (\Exception $e){
            info($e->getMessage());
            info($e->getLine());
            return [];
            return response()->json(['message' => 'Something went wrong!!','error'=>$e->getMessage(),'error_line'=>$e->getLine()], 500);
        }

    }
    public function propertyCheckRera(Request $request){
        $request->validate([
            'id' => 'required|exists:properties,id',
        ]);
        try{
            $header  = array();
            $header[] = 'Content-Type: application/json';
            $header[] = 'Content-Length: 4000';
            $header[] = 'Connection: keep-alive';
            $property = Property::find($request->id);
            $response = Http::post('https://secondary.axcrm.ae/api/rera/checkPermit', [
                'permit_number' => $property->rera_permit_number,
                'for' => $property->for,
                'api_token' => 'fYlQO814toCEdmdp'
            ]);
            $res = $response->json();
            if(empty($res)){
                $response =Http::post('https://secondary.axcrm.ae/api/rera/checkPermitWithoutQr', [
                    'permit_number' => $property->rera_permit_number,
                    'for' => $property->for,
                    'api_token' => 'fYlQO814toCEdmdp'
                ]);
                $res = $response->json();
            }
            //dd($res);
            //$res =  $this->reraCheckWithQr($property->rera_permit_number,$property->for);
        if(!empty($res)) {
            $qr = $res['qr'] ?? '';
            if($qr !=''){
                $url = $this->base64ToImage($qr);
                $property->qr_link = $url ?? null;
            }
            $property->rera_issue_date = date('Y-m-d',strtotime($res['start_date']));
            $property->rera_expiry_date = date('Y-m-d',strtotime($res['end_date']));
            $property->rera_validation_url = $res['validation_url'] ?? null;
            $property->withoutApproval()->save();
            $res['qr_link'] = $property->qr_link ?? null;
            return response()->json(['status'=>'1','message' => 'Property RERA Check Successfully', 'data' => $res], 200);
        }else{
            Property::where('id', $property->id)->update(['unable_to_generate_qr' => 1]);
            return response()->json(['status'=>'0','message' => 'Listing not found at RERA', 'data' => $res], 200);
        }
        }catch (\Exception $e){
            return response()->json(['status'=>'0','message' => 'Something went wrong!!','error'=>$e->getMessage(),'error_line'=>$e->getLine()], 500);
        }
    }
}
