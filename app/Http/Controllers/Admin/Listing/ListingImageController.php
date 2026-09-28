<?php

namespace App\Http\Controllers\Admin\Listing;


use App\Models\Property;
use Illuminate\Support\Facades\Response;


use Cjmellor\Approval\Enums\ApprovalStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Auth;

class ListingImageController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $keyword = $request->get('keyword');
        $perPage = $request->get('length', 12);
        $publish_status = $request->get('publish_status');
        $data['listings'] = Property::when($keyword, function ($query, $keyword) {
            return $query->where('title', 'like', "%$keyword%");
        })->when($publish_status, function ($query, $publish_status) {
            return $query->where('publish_status', $publish_status);
        })->orderBy('id', 'desc')->paginate($perPage);

        return view('admin.listing.listingImage')->with($data);
    }
    public function data(){
        $ref_numbers = [
            "axc-3978231",
            "axc-3978033",
            "axc-3977906",
            "axc-3977812",
            "axc-3977778",
            "axc-3977511",
            "axc-3977153",
            "axc-3977013",
            "axc-3976949",
            "axc-3976833",
            "axc-3976829",
            "axc-3976736",
            "axc-3976682",
            "axc-3976596",
            "axc-3976427",
            "axc-3976390",
            "axc-3976339",
            "axc-3976211",
            "axc-3976186",
            "axc-3976160",
            "axc-3976050",
            "axc-3976049",
            "axc-3976048",
            "axc-3976047",
            "axc-3976046",
            "axc-3976045",
            "axc-3976043",
            "axc-3976042",
            "axc-3976041",
            "axc-3976040",
            "axc-3976038",
            "axc-3976024",
            "axc-3976017",
            "axc-3975833",
            "axc-3975563",
            "axc-3975388",
            "axc-3975246",
            "axc-3975066",
            "axc-3974766",
            "axc-3974467",
            "axc-3974271",
            "axc-3974257",
            "axc-3974035",
            "axc-3973774",
            "axc-3973562",
            "axc-3973297",
            "axc-3971511",
            "axc-3965318",
            "axc-3679222",
            "axc-3678910"
        ];

        $data['listings'] = Property::whereIn('ref_no',$ref_numbers)->get();
       return view('admin.listing.tempFile')->with($data);
    }
}
