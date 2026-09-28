<?php

namespace App\Http\Controllers\Api\V1\Lead;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\ContactMedia;
use App\Models\ContactMeta;
use App\Models\Deal;
use App\Models\DealDeductibleType;
use App\Models\DealMedia;
use App\Models\DealMeta;
use App\Models\DealSplit;
use App\Models\DealTask;
use App\Models\CommissionSlab;
use App\Models\CommissionSlabUser;
use App\Models\DealNote;
use App\Models\DealSource;
use App\Models\DealStatus;
use App\Models\Lead;
use App\Models\LeadMedia;
use App\Models\DealSplitCommission;
use App\Models\LeadMeta;
use App\Models\DealSubStatus;
use App\Models\DealType;
use App\Models\FinanceType;
use App\Models\LeadSource;
use App\Models\Medium;
use App\Models\Note;
use App\Models\Property;
use App\Models\PropertyMedia;
use App\Models\TransactionType;
use App\Models\User;
use App\Traits\FileTrait;
use App\Traits\FiltersTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use DB;
use validator;
use App\Models\DealDeductible;
use App\Models\DealCommissionSource;

class LeadsApiController extends Controller
{
    use FiltersTrait, FileTrait;

    public function getLeadMedia(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
        ]);
        try {
            $media = LeadMedia::where('deal_id', $request->lead_id)->with('createdBy')->get();
            if ($media) {
                return response()->json(['status' => '1', 'msg' => 'Media Found', 'data' => $media]);
            } else {
                return response()->json(['status' => '0', 'msg' => 'Media Not Found']);
            }
        } catch (Exception $e) {
            // Handle any exceptions here
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }
}
