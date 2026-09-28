<?php

namespace App\Http\Controllers\Admin\Deals;

use App\Events\DealCreated;
use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\DealNote;
use App\Models\DealSource;
use App\Models\DealSplit;
use App\Models\DealStatus;
use App\Models\DealSubStatus;
use App\Models\DealTask;
use App\Models\DealType;
use App\Models\FinanceType;
use App\Models\Note;
use App\Models\Property;
use App\Models\TransactionType;
use App\Models\User;
use App\Traits\FiltersTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
class DealsController extends Controller
{
    use FiltersTrait;

    public function index()
    {
        abort_if(Gate::denies("Deals List"), 401);
        $dealTypes = DealType::where("status", true)->get();
        if (Cache::has('transactionTypes')) {
            $transactionTypes = Cache::get('transactionTypes');
        } else {
            $transactionTypes = TransactionType::where("status", true)->get();
            Cache::put('transactionTypes', $transactionTypes, 6000);
        }
        $dealStatuses = DealStatus::where("status", true)->get();
        $dealSubStatuses = DealSubStatus::where("status", true)->get();
        $financeTypes = FinanceType::where("status", true)->get();
        $dealSources = DealSource::where("status", true)->get();
        $agents = User::whereHas("roles", function ($q) {
            $q->where("name", "Agent");
        })->select('id','first_name','last_name')->where('status',true)->get();
        return view('admin.deals.index',
            compact(
                "dealTypes", "transactionTypes",
                "dealStatuses", "dealSubStatuses", "financeTypes",
                 "dealSources","agents"));
    }
    public function create($id)
    {
        $data['deals'] = Deal::where('id', $id)->with('dealType', 'dealStatus', 'agent', 'property', 'property.price', 'property.photos', 'contact', 'createdBy')->first();

        return view('admin.deals.create')->with($data);
    }

    //wizard form

