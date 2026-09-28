<?php

namespace App\Http\Controllers\Admin\Jobs;

use App\Events\ShootingRequestCreated;
use App\Events\ShootJobAction;
use App\Events\ShootJobCreated;
use App\Events\ShootJobStatusChange;
use App\Events\ShootRequestCreated;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Job;
use App\Models\PropertyNote;
use App\Models\PropertyShootBookingNote;
use App\Models\Property;
use App\Models\PropertyShootBooking;
use App\Models\User;
use App\Traits\FiltersTrait;
use Carbon\Carbon;
use FontLib\Table\Type\post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ShootController extends Controller
{
    use FiltersTrait;

    public function index(Request $request)
    {
        abort_if(Gate::denies("Property Shoot List"), 401);
        $notAdmin = auth()->user()->hasRole(["Sales Director","Line Manager", "Agent"]);
        $childrenIds = [];
        if ($notAdmin){
            $childrenIds = getUserChildrenIds(auth()->id());
        }
        $data['agents'] = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Agent"]);
        })->when($notAdmin, fn($q) => $q->whereIn("parent_id", $childrenIds))->where("status", true)->get();
        if($request->id){
            $data['booking'] = PropertyShootBooking::where('id',$request->id)->with('property','property.bed','property.propertyCategory','property.bed','property.bath','requestedBy','approvedBy')->first();

        }else{
            $data['booking'] = null;
            $data['photographers'] = null;
        }
        $data['photographers'] = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Photographer"]);
        })->where("status", true)->get();
        $data['admins'] = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Admin","Super Admin","Sales Director","Line Manager"]);
        })->where("status", true)->get();
        return view("admin.jobs.shoot.index")->with($data);
    }
    public function getFormModal(Request $request)
    {
       if($request->agent_id !=''){
              $agent = User::where('id',$request->agent_id)->first();
    }else{
        $agent = null;
       }
       if($request->property_id !=''){
                $property = Property::where('id',$request->property_id)->first();
    }else{
           $property = null;
       }

//        $agents = User::whereHas("roles", function ($q) {
//            $q->whereIn("name", ["Agent"]);
//        })->where("status", true)->get();

        $html = view('admin.jobs.shoot.modal',get_defined_vars())->render();

        return response()->json(['status' => true,'html' => $html]);
    }
    public function getPhotographerTimeline(Request $request)
    {
        $photographers = User::whereHas("roles", function ($q) {
            $q->whereIn("name", ["Photographer"]);
        })->with(['events' => function ($q) use ($request) {
            $q->whereDate('start_time', Carbon::parse($request->date)->format('Y-m-d'));
        }])->where("status", true)->get() ?? [];
        $timeSlots = generateTimeSlots('09:00', '17:00');
        $date = $request->date;
        $html = view('admin.jobs.shoot.pgTimeline',get_defined_vars())->render();
        if(count($photographers) > 0) {
            return response()->json(['status' => true, 'html' => $html]);
        }else{
            $userPage = route('admin.user.users.index');
            return response()->json(['status'=>true, 'html'=>'<h6 class="text-muted text-center">Photographers not found. Please add one. <a target="_blank" href="'.$userPage.'">Here</a></h6>']);
        }
    }


    public function store(Request $request)
    {

        $validatedData = $request->validate([
            "agent_id" => "required|exists:users,id",
            "property_id" => "required|exists:properties,id",
            "description" => "nullable|string",
            "title" => "required|string",
            "access_information" => "nullable",
            "preferred_time" => "required",
            "requested_tags" => "nullable|array",
            "dewa" => "string|nullable",
            "furnished" => "string|nullable",
            "cleaned" => "string|nullable",
            "luxury" => "string|nullable",
            "agent_present" => "string|nullable",

        ]);
        try {
            $isLuxury = false;
            $property = Property::where('id',$validatedData['property_id'])->with('price')->first();
            if($property?->price?->price> 3000000 || isset($validatedData['luxury'])){
                $isLuxury = true;
            }
            $obj = new PropertyShootBooking();
            $obj->property_id           = $validatedData["property_id"];
            $obj->title                 = $validatedData["title"];
            $obj->description           = $validatedData["description"];
            $obj->access_information    = $validatedData["access_information"];
            $obj->requested_tags        = json_encode($validatedData["requested_tags"]);
            $obj->preferred_time        = Carbon::parse($validatedData["preferred_time"])->format("Y-m-d H:i:s");
            $obj->is_luxury             = $isLuxury;
            $obj->dewa                  = isset($validatedData["dewa"]) ? 1 : 0;
            $obj->furnished             = isset($validatedData["furnished"]) ? 1 : 0;
            $obj->cleaned               = isset($validatedData["cleaned"]) ? 1 : 0;
            $obj->agent_present         = isset($validatedData["agent_present"]) ? 1 : 0;
            $obj->requested_by_id       = $validatedData["agent_id"];
            $obj->created_by_id         = auth()->id();
            if($obj->save()){



                if($validatedData['property_id']){
                    PropertyNote::create([
                        "property_id" => $validatedData['property_id'],
                        "note" => "New property shoot request created",
                        "created_by_id" => auth()->id(),
                    ]);
                }
                $note = new PropertyShootBookingNote();
                $note->property_shoot_booking_id = $obj->id;
                $note->note = "New property shoot request created";
                $note->created_by_id = auth()->id();
                $note->save();
                event(new ShootingRequestCreated($obj));

                $result = array('status' => '1', 'msg' => 'Bookings Added Successfully. And waiting for approval','data' => $obj);
            }else{
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return response()->json($result);
    }

    public function getJob(Request $request)
    {
        $request->validate([
            "id" => "required|exists:property_shoot_bookings,id"
        ]);
        $job = PropertyShootBooking::where('id',$request->id)->with('property','requestedBy','approvedBy')->first();
        if(!is_null($job)){
            return response()->json(['status' => '1', 'msg' => 'Job Found', 'data' => $job]);
        }else{
            return response()->json(['status' => '0', 'msg' => 'Job Not Found']);
        }
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            "id" => "required|exists:property_shoot_bookings,id",
            "agent_id" => "required|exists:users,id",
            "property_id" => "required|exists:properties,id",
            "description" => "nullable|string",
            "title" => "required|string",
            "access_information" => "nullable",
            "preferred_time" => "required",
            "requested_tags" => "nullable|array",
            "dewa" => "string|nullable",
            "furnished" => "string|nullable",
            "cleaned" => "string|nullable",
            "luxury" => "string|nullable",
            "agent_present" => "string|nullable",

        ]);
        try {
            $isLuxury = false;
            $property = Property::where('id',$validatedData['property_id'])->with('price')->first();
            if($property?->price?->price> 3000000 || isset($validatedData['luxury'])){
                $isLuxury = true;

            }
            $obj = PropertyShootBooking::find($request->id);
            $obj->property_id           = $validatedData["property_id"];
            $obj->title                 = $validatedData["title"];
            $obj->description           = $validatedData["description"];
            $obj->access_information    = $validatedData["access_information"];
            $obj->requested_tags        = json_encode($validatedData["requested_tags"]);
            $obj->preferred_time        = $validatedData["preferred_time"];
            $obj->is_luxury              = $isLuxury;
            $obj->dewa                  = isset($validatedData["dewa"]) ? 1 : 0;
            $obj->furnished             = isset($validatedData["furnished"]) ? 1 : 0;
            $obj->cleaned               = isset($validatedData["cleaned"]) ? 1 : 0;
            $obj->agent_present         = isset($validatedData["agent_present"]) ? 1 : 0;
            $obj->requested_by_id       = $validatedData["agent_id"];
            $obj->created_by_id         = auth()->id();
            if($obj->save()){


                $result = array('status' => '1', 'msg' => 'Bookings updated Successfully. And waiting for approval','data' => $obj);
            }else{
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        } catch (\Exception $exception) {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!', 'error' => $exception->getMessage());
        }
        return response()->json($result);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_shoot_bookings,id',
        ]);

        $obj = PropertyShootBooking::find($request->id);
        if ($obj->delete()) {
            $result = array('status' => '1', 'msg' => 'Property shoot request deleted successfully');
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
            if (PropertyShootBooking::whereIn('id', $ids)->delete()) {
                $result = array('status' => '1', 'msg' => count($ids).' Request deleted successfully');
            } else {
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }
        }

        return json_encode($result);
    }

    public function dataTable(Request $request)
    {
        $columns = array(
            0 => 'id',
            1 => 'id',
            2 => 'type',
            3 => 'requested_by_id',
            4 => 'assigned_to_id',
            5 => 'approved_by_id',
            6 => 'preferred_time',
            7 => 'priority',
            8 => 'created_by_id',
            9 => 'updated_at',
            10 => 'status',
        );

        $notAdmin = auth()->user()->hasRole(["Sales Director","Line Manager", "Agent",'Photographer']);
        $childrenIds = [];
        if ($notAdmin){
            $childrenIds = getUserChildrenIds(auth()->id());
        }


        $searchKeyWord = htmlspecialchars($request['search']['value']);
        $q = PropertyShootBooking::query();
        $totalData = $this->applyShootJobFilters($q, $request)->when($notAdmin, function ($q) use ($childrenIds){
            $q->whereIn("requested_by_id", $childrenIds);
            $q->orWhereIn("assigned_to_id", $childrenIds);
            $q->orWhereIn("approved_by_id", $childrenIds);
            $q->orWhereIn("created_by_id", $childrenIds);
            $q->orWhere("assigned_to_id", auth()->id());
        })->count();
        $totalFiltered = $totalData;

        if (!empty($searchKeyWord)) {
            $query = PropertyShootBooking::query();
            $data = $this->applyShootJobFilters($query, $request)->with('createdBy', 'requestedBy', 'approvedBy', 'assignedTo')
                ->when($notAdmin, function ($q) use ($childrenIds){
                    $q->whereIn("requested_by_id", $childrenIds);
                    $q->orWhereIn("assigned_to_id", $childrenIds);
                    $q->orWhereIn("approved_by_id", $childrenIds);
                    $q->orWhereIn("created_by_id", $childrenIds);
                    $q->orWhere("assigned_to_id", auth()->id());
                }) ->with('createdBy', 'requestedBy', 'approvedBy', 'assignedTo','rejectedBy','property','property.community')->get();
        } else {
            $query = PropertyShootBooking::query();
            $data = $this->applyShootJobFilters($query, $request)->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
                ->offset($request['start'])
                ->limit($request['length'])
                ->with('createdBy', 'requestedBy', 'approvedBy', 'assignedTo','rejectedBy','property','property.community')
                ->when($notAdmin, function ($q) use ($childrenIds){
                    $q->whereIn("requested_by_id", $childrenIds);
                    $q->orWhereIn("assigned_to_id", $childrenIds);
                    $q->orWhereIn("approved_by_id", $childrenIds);
                    $q->orWhereIn("created_by_id", $childrenIds);
                    $q->orWhere("assigned_to_id", auth()->id());
                })->with('property')
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



    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_shoot_bookings',
            'status' => 'required|string',
        ]);

        $status = PropertyShootBooking::find($request->id);
       if ($request->status == "Rejected"){
            $status->rejected_by_id = auth()->id();
            $status->rejected_at = Carbon::now();
        }
        $status->status = $request->status;
        $status->updated_by_id = auth()->id();
        if ($status->save()) {
            if($status->status == "Completed"){


                if($status->pending_notification != null) {
                    $pending_notification = json_decode($status->pending_notification);
                    $pending_notification->job_completed = 'Shoot job completed.';
                    $status->pending_notification = json_encode($pending_notification);
                    $status->save();
                }else{
                    $pending_notification = new \stdClass();
                    $pending_notification->job_completed  = 'Shoot job completed.';
                    $status->pending_notification = json_encode($pending_notification);
                    $status->save();
                }

                if($status->property_id){
                    PropertyNote::create([
                        "property_id" => $status->property_id,
                        "note" => "New ".( $status->is_luxury == 1 ? 'Luxury' : 'Standard')." property shoot has been completed.",
                        "created_by_id" => auth()->id(),
                    ]);
                }
                $note = new PropertyShootBookingNote();
                $note->property_shoot_booking_id = $status->id;
                $note->note = "Shoot job marked as completed.";
                $note->created_by_id = auth()->id();
                $note->save();
                //\event(new ShootJobStatusChange($status));
            }else{
                $note = new PropertyShootBookingNote();
                $note->property_shoot_booking_id = $status->id;
                $note->note = "Shoot job status updated to ".$status->status.".";
                $note->created_by_id = auth()->id();
                $note->save();
                \event(new ShootJobAction($status));
            }
            $result = array('status' => '1', 'msg' => 'Shoot request status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function photoGrapherStatusUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_shoot_bookings',
            'status' => 'required|string',
        ]);

        $job = PropertyShootBooking::find($request->id);
        $job->status = $request->status;
        $job->updated_by_id = auth()->id();
        if ($job->save()) {
            \event(new ShootJobStatusChange($job));
            if($job->property_id){
                PropertyNote::create([
                    "property_id" => $job->property_id,
                    "note" => "New ".( $job->is_luxury == 1 ? 'Luxury' : 'Standard')." property shoot has been completed.",
                    "created_by_id" => auth()->id(),
                ]);
            }
            $result = array('status' => '1', 'msg' => 'Shoot request status updated successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function approveAndAssignToUser(Request $request){
        $request->validate([
            'id' => 'required|exists:property_shoot_bookings,id',
            'user_id' => 'required|exists:users,id',
        ]);
        $photographer = User::find($request->user_id);
        $booking = PropertyShootBooking::find($request->id);
        $booking->assigned_to_id = $request->user_id;
        $booking->updated_by_id = auth()->id();
        $booking->approved_by_id = auth()->id();
        $booking->preferred_time = Carbon::parse($request->time)->format('Y-m-d H:i:s');
        $booking->status = "Approved";
        $booking->approved_at = Carbon::now();
        if ($booking->save()) {
            if($booking->pending_notification != null) {
                $pending_notification = json_decode($booking->pending_notification);
                $pending_notification->request_approved = 'Photo shoot assigned to photographer';
                $booking->pending_notification = json_encode($pending_notification);
                $booking->save();
            }else{
                $pending_notification = new \stdClass();
                $pending_notification->request_approved = 'Photo shoot assigned to photographer';
                $booking->pending_notification = json_encode($pending_notification);
                $booking->save();
            }

        $type = EventType::where('name', 'Property Shoot')->first();
            $property =   Property::find($booking->property_id)->with('community','subCommunity','communityProperty')->first();
          $location = $property?->communityProperty?->path_name ?? $property?->subCommunity?->path_name ?? $property?->community?->path_name ?? "";
            $event          = new Event();
            $event->agent_id = $request->user_id;
            $event->property_id = $booking->property_id;
            $event->start_time = $booking->preferred_time;
            $event->end_time = $booking->preferred_time;
            $event->event_type_id = $type->id;
            $event->title =  $booking->title;
            $event->location = $location;
            $event->description = $booking->description;
            $event->created_by_id = auth()->id();
            $event->save();


//            event(new ShootJobAction($booking));

            $event->attendees()->sync([$booking->requested_by_id] ?? []);

            $note = new PropertyShootBookingNote();
            $note->property_shoot_booking_id = $booking->id;
            $note->note = "Shoot job assigned to photographer ".$photographer?->full_name;
            $note->created_by_id = auth()->id();
            $note->save();
            $result = array('status' => '1', 'msg' => 'Job assigned successfully');
        } else {
            $result = array('status' => '0', 'msg' => 'Something went wrong!!');
        }
        return json_encode($result);
    }
    public function addNote(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:property_shoot_bookings,id',
            'note' => 'required|string',
        ]);

        try {

            $note = new PropertyShootBookingNote();
            $note->property_shoot_booking_id = $request->id;
            $note->note = $request->note;
            $note->created_by_id = auth()->id();
            if($note->save()){
                $result = array('status' => '1', 'msg' => "Note added successfully.");
            }else{
                $result = array('status' => '0', 'msg' => 'Something went wrong!!');
            }

            return json_encode($result);
        }catch (\Exception $exception){
            $result = array('status' => '0', 'msg' => 'Something went wrong!!','error'=>$exception->getMessage());
            return json_encode($result);
        }
    }
    public function getNotes(Request $request)
    {
        $request->validate([
            "id" => "required|exists:property_shoot_bookings,id"
        ]);
        $data = PropertyShootBookingNote::when($request->id, function ($q) {
            $q->where("property_shoot_booking_id", \request("id"));
        })->with(["createdBy"])->get();

        return response([
            "data" => $data
        ]);
    }
    public function getActivityLog(Request $request)
    {
        $params = new Request();
        $params->replace($request->all());
        $res = (new AjaxController())->activityLog($params);
        if ($res->original['data'] != null) {
            $datas = []; $raw = [];
            foreach ($res->original['data'] as $data) {
                $properties['old'] = isset($data->properties['old']) ? shootReqColumnsReadable($data?->properties['old']) : [];
                $properties['attributes'] = isset($data->properties['attributes']) ? shootReqColumnsReadable($data?->properties['attributes']) : [];
                $data->properties = $properties;
                $datas[] = $data;
                $raw[] = $data;

            }
        }
        return response()->json(['data' => $datas,'raw'=>$raw]);
    }
    public function manualNotification(Request $request){
        $request->validate([
            "id" => "required|exists:property_shoot_bookings,id",
            "type" => "required|string",
        ]);
        try{
            $job = PropertyShootBooking::where('id',$request->id)->first();
            if($request->type == 'job_completed'){
                $for = 'Job completed';
                event(new ShootJobStatusChange($job));
                $pending_notification = json_decode($job->pending_notification);
                unset($pending_notification->job_completed);

                $job->pending_notification = json_encode($pending_notification);
                $job->save();

            }elseif ($request->type == 'request_approved'){
                event(new ShootJobAction($job));
                $for = 'Shoot request approved';
                $pending_notification = json_decode($job->pending_notification);
                unset($pending_notification->request_approved);
                $job->pending_notification = json_encode($pending_notification);
                $job->save();
            }

        $note = new PropertyShootBookingNote();
        $note->property_shoot_booking_id = $job->id;
        $note->note = "Manual notification sent for ".$for;
        $note->created_by_id = auth()->id();
        $note->save();
        return response()->json(['status' => '1', 'msg' => 'Notification sent successfully']);
        }catch (\Exception $exception){
            $result = array('status' => '0', 'msg' => 'Something went wrong!!','error'=>$exception->getMessage(),'line'=>$exception->getLine());
            return json_encode($result);
        }

    }
}
