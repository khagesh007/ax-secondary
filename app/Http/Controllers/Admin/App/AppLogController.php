<?php

namespace App\Http\Controllers\Admin\App;

use App\Events\ChangesRequested;
use App\Events\ListingNoteCreated;
use App\Events\PocketRequest;
use App\Events\PublishRequested;
use App\Events\UnpublishRequested;
use App\Exports\PropertyExport;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\Controller;
use App\Mail\Admin\PropertyUnpublishRequestMail;
use App\Models\AppLog;
use App\Models\Bath;
use App\Models\Bed;
use App\Models\Community;
use App\Models\CommunityProperty;
use App\Models\Contact;
use App\Models\ContactAgent;
use App\Models\ContactSource;
use App\Models\Country;
use App\Models\Developer;
use App\Models\Event;
use App\Models\Portal;
use App\Models\Property;
use App\Models\PropertyAmenity;
use App\Models\PropertyCategory;
use App\Models\PropertyFeatures;
use App\Models\PropertyMedia;
use App\Models\PropertyNote;
use App\Models\PropertyPortal;
use App\Models\PropertyPrice;
use App\Models\PropertyStatus;
use App\Models\SubCommunity;
use App\Models\User;
use App\Traits\FileTrait;
use App\Traits\FiltersTrait;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\TelegramTrait;

class AppLogController extends Controller
{
    use FiltersTrait;

    public function index()
    {

        // abort_if(Gate::denies("Properties List"), 401);

        return view('admin.app.log');
    }


    public function dataTable(Request $request)
    {

        $columns = array(
            0 => 'created_at',
            1 => 'id',
            2 => 'title',
            3 => 'type',
        );
        $q = AppLog::query();
        $totalData = $q->count(); // total data count
        $totalFiltered = $q->count();

        $data = AppLog::with('user')->offset($request['start'])
            ->limit($request['length'])
            ->orderBy($columns[$request["order"][0]["column"]],
                $request["order"][0]["dir"])
            ->get();

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => $totalFiltered,
            "recordsFiltered" => $totalFiltered,
            "data" => $data,
        );
        echo json_encode($json_data);
    }

    public function get(Request $request)
    {
        $data = AppLog::with('user')->where('id', $request->id)->first();
        if (!empty($data)) {
            return response()->json(['status' => 1, 'message' => 'Fetched successfully', 'data' => $data]);
        } else {
            return response()->json(['status' => 0, 'message' => 'No data found']);
        }
    }
}