    public function wizard(Request $request){
        $id = $request->id;
        if($id) {
            $data['deal'] = Deal::where('id', $id)->with('dealType', 'dealStatus', 'agent', 'property', 'property.price', 'property.photos',
                'property.bed', 'property.bath',
                'contact', 'createdBy')->first();
        }else{
            $data['deal'] = null;
        }
        return view('admin.deals.wizard.index')->with($data);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "agent_id" => "required|exists:users,id",
            "deal_type_id" => "nullable|exists:deal_types,id",
            "transaction_type_id" => "required|exists:transaction_types,id",
            "lead_id" => "required|exists:leads,id",
            "contact_id" => "required_if:deal_type_id,!=,3|exists:contacts,id",
            "property_id" => "required|exists:properties,id",
            "deal_source_id" => "nullable|exists:contacts,id",
            "deal_status_id" => "required|exists:contacts,id",
            "deal_sub_status_id" => "required|exists:contacts,id",
            "deal_price" => "required",
            "deposit" => "required|numeric",
            "finance_type_id" => "nullable|exists:contacts,id",
            "gross_commission" => "nullable|numeric",
            "include_commission_vat" => "nullable",
//            "gross_commission_vat_amount" => "nullable|numeric",
//            "total_gross_commission_vat_amount" => "nullable|numeric",
          //  "agent_commission" => "nullable|numeric",
       //     "agent_commission_amount" => "nullable|numeric",
        ]);

        try {
            $validatedData["created_by_id"] = auth()->id();
            $validatedData["deal_price"] = str_replace(",", "", $request->deal_price);
            $validatedData["include_commission_vat"] = $request->boolean("include_commission_vat");
            Deal::create($validatedData);
            return response()->json(['status' => '1', 'msg' => 'Deal Added Successfully']);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function initialDataStore(Request $request){
        $validatedData = $request->validate([
            "agent_id" => "nullable|exists:users,id",
            "property_id" => "required|exists:properties,id",
            "lead_ids" => "required|array|exists:leads,id",
            "deal_price" => "required",
            "deal_type_id" => "nullable|exists:deal_types,id",
        ]);

        try {
            $property = Property::find($request->property_id);
            $deal = new Deal();
            $deal->agent_id = $property->agent_id;
            $deal->property_id = $property->id;
            $deal->contact_id = $property->owner_id;
            $deal->deal_price =   str_replace(",", "", $request->deal_price);
            $deal->deal_type_id = $request->deal_type_id;
            $deal->created_by_id = auth()->id();
            if($deal->save()) {

                if ($validatedData['lead_ids']) {
                    $deal->leads()->sync($validatedData['lead_ids']);
                }
                $tasks = [
                    ['deal_id'=>$deal->id,'user_id'=>$property->agent_id,'subject' =>'Approve Draft','description'=>'Task testing description','due_date'=>Carbon::today()->format('Y-m-d'),'priority'=>'low','created_by_id'=>auth()->user()->id],
                    ['deal_id'=>$deal->id,'user_id'=>$property->agent_id,'subject' =>'KYC & AML Check','description'=>'Task testing description','due_date'=>Carbon::today()->format('Y-m-d'),'priority'=>'high','created_by_id'=>auth()->user()->id],
                    ['deal_id'=>$deal->id,'user_id'=>$property->agent_id,'subject' =>'Lead Documents','description'=>'Task testing description','due_date'=>Carbon::today()->format('Y-m-d'),'priority'=>'low','created_by_id'=>auth()->user()->id],
                ];

                $task = DealTask::insert($tasks);
                //call a php artisan commmand to calculate the commission
//                \Artisan::call('calculate:commission', [
//                    'deal_id' => $deal->id,
//                ]);

                event(new DealCreated($deal));

                return response()->json(['status' => '1', 'msg' => 'Deal Added Successfully', 'data' => $deal]);
            }else{
                return response()->json(['status' => '0', 'msg' => 'Something went wrong!!']);
            }
        } catch (Exception $e) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return json_encode($result);
        }
    }

    public function getDealCreateForm(Request $request){
        return view('admin/deals/include/createForm', get_defined_vars())->render();
    }

    public function dataTable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',

        );

        $notAdmin = auth()->user()->hasRole(["Sales Director","Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin){
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $q = Deal::query();
        $totalData = $this->applyDealFilters($q, $request)->when($notAdmin, fn($q) => $q->whereIn("agent_id", $childrenIds))->count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $query = Deal::query();
            $data = $this->applyDealFilters($query, $request)  ->with('createdBy','updatedBy', 'dealType', 'property','property.subCommunity','property.communityProperty','property.price', 'agent', 'dealStatus')
                ->when($notAdmin, fn($q) => $q->whereIn("agent_id", $childrenIds))->get();
        } else {
            $query = Deal::query();
            $data = $this->applyDealFilters($query, $request)->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->offset($request['start'])
                ->limit($request['length'])
                ->with('createdBy','updatedBy', 'dealType', 'property','property.subCommunity','property.communityProperty','property.price', 'agent', 'dealStatus')
                ->when($notAdmin, fn($q) => $q->whereIn("agent_id", $childrenIds))
                ->get();
        }

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);
    }

    public function getDeal(Request $request)
    {
        $contact = Deal::where('id', $request->id)->with('dealType', 'dealStatus', 'agent', 'property', 'property.price', 'property.photos', 'contact', 'createdBy','dealMeta','progression')->first();
        if (!empty($contact)) {
            return response()->json(['status' => '1', 'msg' => 'Deal Found', 'data' => $contact]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Contact Not Found']);
        }
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:deals,id",
            "agent_id" => "required|exists:users,id",
            "deal_type_id" => "required|exists:deal_types,id",
            "transaction_type_id" => "required|exists:transaction_types,id",
            "lead_id" => "required|exists:leads,id",
            "contact_id" => "required_if:deal_type_id,!=,3|exists:contacts,id",
            "property_id" => "required|exists:properties,id",
            "deal_source_id" => "nullable|exists:contacts,id",
            "deal_status_id" => "required|exists:contacts,id",
            "deal_sub_status_id" => "required|exists:contacts,id",
            "deal_price" => "required|numeric",
            "deposit" => "required|numeric",
            "finance_type_id" => "nullable|exists:contacts,id",
            "gross_commission" => "nullable|numeric",
            "include_commission_vat" => "nullable",
            "gross_commission_vat_amount" => "nullable|numeric",
            "total_gross_commission_vat_amount" => "nullable|numeric",
            "agent_commission" => "nullable|numeric",
            "agent_commission_amount" => "nullable|numeric",
        ]);

        try {
            $validatedData["updated_by_id"] = auth()->id();
            $validatedData["include_commission_vat"] = $request->boolean("include_commission_vat");
            $deal = Deal::find($request->id);
            $deal->update($validatedData);
            return response()->json(['status' => '1', 'msg' => 'Deal updated Successfully']);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:deals',

        ]);

        $type = Deal::find($request->id);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => 'Deal deleted successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',

        ]);
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;
            if (Deal::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Deals deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }

        return json_encode($result);
    }

    public function addNote(Request $request)
    {
        $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'note' => 'required|string',
        ]);

        try {
            DealNote::create([
                "deal_id" => $request->deal_id,
                "note" => $request->note,
                "created_by_id" => auth()->id(),
            ]);
            $result = array('status' => '1', 'msg' => "Deal note added successfully.");
            return json_encode($result);
        }catch (\Exception $exception){
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return json_encode($result);
        }
    }



}
