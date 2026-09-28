<?php

namespace App\Http\Controllers\Api\V1\Deals;

use App\Http\Controllers\Controller;
use App\Models\DealCommissionSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DealCommissionSourceApiController extends Controller
{
    public function storeDealCommissionSource(Request $request)
    {

        $request->validate([
            'deal_id' => 'required',
            'amount' => 'required',
            'source' => 'required',
            'is_vat_applicable' => 'required',
        ]);

        try {
            DB::beginTransaction();
            foreach ($request->deal_id as $key => $deal_id) {
                $dealCommissionSource = new DealCommissionSource();
                $dealCommissionSource->deal_id = $deal_id;
                $dealCommissionSource->amount = $request->amount[$key];
                $dealCommissionSource->source = $request->source[$key];
                $dealCommissionSource->is_vat_applicable = $request->is_vat_applicable[$key];
                $dealCommissionSource->vat_percentage = config('deals.vat');

                $vat_value = config('deals.vat');
                $vat_type = config('deals.vat_type');

                if ($request->is_vat_applicable[$key] == 1) {
                    if ($vat_type == 'percentage') {
                        $dealCommissionSource->vat_amount = ($request->amount[$key] * $vat_value) / 100;
                    } else {
                        $dealCommissionSource->vat_amount = $vat_value;
                    }
                } else {
                    $dealCommissionSource->vat_amount = 0;
                }

                $dealCommissionSource->created_by_id = auth()->user()->id;
                $dealCommissionSource->updated_by_id = auth()->user()->id;
                $dealCommissionSource->save();
            }
            DB::commit();
            return response()->json(['status' => '1', 'msg' => 'Deal Commission Source(s) Created Successfully', 'data' => []]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['status' => '0', 'msg' => 'Something Went Wrong', 'data' => []]);
        }

    }
}
