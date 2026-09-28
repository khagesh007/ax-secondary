<?php

namespace App\Http\Controllers\Admin\Offplan;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateOffplanPropertyJob;
use App\Jobs\PublishOffplanImportedProperty;
use App\Jobs\UpdateReraOfImportedProperty;
use App\Models\Developer;
use App\Models\DeveloperProject;
use App\Models\OffplanMeta;
use App\Models\OffplanProperty;
use App\Traits\FileTrait;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeveloperMetaController extends Controller
{
    use FileTrait;

    public function index()
    {
        $metas = DeveloperProject::all();
        return view('admin.offplan.index')
            ->with('metas', $metas);
    }
    public function create()
    {

        $developers = Developer::all();
        return view('admin.offplan.create')
            ->with('developers', $developers);
    }

    public function edit($id)
    {
        $developers = Developer::all();
        $developer = DeveloperProject::where('id', $id)->first();

        $projectProperty = OffplanProperty::where('developer_id', $developer->developer_id)->where('project_name', $developer->project_name)->whereNotNull('property_id')->count();

        return view('admin.offplan.edit')
            ->with('developer', $developer)
            ->with('developers', $developers)
            ->with('projectProperty', $projectProperty);
    }

    public function update(Request $request, $id)
    {

        $developer = DeveloperProject::where('id', $id)->first();
        if ($request->developer_id) {
            $developer->developer_id = $request->developer_id;
        }
        if ($request->project_name) {
            $developer->project_name = $request->project_name;
        }
        if ($request->rera_permit) {
            $developer->rera_permit = $request->rera_permit;
        }
        if ($request->description) {
            $developer->description = $request->description;
        }

        if ($request->amenities) {
            $developer->amenities = $request->amenities;
        }

        if ($request->payment_plans) {
            $developer->payment_plans = $request->payment_plans;
        }

        if ($request->additional_description) {
            $developer->additional_description = $request->additional_description;
        }

        if ($request->signature) {
            $developer->signature = $request->signature;
        }

        $developer->save();
        if ($request->title) {

            $developer = new OffplanMeta();

            $developer->developer_project_id = $id;
            $developer->key = 'title';
            $developer->value = $request->title;
            $developer->save();
        }

        if ($request->titles) {
            foreach ($request->titles as $key => $title) {
                $developer = OffplanMeta::find($request->ids[$key]);
                if (!$developer) {
                    $developer = new OffplanMeta();
                }
                $developer->developer_project_id = $id;
                $developer->key = 'title';
                $developer->value = $title;
                $developer->save();
            }
        }

        return redirect()->back();

    }

    public function store(Request $request)
    {

        $request->validate([
            'developer_id' => 'required',
            'project_name' => 'required',
        ]);

        $project = DeveloperProject::create([
            'slug' => Str::slug($request->project_name),
            'developer_id' => $request->developer_id,
            'project_name' => $request->project_name,
        ]);

        return redirect()->route('admin.developer.meta.edit', $project->id);

    }

    public function storeGallery(Request $request, $id)
    {
        $watermark = 'assets/images/logo/watermarkv3.png';
        if ($request->images) {
            foreach ($request->images as $key => $image) {
                $image = $this->uploadImageAndThumbWithWatermark(file: $image, path: "listings", watermark: $watermark);
                $images = [
                    'image' => $image["image"],
                    'original' => $image["original"] ?? null,
                    'thumbnail' => $image["thumbnail"] ?? null,
                ];
                $developer = new OffplanMeta();
                $developer->developer_project_id = $id;
                $developer->key = 'Photos';
                $developer->value = json_encode($images);
                $developer->save();
            }
        }
        return redirect()->back();
    }

    public function getData()
    {
        $property =
            [
            'developer_name' => 'developer_name',
            'project_name' => 'project_name', // Bayview
            'property_id' => 'property_id',

            'title' => 'title',
            'description' => 'description',

            'unit_no' => 'unit_no',
            'unit_position' => 'unit_position',

            'price' => 'price',
            'bed' => 'bed',
            'bath' => 'bath',

            'total_area' => 'total_area',
            'plot_size' => 'plot_size',

            'property_type' => 'property_type', // apartment, villa
            'property_usage' => 'property_usage', // residential,commersial

            'booking_status' => 'booking_status',

            'completion_status' => 'completion_status',
            'view' => 'view',
            'parking' => 'parking',
            'location_code' => [
                "building" => "Greenside Residence",
                "location" => "DE Greenside Building A",
                "property" => "Dubai Hills Estates",
            ],
            'payment_plans' => [
            ],
            'floor_plans' => [
            ],
            'photos' => [
            ],
            'creation_date' => 'creation_date',
            'miscellaneous' => [
                'key1' => [
                    //data
                ],
                'key2' => [
                    //data
                ],
                'key3' => [
                    //data
                ],
            ],

        ];
    }

    public function __store(Request $request)
    {
        try {

            DB::beginTransaction();
            if ($request->titles) {
                foreach ($request->titles as $key => $title) {
                    $developer = new OffplanMeta();
                    $developer->developer_name = $request->developer ?? 'emaar';
                    $developer->project_name = $request->project_name ?? 'emaar';
                    $developer->key = 'title';
                    $developer->value = $title;
                    $developer->save();
                }
            }
            $watermark = 'assets/images/logo/watermarkv3.png';
            if ($request->images) {
                foreach ($request->images as $key => $image) {
                    $image = $this->uploadImageAndThumbWithWatermark(file: $image, path: "listings", watermark: $watermark);
                    $images = [
                        'image' => $image["image"],
                        'original' => $image["original"] ?? null,
                        'thumbnail' => $image["thumbnail"] ?? null,
                    ];
                    $developer = new OffplanMeta();
                    $developer->developer_name = $request->developer ?? 'emaar';
                    $developer->project_name = $request->project_name ?? 'emaar';
                    $developer->key = 'Photos';
                    $developer->value = json_encode($images);
                    $developer->save();
                }
            }
            DB::commit();
            return response()->json(["status" => 1, "msg" => "Developer saved successfully"]);

        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
            return response()->json(["status" => 0, "msg" => "Developer not saved successfully"]);
            //throw $th;
        }
    }

    public function updateExistingProperty(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'id' => 'required',
        ]);
        $developer = DeveloperProject::where('id', $request->id)->first();
        $type = $request->type;
        $project_id = $request->id;
        UpdateOffplanPropertyJob::dispatch($type, $project_id);


        return redirect()->back();

    }

    public function publishOffplanImportedProperty(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        $developer = DeveloperProject::where('id', $request->id)->first();
        $project_id = $request->id;
        PublishOffplanImportedProperty::dispatch($project_id, auth()->user()->id);
        return redirect()->back();

    }

    public function updateReraNumber(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        $developer = DeveloperProject::where('id', $request->id)->first();
        $project_id = $request->id;
        updateReraOfImportedProperty::dispatch($project_id, auth()->user()->id);
        return redirect()->back();

    }


    public function delete(Request $request){
        $meta = OffplanMeta::find($request->id);
        $meta->delete();
        return response()->json(["status" => 1, "msg" => "Data Deleted"]);

    }
}
