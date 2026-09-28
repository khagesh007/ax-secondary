<?php

namespace App\Http\Controllers\Admin\Deals;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TransactionType;
use Auth;
use Illuminate\Support\Facades\Gate;

class TransactionTypeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies("Transaction Types List"), 401);
        return view('admin.deals.transactiontype');
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string",
        ]);

        try {
            $category = TransactionType::create([
                "name" => $request->name,
                "status" => $request->boolean("status"),
                "created_by_id" => auth()->id(),
            ]);
            $result = array('status' => '1', 'msg' => 'Transaction type added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    public function transactionForDatatable(Request $request)
    {
        $columns = array(
            0 => 'updated_at',
            1 => 'id',
            2 => 'name'
        );
        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $totalData = TransactionType::count();
        $totalFiltered = $totalData;

        

        if (!empty($searchKeyWord)) {
            $data_filtered = TransactionType::where(function ($q) use ($searchKeyWord) {
                $q->where('name', 'LIKE', '%' . $searchKeyWord . '%');
            })->with('created_by', 'updated_by');

            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->get();
        } else {
            $data_filtered = TransactionType::orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->with('created_by', 'updated_by');
            $totalFiltered = $data_filtered->count();
            $data = $data_filtered->limit($request['length'])->offset($request['start'])->get();
        }

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);
    }

    public function getTransaction(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:transaction_types,id',
        ]);
        try {
            $cat = TransactionType::find($request->id);
            $result = array('status' => '1', 'msg' => 'Transaction type details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }


    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:transaction_types,id',
            "name" => "required|string",
            // 'thumbnail' => 'nullable|string',
            // 'image' => 'nullable|string',
            // 'color_code' => 'nullable|string',
        ]);

        try {
            $category = TransactionType::find($request->id);
            $category->update([
                "name" => $request->name,
                // 'slug' => Str::slug($request->title, "-"),
                // "color_code" => $request->color_code,
                "status" => $request->boolean("status"),
                "updated_by_id" => auth()->id(),
            ]);
            $result = array('status' => '1', 'msg' => 'Transaction type details updated successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:transaction_types',
            'action' => 'required',
        ]);

        $type = TransactionType::find($request->id);
        $type->status = $request->boolean('action');
        $type->updated_by_id = auth()->user()->id;
        if ($type->save()) {
            $result = array('status' => '1', 'msg' => 'Transaction type updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:transaction_types,id',

        ]);

        $type = TransactionType::find($request->id);
        if ($type->delete()) {
            $result = array('status' => '1', 'msg' => 'Transaction type deleted successfully');
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
            if (TransactionType::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Transaction type deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }
        return json_encode($result);
    }
}
