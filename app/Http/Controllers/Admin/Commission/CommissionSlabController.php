<?php

namespace App\Http\Controllers\Admin\Commission;

use App\Http\Controllers\Controller;
use App\Models\CommissionSlab;
use App\Models\CommissionSlabLimit;
use App\Models\CommissionSlabUser;
use App\Traits\FileTrait;
use Auth;
use DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class CommissionSlabController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    use FileTrait;
    public function index()
    {
        abort_if(Gate::denies("Commission Slab List"), 401);
        $data['roles'] = Role::where("name", "<>", "Super Admin")->get();
        return view('admin.commission_slabs.index')->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function datatable(Request $request)
    {
        $notAdmin = auth()->user()->hasRole(["Sales Director", "Team Leader", "Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $query = CommissionSlab::with(['approvedBy', 'createdBy', 'updatedBy'])
            ->when($notAdmin, function ($query) use ($childrenIds) {
                $query->whereIn('user_id', $childrenIds);
            });
        return DataTables::of($query)
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'frequency' => 'nullable|in:Daily,Weekly,Monthly,Quarterly,Biannually,Annually,Not applicable',
            'commission_type' => 'nullable|in:Percentage,Fixed',
            'commission_value' => 'nullable|numeric',
            'commission_category' => 'nullable|in:Direct,Indirect',
            'calculation_from' => 'nullable|in:Net Commission,Remaining Amount',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'icon' => 'nullable|string',
            'badge' => 'nullable|string',
            'order' => 'nullable|integer',
            'status' => 'nullable',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => '2', 'errors' => $validator->errors()]);
        }

        try {

            $obj = new CommissionSlab();
            $obj->name = $request->name;
            $obj->description = $request->description;
            $obj->frequency = $request->frequency ?? 'Monthly';
            $obj->commission_type = $request->commission_type;
            $obj->commission_value = $request->commission_value;
            $obj->commission_category = $request->commission_category;
            $obj->calculation_from = $request->calculation_from;
            $obj->status = $request->status == 'on' ? 1 : 0;
            $obj->icon = $request->icon;
            $obj->slug = Str::slug($request->name, "-");
            $obj->badge = $request->badge;
            $obj->order = $request->order ?? 100;
            $obj->created_by_id = Auth::user()->id;

            if ($request->has('thumbnail')) {
                $image = $this->uploadImage($request->file("thumbnail"));
                $obj->thumbnail = $image;
            }
            if ($request->has('image')) {
                $image = $this->uploadImage($request->file("image"));
                $obj->image = $image;
            }

            if($obj->save()){
                $clu = new CommissionSlabUser();
                $clu->role_id = $request->role_id ?? null;
                $clu->user_id = $request->user_id ?? null;
                $clu->commission_slab_id = $obj->id;
                $clu->save();
            }

            $result = array('status' => '1', 'msg' => 'Commission Slab added successfully');
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:commission_slabs,id',
        ]);
        try {
            $cat = CommissionSlab::with('commissionLimits','commissionSlabUsers.user')->find($request->id);
            $result = array('status' => '1', 'msg' => 'Slab details fetched successfully', 'data' => $cat);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return json_encode($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:commission_slabs,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'frequency' => 'nullable|in:Daily,Weekly,Monthly,Quarterly,Biannually,Annually,Not applicable',
            'commission_type' => 'nullable|in:Percentage,Fixed',
            'commission_value' => 'nullable|numeric',
            'commission_category' => 'nullable|in:Direct,Indirect',
            'calculation_from' => 'nullable|in:Net Commission,Remaining Amount',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'icon' => 'nullable|string',
            'badge' => 'nullable|string',
            'order' => 'nullable|integer',
            'status' => 'nullable',
        ]);
        try {
            $obj = CommissionSlab::find($request->id);
            $obj->name = $request->name;
            $obj->description = $request->description;
            $obj->frequency = $request->frequency ?? 'Monthly';
            $obj->commission_type = $request->commission_type;
            $obj->commission_value = $request->commission_value;
            $obj->commission_category = $request->commission_category;
            $obj->calculation_from = $request->calculation_from;
            $obj->status = $request->status == 'on' ? 1 : 0;
            $obj->icon = $request->icon;
            $obj->slug = Str::slug($request->name, "-");
            $obj->badge = $request->badge;
            $obj->order = $request->order ?? 100;
            $obj->updated_by_id = Auth::user()->id;

            if ($request->has('thumbnail')) {
                $image = $this->uploadImage($request->file("thumbnail"));
                $obj->thumbnail = $image;
            }
            if ($request->has('image')) {
                $image = $this->uploadImage($request->file("image"));
                $obj->image = $image;
            }

            if($obj->save()){
                   CommissionSlabUser::where('commission_slab_id',$obj->id)->delete();
                    $clu = new CommissionSlabUser();
                    $clu->role_id = $request->role_id ?? null;
                    $clu->user_id = $request->user_id ?? null;
                    $clu->commission_slab_id = $obj->id;
                    $clu->save();

            }
            $result = array('status' => '1', 'msg' => 'Commission Slab updated successfully','data'=>$obj);
            return json_encode($result);
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
            return json_encode($result);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function delete(Request $request)
    {

        $request->validate([
            'ids' => 'required|array',
        ]);
        if (gettype($request->ids) == 'array' && count($request->ids) > 0) {
            $ids = $request->ids;

            if (CommissionSlab::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids) . ' Slab deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

        }

        return json_encode($result);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sources',
            'action' => 'required',
        ]);

        $status = CommissionSlab::find($request->id);
        $status->status = $request->boolean('action');
        $status->updated_by_id = auth()->user()->id;
        if ($status->save()) {
            $result = array('status' => '1', 'msg' => ' Slab updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }

    public function createSlabItem(Request $request): JsonResponse
    {
        $request->validate([
            'commission_slab_id' => 'required|exists:commission_slabs,id',
            'from_value' => 'required|string',
            'to_value' => 'required|string',
            'commission_type' => 'required|in:Percentage,Fixed',
            'commission_value' => 'required|numeric',
        ]);

        $slab_item = new CommissionSlabLimit();
        $slab_item->commission_slab_id = $request->commission_slab_id;
        $slab_item->from_value = $request->from_value;
        $slab_item->to_value = $request->to_value;
        $slab_item->commission_type = $request->commission_type;
        $slab_item->commission_value = $request->commission_value;
        $slab_item->created_by_id = auth()->user()->id;
        if ($slab_item->save()) {
            $result = array('status' => '1', 'msg' => ' Slab item created successfully','data'=>$slab_item);
            return response()->json($result);
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return  response()->json($result);
        }
    }

    public function updateSlabItem(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'id' => 'required|exists:commission_slab_limits',
            'commission_slab_id' => 'required|exists:commission_slabs,id',
            'from_value' => 'required|string',
            'to_value' => 'required|string',
            'commission_type' => 'required|in:Percentage,Fixed',
            'commission_value' => 'required|numeric',
        ]);
        $slab_item = CommissionSlabLimit::find($request->id);
        $slab_item->commission_slab_id = $request->commission_slab_id;
        $slab_item->from_value = $request->from_value;
        $slab_item->to_value = $request->to_value;
        $slab_item->commission_type = $request->commission_type;
        $slab_item->commission_value = $request->commission_value;
        $slab_item->created_by_id = auth()->user()->id;
        if ($slab_item->save()) {
            $result = array('status' => '1', 'msg' => ' Slab item Updated successfully','data'=>$slab_item);
            return  response()->json($result);
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return  response()->json($result);
        }
    }

    public function getSlabLimits(Request $request){
        $request->validate([
            'id' => 'required|exists:commission_slabs,id',
        ]);
        $data['commission_limits'] = CommissionSlabLimit::where('commission_slab_id',$request->id)->get();
        $result = array('status' => '1', 'msg' => ' Slab item fetched successfully','data'=>$data);
        return response()->json($result);
      //  return view('admin.commission_slabs.includes.component.slab-limits')
         //   ->with('limits',$limits);
    }
    public function deleteSlabLimits(Request $request){
        $request->validate([
            'id' => 'required|exists:commission_slabs,id',
        ]);
    try {
        $del = CommissionSlabLimit::where('id', $request->id)->delete();
        if($del) {
            $result = array('status' => '1', 'msg' => ' Slab item deleted successfully');
            return response()->json($result);
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            return response()->json($result);
        }
    }catch (\Exception $exception){
        $result = array('status' => '0', 'msg' => 'Something went wrong!!','error'=>$exception->getMessage());
        return response()->json($result);
    }

    }
}
