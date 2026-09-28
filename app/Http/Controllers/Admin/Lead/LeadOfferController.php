<?php

namespace App\Http\Controllers\Admin\Lead;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
class LeadOfferController extends Controller
{
   public function storeOffer(Request $request)
   {
       abort_if(Gate::denies("Leads Offer Create"), 403);
       try {
           $request->validate([
               'amount' => 'required',
               'notes' => 'required',
           ]);
           $lead = Lead::find($request->lead_id);
           if (!$lead) {
               return redirect()->back()->with('error', 'Lead not found');
           }
           LeadOffer::where('lead_id', $request->lead_id)->update(['status' => 'inactive']);
           //remove comma
           $amount = str_replace(',', '', $request->amount);
           $offer = new LeadOffer();
           $offer->lead_id = $request->lead_id;
           $offer->amount = $amount;
           $offer->notes = $request->notes;
           $offer->created_by_id = auth()->id();
           if ($offer->save()) {
               $result = [
                   'status' => '1',
                   'message' => 'Offer created successfully',
               ];
               return response()->json($result);
           } else {
               $result = [
                   'status' => '0',
                   'message' => 'Unable to create offer',
               ];
               return response()->json($result);
           }
       }catch (\Exception $e){
           $result = [
               'status' => '0',
               'message' => 'Unable to create offer',
               'error' => $e->getMessage(),
           ];
           return response()->json($result);
       }

   }
}
