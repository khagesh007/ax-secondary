<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\OffplanProperty;
use App\Models\TempDump;
use DB;
use Illuminate\Http\Request;

class OffplanPropertiesWebhookController extends Controller
{

    public function show()
    {
        // show latest 20 OffplanProperty
        $offplan = OffplanProperty::orderBy('id', 'desc')->take(20)->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Webhook received successfully',
            'data' => $offplan,
        ]);
    }

    public function handle(Request $request)
    {

        try {
            $dump = TempDump::create([
                'data' => json_encode($request->all()),
                'name' => 'OFFPLAN',
                'flags' => $request->developer_id,
            ]);

            // Log::channel('offplan')->info('Webhook Received', ['data' => $request->all()]);
            try {
                $req = $request->all();
                $property = $req;



                $already_offplan = OffplanProperty::where('developer_id',$property['developer_id'])->where('project_name', $property['project_name'])->where('unit_no', $property['unit_no'])->first();
                if ($already_offplan) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Webhook received successfully',
                    ]);
                } else {
                    DB::beginTransaction();
                    $offplan = new OffplanProperty();
                    $offplan->unique_id = rand(10, 99999) . rand(10, 9999);
                    $offplan->developer_id = $property['developer_id'] ?? null;
                    $offplan->unit_no = $property['unit_no'] ?? null;
                    $offplan->completion_status = 'Off-Plan Primary';
                    $offplan->title = $property['title'] ?? $property['project_name'] . ' | ' . $property['developer_name'] ?? null;
                    // $offplan->name = $property['unit_no'];
                    $offplan->description = $property['description'] ?? 'A Project (' . $property['unit_no'] . ') by ' . $property['developer_name'] ?? null;
                    // $offplan->address_details = $property['address_details']??null;
                    $offplan->bath_id = $property['bath'] ?? 'null';
                    $offplan->bed_id = $property['bed'] ?? 'null';
                    $offplan->project_name = $property['project_name'] ?? null;
                    $offplan->building_location = $property['unit_no'] ?? null;
                    $offplan->community_name = $property['location_code']['building'] ?? null;
                    $offplan->property_name = $property['location_code']['property'] ?? null;
                    $offplan->location_code = $property['location_code']['location'] ?? null;
                    // $offplan->location_id = $property['unit_no'] ?? null;
                    $offplan->parking = $property['parking'] ?? null;
                    $offplan->plot_size = $property['plot_size'] ?? null;
                    $offplan->total_area = $property['total_area'] ?? null;
                    $offplan->property_type = $property['property_type'] ?? null;
                    $offplan->property_usage = $property['property_usage'] ?? null;
                    $offplan->selling_price = $property['selling_price'] ?? null;
                    $offplan->price = $property['price'] ?? null;
                    $offplan->land_registration_fee = $property['land_registration_fee'] ?? null;
                    $offplan->applicable_fee = $property['applicable_fee'] ?? null;
                    $offplan->total_amount = $property['total_amount'] ?? null;
                    $offplan->data = json_encode($property['miscellaneous']) ?? null;
                    $offplan->location_data = json_encode($property['location_code']) ?? null;
                    $offplan->save();
                    DB::commit();
                }

                // Log::channel('offplan')->info('Webhook Received and saveed with ID: '.$offplan->id , ['id' => $offplan->id, 'data' => $offplan]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Webhook received successfully',
                    'data' => $offplan,
                ]);
            } catch (\Throwable $th) {
                DB::rollback();
                info($th);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Something went wrong ' . $th->getMessage(),
                    'data' => $dump,
                ]);

            }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ]);
        }

    }

    public function getDeveloper($id)
    {
        $arry = [
            11 => 1,
            12 => 2,
            13 => 3,
            14 => 4,
            15 => 5,
            16 => 6,
            17 => 7,
        ];

        return $arry[$id] ?? null;
    }
}
