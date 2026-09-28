<?php

namespace App\Http\Controllers\Api\V1\Folders;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Traits\FileTrait;
use App\Traits\FiltersTrait;
use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\FolderMedia;
use App\Models\Lead;
use App\Models\Property;
use App\Models\Tag;
use App\Traits\PDFTrait;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FolderApiController extends Controller
{
    use FiltersTrait, FileTrait, PDFTrait;

    public function getFolder(Request $request)
    {
        $request->validate([
            'slug' => 'required|exists:folders,slug',
        ]);

        $folder = Folder::where("slug", $request->slug)->first();
        if (!empty($folder)) {
            return response()->json(['status' => '1', 'msg' => 'Folder Found', 'data' => $folder, 'breadcrumbs' => array_reverse(getParentFoldersByID($folder->id))]);
        } else {
            return response()->json(['status' => '0', 'msg' => 'Folder Not Found']);
        }
    }

    public function createFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'folder_order' => 'required|numeric',
            'lead_id' => 'nullable|exists:leads,id',
            'deal_id' => 'nullable|exists:deals,id',
            'property_id' => 'nullable|exists:properties,id',
            'thumbnail' => 'nullable|mimes:jpg,png,jpeg',
            'parent_id' => 'nullable|exists:folders,id',
            'tags' => 'nullable|string',
        ]);

        try {

            $folder = new Folder();

            $slug = generateFolderSlug($request->name);

            $folder->name = $request->name;
            $folder->slug = $slug;
            $folder->folder_order = $request->folder_order;
            $folder->parent_id = $request->parent_id;

            if (!empty($request->lead_id)) {
                $folder->model_type = Lead::class;
                $folder->model_id = $request->lead_id;
            }

            if (!empty($request->deal_id)) {
                $folder->model_type = Deal::class;
                $folder->model_id = $request->deal_id;
            }

            if (!empty($request->property_id)) {
                $folder->model_type = Property::class;
                $folder->model_id = $request->property_id;
            }

            $folder->created_by_id = auth()->id();

            if ($request->hasFile("thumbnail")) {
                $images = $this->uploadThumb($request->file("thumbnail"), "file-manager/thumbnail");
                $folder->thumbnail = $images["thumbnail"] ?? null;
            }


            if ($folder->save()) {
                $tagIds = [];
                foreach (explode(",", $request->tags ?? "") as $tag) {
                    $tag = Tag::updateOrCreate([
                        "name" => $tag
                    ], [
                        "slug" => Str::slug($tag)
                    ]);
                    $tagIds[] = $tag->id;
                }
                $folder->tags()->sync($tagIds);
                $folder->load(["tags"]);
                return response()->json(['status' => '1', 'msg' => 'Folder Created Successfully', 'data' => $folder]);
            } else {
                return response()->json(['status' => '0', 'msg' => 'Folder Not Created']);
            }
        } catch (Exception $exception) {
            return response()->json(['status' => '0', 'msg' => 'An error occurred', 'error' => $exception->getMessage()]);
        }
    }

    public function updateFolder(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:folders',
            'name' => 'required|string',
            'folder_order' => 'required|numeric',
            'lead_id' => 'nullable|exists:leads,id',
            'property_id' => 'nullable|exists:properties,id',
            'thumbnail' => 'nullable|mimes:jpg,png,jpeg',
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        try {
            $folder = Folder::find($request->id);

            $folder->name = $request->name;
            $folder->folder_order = $request->folder_order;
            $folder->parent_id = $request->parent_id;

            if (!empty($request->lead_id)) {
                $folder->model_type = Lead::class;
                $folder->model_id = $request->lead_id;
            }

            if (!empty($request->deal_id)) {
                $folder->model_type = Deal::class;
                $folder->model_id = $request->deal_id;
            }

            if (!empty($request->property_id)) {
                $folder->model_type = Property::class;
                $folder->model_id = $request->property_id;
            }
            $folder->updated_by_id = auth()->id();

            if ($request->hasFile("thumbnail")) {
                $images = $this->uploadThumb($request->file("thumbnail"), "file-manager/thumbnail");
                $folder->thumbnail = $images["thumbnail"] ?? null;
            }


            if ($folder->save()) {
                $tagIds = [];
                foreach (explode(",", $request->tags ?? "") as $tag) {
                    $tag = Tag::updateOrCreate([
                        "name" => $tag
                    ], [
                        "slug" => Str::slug($tag)
                    ]);
                    $tagIds[] = $tag->id;
                }
                $folder->tags()->sync($tagIds);
                $folder->load(["tags"]);
                return response()->json(['status' => '1', 'msg' => 'Folder Updated Successfully', 'data' => $folder]);
            } else {
                return response()->json(['status' => '0', 'msg' => 'Folder Not Udpdated']);
            }
        } catch (Exception $exception) {
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }

    public function getFolders(Request $request)
    {
        try {
            $folders = Folder::orderBy("folder_order")->get();
            return response()->json(['status' => '1', 'msg' => 'Folders Found', 'data' => $folders]);
        } catch (Exception $e) {
            // Handle any exceptions here
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }

    public function deleteFolder(Folder $folder)
    {
        try {

            $parentFolders = getParentFoldersByID($folder->id);

            $dir = "file-manager/";
            foreach ($parentFolders as $parentFolder) {
                $folderMedia = FolderMedia::where("folder_id", $parentFolder["id"])->get();
                foreach ($folderMedia as $media) {
                    //this folder logic
                }
            }
            return response()->json(['status' => '1', 'msg' => 'Folder Deleted Successfully', 'data' => []]);
        } catch (Exception $e) {
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }

    public function getFoldersList(Request $request)
    {
        try {
            $folders = Folder::when(
                !empty($request->id) && empty($request->term),
                fn($q) => $q->where("parent_id", $request->id),
                fn($q) => !empty($request->id) ? $q->whereIn("parent_id", getFolderChildrenIdsByID($request->id)) : null
            )
                ->when($request->boolean("root"), fn($q) => $q->whereNull("parent_id"))
                ->when(!empty($request->term), function ($q) {
                    $q->where(function ($q1) {
                        $q1->where("name", "LIKE", "%" . request("term") . "%");
                        $q1->orWhere("model_type", "LIKE", "%" . request("term") . "%");
                    });
                })
                ->withCount("folderMedia")
                ->with(["tags:id,name"])
                // ->orderBy("folder_order")
                ->latest()
                ->paginate(6);
            return response()->json(['status' => '1', 'msg' => 'Folders Found', 'data' => $folders]);
        } catch (Exception $e) {
            // Handle any exceptions here
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }

    public function uploadFiles(Request $request)
    {
        $request->validate([
            'folder_id' => 'required|exists:folders,id',
            'files' => 'required|array',
        ]);

        try {
            $folder = Folder::find($request->folder_id);

            $folderLocation = "file-manager/" . rtrim($folder->slug, "/");

            foreach ($request->file("files") ?? [] as $file) {
                $extension = $file->extension();
                $path = $this->uploadFileToFileManagerFolder($file, $folderLocation);
                $file_name = $file->getClientOriginalName();
                //get file name without extension
                $file_name = pathinfo($file_name, PATHINFO_FILENAME);
                $slug = Str::slug($file_name);

                if (!is_null($path)) {
                    FolderMedia::create([
                        "folder_id" => $folder->id,
                        "type" => getFolderMediaTypeByExtension($extension),
                        "title" => basename($path["path"]),
                        "slug" => $slug,
                        "path" => $path["path"],
                        "disk" => $path["disk"],
                        "extension" => $extension,
                        "size" => $path["size"] >> 10, //Size in KB
                        "created_by_id" => auth()->id(),
                    ]);
                }
            }
            return response()->json(['status' => '1', 'msg' => 'Files Uploaded Successfully', 'data' => []]);
        } catch (Exception $e) {
            return response()->json(['status' => '0', 'msg' => 'Files Not Uploaded', 'error' => $e->getMessage()]);
        }
    }

    public function getFilesList(Request $request)
    {
        try {
            $files = FolderMedia::when(!empty($request->folder_id), fn($q) => $q->where("folder_id", $request->folder_id))
                // ->orderBy("folder_order")
                ->latest()
                ->paginate(5);
            return response()->json(['status' => '1', 'msg' => 'Files Found', 'data' => $files]);
        } catch (Exception $e) {
            // Handle any exceptions here
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }

    public function deleteFile(FolderMedia $folderMedia)
    {

        try {

            $deleted = $this->removeFile($folderMedia->path);
            if ($deleted) {
                $folderMedia->slug = $folderMedia->slug. rand(1,99999) . "-deleted";
                $folderMedia->save();
                $folderMedia->delete();
                return response()->json(['status' => '1', 'msg' => 'File Deleted Successfully', 'data' => []]);
            }
            return response()->json(['status' => '0', 'msg' => 'File not deleted']);

        } catch (Exception $e) {
            // Handle any exceptions here
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }

    public function downloadFolderZip(Request $request)
    {
        try {
            $folder = Folder::find($request->id);
            if (is_null($folder)) {
                return response()->json(['status' => '0', 'msg' => 'Folder not found']);
            }

            $folderMedia = FolderMedia::where("folder_id", $folder->id)->get();

            $fileUrls = [];

            foreach ($folderMedia as $media) {
                $fileUrls[] = $media->path;
            }

            if (count($fileUrls) > 0) {
                $fileName = $folder->slug . ".zip";
                $filePath = $this->makeZipFromUrls($fileUrls, $folder->slug, $fileName);

                if (!is_null($filePath)) {
                    FolderMedia::create([
                        "folder_id" => $folder["id"],
                        "type" => getFolderMediaTypeByExtension("zip"),
                        "title" => "Zip File requested by " . auth()->user()->name,
                        "path" => $filePath["path"],
                        "disk" => $filePath["disk"],
                        "extension" => "zip",
                        "size" => $filePath["size"] >> 10, //Size in KB
                        "created_by_id" => auth()->id(),
                    ]);
                    return response()->json(['status' => '1', 'msg' => 'Zip file created successfully.', 'downloadUrl' => $filePath["path"]]);
                }
                return response()->json(['status' => '0', 'msg' => 'Unable to create zip.']);
            }

            return response()->json(['status' => '0', 'msg' => 'This folder has no files.']);
        } catch (Exception $exception) {
            return response()->json(['status' => '0', 'msg' => 'An error occurred']);
        }
    }

    public function getFile(Request $request)
    {
        $folderMedia = null;
        if (!empty($request->id)) {
            $folderMedia = FolderMedia::find($request->id);
        } else if (!empty($request->slug)) {
            $folderMedia = FolderMedia::where("slug", $request->slug)->first();
        }

        if (!is_null($folderMedia)) {
            return response()->json(['status' => '1', 'msg' => 'File Found', 'data' => $folderMedia]);
        }

        return response()->json(['status' => '0', 'msg' => 'File Not Found']);
    }

    public function filesListDatatable(Request $request)
    {

        $columns = array(
            0 => 'title',
            1 => 'type',
            2 => null,
            3 => 'size',
            4 => null,
            5 => 'updated_at',
            6 => null,
        );

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Offplan Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $q = FolderMedia::query();

        $totalFiltered = $this->applyFolderMediaFilters($q, $request)->when(!empty($request->folder_id), fn($q) => $q->where("folder_id", $request->folder_id))->count();

        $data = $this->applyFolderMediaFilters($q, $request)->when(!empty($request->folder_id), fn($q) => $q->where("folder_id", $request->folder_id))
            ->offset($request['start'])
            ->limit($request['length'])
            ->orderBy($columns[$request["order"][0]["column"]], $request["order"][0]["dir"])
            ->with(["createdBy", "updatedBy", "folder:id,model_type"])
            ->get();

        $json_data = array(
            "draw" => intval($request['draw']),
            "recordsTotal" => $totalFiltered,
            "recordsFiltered" => $totalFiltered,
            "data" => $data,
        );
        echo json_encode($json_data);
    }

    public function getMyFilesList(Request $request) :  \Illuminate\Http\JsonResponse
    {
        $keyword = $request->input('keyword');

        $notAdmin = auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent", "Offplan Agent"]);
        $childrenIds = [];
        if ($notAdmin) {
            $childrenIds = getUserChildrenIds(auth()->id());
        }

        $q = FolderMedia::query();
        if(!empty($keyword)){
            $q->where('title', 'like', "%$keyword%");
        }
        $totalFiltered = $this->applyFolderMediaFilters($q, $request)->when(!empty($request->folder_id), fn($q) => $q->where("folder_id", $request->folder_id))->count();
        $data = $this->applyFolderMediaFilters($q, $request)->when(!empty($request->folder_id), fn($q) => $q->where("folder_id", $request->folder_id))
            ->offset($request['start'])
            ->orderBy('id', 'desc')
            ->with(["createdBy", "updatedBy", "folder:id,model_type"])
            ->paginate($request['length']);

//        $file = File::when($keyword, function ($query, $keyword) {
//            return $query->where('title', 'like', "%$keyword%");
//        })->orderBy('id', 'desc')->paginate(12);
        if(!empty($data)){
            $result = ['status'=>'success','data'=>$data];
            return response()->json($result);
        }else{
            $result = ['status'=>'failed','message'=>'No data found'];
            return response()->json($result);
        }
    }


    public function getProperty(Request $request)
    {

        $property = null;
        if (!empty($request->id)) {
            $property = Property::find($request->id);
        } else if (!empty($request->ref_no)) {
            $property = Property::where("ref_no", $request->ref_no)->first();
        }

        if (!is_null($property)) {
            $property->load([
                "owner:id,first_name,last_name,passport,passport_expiry",
                "propertyStatus:id,title",
                "community:id,name",
                "bed:id,name",
                "bath:id,name",
                "price:id,price",
                "agent:id,name",
            ]);
            return response()->json(['status' => '1', 'msg' => 'Property Found', 'data' => $property]);
        }

        return response()->json(['status' => '0', 'msg' => 'Property Not Found']);
    }

    public function generatePDF(Request $request)
    {
        $request->validate([
            "type" => "required|string"
        ]);

        if ($request->type == "a2a-agreement-form") {
            return $this->generateA2AAgreementFormPDF($request);
        }

        if ($request->type == "contract-between-buyer-and-broker-form-b") {
            return $this->generateContactBetweenBuyerAndBrokerFormB($request);
        }

        if ($request->type == "tenancy-form-dubai") {
            return $this->generateTenancyFormDubai($request);
        }

        if ($request->type == "marketing-form-a-leasing-fillable") {
            return $this->generateMarketingFormALeasingFillable($request);
        }
        if ($request->type == "lead-kyc-form-entity") {
            return $this->generateKycEntityFillable($request);
        }
        if ($request->type == "lead-kyc-form-individual") {
            return $this->generateKycIndividualFillable($request);
        }
        if ($request->type == "form-a-contract-seller-broker") {
            return $this->generateFormAContractSellerBrokerFillable($request);
        }
        if ($request->type == "key-handover-form") {
            return $this->generateKeyHandoverFormFillable($request);
        }
        if ($request->type == "Leasing-Letter-of-Intent-Payables") {
            return $this->generateLeasingLetterIntentPayableFillable($request);
        }
        if ($request->type == "Sales-Letter-Of-Intent-Purchase") {
            return $this->generateLeasingLetterIntentPurchaseFillable($request);
        }
        if ($request->type == "Leasing-Reservation-Form") {
            return $this->generateLeasingReservationFormFillable($request);
        }
        if ($request->type == "Sales-Reservation-Form") {
            return $this->generateSalesReservationFormFillable($request);
        }
        if ($request->type == "KYC-Compliance-Declaration-Form") {
            return $this->generateKycComplianceDFormFillable($request);
        }

        return response()->json(['status' => '0', 'msg' => 'Please select a type']);
    }

    public function generateA2AAgreementFormPDF(Request $request)
    {
        $request->validate([
            "folder_id" => "nullable|exists:folders,id"
        ]);

        try {
            $property = Property::find(1);

            $folder = createPropertyFolder($property);

            if ($folder !== false) {
                $name = "Agent to agent agreentment generated by " . auth()->user()->name . " on " . $request->filename;
                $slug = Str::slug($name);
                $path = $this->fillAndGenerateA2AAgreementForm($request->all(), $slug, $folder["folder_path"]);
                if (is_null($path)) {
                    return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
                }
                $fileName = $folder["folder_path"] . $slug . ".pdf";
                FolderMedia::updateOrCreate([
                    "folder_id" => $folder["id"],
                    "title" => $name . ".pdf",
                ], [
                    "type" => getFolderMediaTypeByExtension("pdf"),
                    "path" => $fileName,
                    "slug" => $slug.Str::random(10),
                    "extension" => "pdf",
                    "size" => 770, //Size in KB
                    "created_by_id" => auth()->id(),
                ]);
                return response()->json(['status' => '1', 'msg' => 'PDF generated successfully.', 'url' => asset("storage/" . $fileName)]);
            }
            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
        } catch (Exception $e) {
            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
        }
    }

    public function generateContactBetweenBuyerAndBrokerFormB(Request $request)
    {
        $request->validate([
            "folder_id" => "nullable|exists:folders,id"
        ]);

        try {
            $property = Property::find(1);

            $folder = createPropertyFolder($property);

            if ($folder !== false) {
                $name = "Form B generated by " . auth()->user()->name . " on " . $request->filename;
                $slug = Str::slug($name);
                $path = $this->fillAndGenerateContactBetweenBuyerAndBrokerFormB($request->all(), $slug, $folder["folder_path"]);
                if (is_null($path)) {
                    return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
                }
                $fileName = $folder["folder_path"] . $slug . ".pdf";
                FolderMedia::updateOrCreate([
                    "folder_id" => $folder["id"],
                    "title" => $name . ".pdf",
                ], [
                    "type" => getFolderMediaTypeByExtension("pdf"),
                    "path" => $fileName,
                    "slug" => $slug.Str::random(10),
                    "extension" => "pdf",
                    "size" => null, //Size in KB
                    "created_by_id" => auth()->id(),
                ]);
                return response()->json(['status' => '1', 'msg' => 'PDF generated successfully.', 'url' => asset("storage/" . $fileName)]);
            }
            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
        } catch (Exception $e) {
            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
        }
    }

    public function generateTenancyFormDubai(Request $request)
    {
        $validatedData = $request->validate([
            "file_id" => "nullable|exists:folder_media,id",
            "filename" => "required|string",
            "type" => "required|string",
            "deal_id" => "nullable|exists:deals,id",
            "property_usage" => "nullable|string",
            "owner_name" => "nullable|string",
            "landlord_name" => "nullable|string",
            "tenant_name" => "nullable|string",
            "tenant_email" => "nullable|string",
            "landlord_email" => "nullable|string",
            "tenant_phone" => "nullable|string",
            "landlord_phone" => "nullable|string",
            "building_name" => "nullable|string",
            "location" => "nullable|string",
            "property_size" => "nullable|string",
            "property_type" => "nullable|string",
            "property_no" => "nullable|string",
            "premises_no" => "nullable|string",
            "plot_no" => "nullable|string",
            "contract_period_to" => "nullable|string",
            "contract_period_from" => "nullable|string",
            "annual_rent" => "nullable|string",
            "contract_value" => "nullable|string",
            "security_deposit_amount" => "nullable|string",
            "mode_of_payment" => "nullable|string",
        ]);

        try {

            $deal = Deal::find($request->deal_id);

            $folderMedia = FolderMedia::find($request->file_id);
            if (!is_null($folderMedia)) {
                $fileSlug = basename($folderMedia->path);
                $folderPath = str_replace($fileSlug, "", $folderMedia->path);
                $fileSlug = str_replace(".pdf", "", $fileSlug);
                $path = $this->fillAndGenerateTenancyFormDubai($validatedData, $fileSlug, $folderPath);
                if (is_null($path)) {
                    return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
                }
                $folderMedia->update([
                    "updated_by_id" => auth()->id(),
                    "data" => $validatedData,
                    "data_editable" => true
                ]);
                return response()->json(['status' => '1', 'msg' => 'PDF generated successfully.', 'url' => asset("storage/" . $folderMedia->path)]);
            }

            $folder = createDealFolder();

            if ($folder !== false) {
                // $name = "Tenancy form dubai generated by " . auth()->user()->name . " on " . $request->filename;
                $name = "Tenancy form dubai generated by on " . $request->filename;
                $slug = Str::slug($name);
                $path = $this->fillAndGenerateTenancyFormDubai($validatedData, $slug, $folder["folder_path"]);
                if (is_null($path)) {
                    return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
                }
                $fileName = $folder["folder_path"] . $slug . ".pdf";
                FolderMedia::updateOrCreate([
                    "folder_id" => $folder["id"],
                    "title" => $name . ".pdf",
                ], [
                    "type" => getFolderMediaTypeByExtension("pdf"),
                    "path" => $fileName,
                    "extension" => "pdf",
                    "size" => 770, //Size in KB
                    //  "created_by_id" => auth()->id(),
                    "created_by_id" => null,
                    "data" => $validatedData,
                    "data_editable" => true
                ]);
                return response()->json(['status' => '1', 'msg' => 'PDF generated successfully.', 'url' => asset("storage/" . $fileName)]);
            }
            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
        } catch (Exception $e) {
            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.', 'error' => $e->getMessage()]);
        }
    }


    public function generateMarketingFormALeasingFillable(Request $request)
    {
        $validatedData = $request->validate([
            "file_id" => "nullable|exists:folder_media,id",
            "filename" => "required|string",
            "type" => "required|string",
            "property_id" => "required|exists:properties,id",
            "property_for" => "nullable|string",
            "lsting_consultant" => "nullable|string",
            "property_reference_no" => "nullable|string",
            "landlord_name" => "nullable|string",
            "passport" => "nullable|string",
            "emirates_id_no" => "nullable|string",
            "passport_expiry" => "nullable|string",
            "category" => "nullable|string",
            "property_furnished" => "nullable|string",
            "property_status" => "nullable|string",
            "availability_date" => "nullable|string",
            "building_name" => "nullable|string",
            "unit_no" => "nullable|string",
            "street" => "nullable|string",
            "community" => "nullable|string",
            "build_up_area" => "nullable|string",
            "plot_size" => "nullable|string",
            "bedrooms" => "nullable|string",
            "bathrooms" => "nullable|string",
            "parking" => "nullable|string",
            "rental_amount" => "nullable|string",
            "commission" => "nullable|string",
            "landlord_agreed_to_appoint_as" => "nullable|string",
            "advertise_period_of_month" => "nullable|string",
            "advertise_from" => "nullable|string",
            "advertise_to" => "nullable|string",
        ]);

        try {
            $property = Property::find($request->property_id);

            $folderMedia = FolderMedia::find($request->file_id);
            if (!is_null($folderMedia)) {
                $folder = Folder::find($folderMedia->folder_id);
                $path = $this->fillAndGenerateMarketingFormALeasingFillable($validatedData, $folderMedia->slug, $folder->path);

                if (is_null($path)) {
                    return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
                }

                $folderMedia->update([
                    "path" => $path["path"],

                    "extension" => "pdf",
                    "size" => $path["size"] >> 10, //Size in KB
                    "updated_by_id" => auth()->id(),
                    "data" => $validatedData,
                    "data_editable" => true
                ]);
                return response()->json(['status' => '1', 'msg' => 'PDF generated successfully.', 'url' => $path["path"]]);
            }

            $folder = createPropertyFolder($property);

            if ($folder !== false) {
                $name = "Marketing Form A - Leasing Fillable generated by " . auth()->user()->name . " on " . $request->filename;
                $slug = Str::slug($name);

                $path = $this->fillAndGenerateMarketingFormALeasingFillable($validatedData, $slug, $folder["folder_path"]);

                if (is_null($path)) {
                    return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
                }
                $validatedData["filename"] = $slug . ".pdf";
                FolderMedia::updateOrCreate([
                    "folder_id" => $folder["id"],
                    "title" => $name . ".pdf",
                ], [
                    "type" => getFolderMediaTypeByExtension("pdf"),
                    "slug" => $slug,
                    "path" => $path["path"],
                    "extension" => "pdf",
                    "size" => $path["size"] >> 10, //Size in KB
                    "created_by_id" => auth()->id(),
                    "data" => $validatedData,
                    "data_editable" => true
                ]);
                return response()->json(['status' => '1', 'msg' => 'PDF generated successfully.', 'url' => $path["path"]]);
            }
            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
        } catch (Exception $e) {
            return response()->json(['status' => '0', 'msg' => $e->getMessage()]);
            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
        }
    }

    public function generateKycEntityFillable(Request $request)
    {
        $validatedData = $request->validate([
            "file_id" => "nullable|exists:folder_media,id",
            "filename" => "required|string",
            "type" => "required|string",
            "lead_id" => "required|exists:leads,id",
            "full_legal_name" => "nullable|string",
            "trading_name" => "nullable|string",
            "company_trading_name" => "nullable|string",
            "company_trad_license_no" => "nullable|string",
            "coi" => "nullable|string",
            "doi" => "nullable|string",
            "address" => "nullable|string",
            "trade_license_authority" => "nullable|string",
            "phone" => "nullable|string",
            "email" => "nullable|string",
            "trade_license_issue_date" => "nullable|string",
            "trade_license_expiry_date" => "nullable|string",
            "ubo_name_1" => "nullable|string",
            "ubo_percentage_1" => "nullable|string",
            "ubo_name_2" => "nullable|string",
            "ubo_percentage_2" => "nullable|string",
            "company_person1_name" => "nullable|string",
            "company_person1_relationship" => "nullable|string",
            "company_person2_name" => "nullable|string",
            "company_person2_relationship" => "nullable|string",
            "company_product" => "nullable|string",
            "company_primary_countries" => "nullable|string",
            "company_individual_occupation" => "nullable|string",
            "name_of_employer" => "nullable|string",
            "deal_with_risky_countries" => "nullable|string",
            "politically_exposed" => "nullable|string",
            "company_person_name" => "nullable|string",
            "company_person_relationship" => "nullable|string",
            "declaration_date" => "nullable|string",

        ]);

        try {
            $folderMedia = FolderMedia::find($request->file_id);

            if (!is_null($folderMedia)) {
                $folder = Folder::find($folderMedia->folder_id);
                $path = $this->fillAndGenerateKycEntityFillable($validatedData, $folderMedia->slug, $folder->path);

                if (is_null($path)) {
                    return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
                }

                $folderMedia->update([
                    "path" => $path["path"],
                    "extension" => "pdf",
                    "size" => $path["size"] >> 10, //Size in KB
                    "updated_by_id" => auth()->id(),
                    "data" => $validatedData,
                    "data_editable" => true
                ]);
                return response()->json(['status' => '1', 'msg' => 'PDF generated successfully.', 'url' => $path["path"]]);
            }

            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
        } catch (Exception $e) {
            return response()->json(['status' => '0','msg' => 'Unable to generate file.', 'error' => $e->getMessage()]);
        }
    }

    public function generateKycIndividualFillable(Request $request)
    {

        $validatedData = $request->validate([
            "file_id" => "nullable|exists:folder_media,id",
            "filename" => "required|string",
            "type" => "required|string",
            "lead_id" => "nullable|exists:leads,id",
            "full_name" => "nullable|string",
            "aliases" => "nullable|string",
            "current_residential_address" => "nullable|string",
            "email" => "nullable|string",
            "telephone_number" => "nullable|string",
            "mobile_number" => "nullable|string",
            "dob" => "nullable|string",
            "pob" => "nullable|string",
            "gender" => "nullable|string",
            "nationality" => "nullable|string",
            "passport_number" => "nullable|string",
            "passport_issue_country" => "nullable|string",
            "passport_issue_date" => "nullable|string",
            "passport_expiry_date" => "nullable|string",
            "fund_employment_income" => "nullable|string",
            "fund_sale_of_investment" => "nullable|string",
            "fund_business_income" => "nullable|string",
            "fund_savings" => "nullable|string",
            "fund_other" => "nullable|string",
            "fund_other_label" => "nullable|string",
            "residency_visa_number" => "nullable|string",
            "emirates_id_no" => "nullable|string",
            "visa_expiry_date" => "nullable|string",
            "occupation" => "nullable|string",
            "years_in_role" => "nullable|string",
            "employer_name" => "nullable|string",
            "employer_address" => "nullable|string",
            "wealth_employment_income" => "nullable|string",
            "wealth_sale_of_inv_property" => "nullable|string",
            "wealth_business_income" => "nullable|string",
            "wealth_savings" => "nullable|string",
            "wealth_inheritance" => "nullable|string",
            "wealth_other" => "nullable|string",
            "wealth_label" => "nullable|string",
            "annual_income" => "nullable|string",
            "purchase_end_use" => "nullable|string",
            "purchase_savings" => "nullable|string",
            "purchase_other" => "nullable|string",
            "purchase_other_label" => "nullable|string",
            "payment_cash" => "nullable|string",
            "payment_cheque" => "nullable|string",
            "payment_bank_transfer" => "nullable|string",
            "payment_mortgage" => "nullable|string",
            "payment_crypto" => "nullable|string",
            "payment_other" => "nullable|string",
            "payment_other_label" => "nullable|string",
            "pep" => "nullable|string",
            "pep_other_label" => "nullable|string",
            "signature" => "nullable|string",

        ]);

      //  try {
            $folderMedia = FolderMedia::find($request->file_id);
            $source_file = FolderMedia::where('slug','lead-kyc-form-individual')->first();
            if (!is_null($source_file)) {
                if(!empty($folderMedia)) {
                    $folder = Folder::find($folderMedia->folder_id) ?? null;
                }else{
                    $folder = null;
                }
                $path = $folder->path ?? $source_file->path;
                $path = $this->fillAndGenerateKycIndividualFillable($validatedData, 'lead-kyc-form-individual', $path ?? '');

                if (is_null($path)) {
                    return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
                }
            if(!empty($folderMedia)) {
                $folderMedia->update([
                    "path" => $path["path"],
                    "extension" => "pdf",
                    "slug" => 'lead-kyc-form-individual-'.Str::random(10),
                    "size" => $path["size"] >> 10, //Size in KB
                    "updated_by_id" => auth()->id(),
                    "data" => $validatedData,
                    "data_editable" => true
                ]);
            }else{
                $folderMedia = new FolderMedia();
                $folderMedia->title = 'KYC Individual by ' . auth()->user()->name;
                $folderMedia->path = $path["path"];
                $folderMedia->extension = "pdf";
                $folderMedia->size = $path["size"] >> 10;
                $folderMedia->created_by_id = auth()->id();
                $folderMedia->type = 'Document';
                $folderMedia->data = $validatedData;
                $folderMedia->data_editable = true;
                $folderMedia->save();
            }
                return response()->json(['status' => '1', 'msg' => 'PDF generated successfully.', 'url' => $path["path"]]);
            }else{
                return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
            }




//        } catch (Exception $e) {
//            return response()->json(['status' => '0', 'msg' => $e->getMessage()]);
//            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
//        }
    }
    public function generateKycIndividualFillableV2(Request $request)
    { //this will return array response instead of json response

        $validatedData = $request->validate([
            "file_id" => "nullable|exists:folder_media,id",
            "filename" => "required|string",
            "type" => "required|string",
            "lead_id" => "nullable|exists:leads,id",
            "full_name" => "nullable|string",
            "aliases" => "nullable|string",
            "current_residential_address" => "nullable|string",
            "email" => "nullable|string",
            "telephone_number" => "nullable|string",
            "mobile_number" => "nullable|string",
            "dob" => "nullable|string",
            "pob" => "nullable|string",
            "gender" => "nullable|string",
            "nationality" => "nullable|string",
            "passport_number" => "nullable|string",
            "passport_issue_country" => "nullable|string",
            "passport_issue_date" => "nullable|string",
            "passport_expiry_date" => "nullable|string",
            "fund_employment_income" => "nullable|string",
            "fund_sale_of_investment" => "nullable|string",
            "fund_business_income" => "nullable|string",
            "fund_savings" => "nullable|string",
            "fund_other" => "nullable|string",
            "fund_other_label" => "nullable|string",
            "residency_visa_number" => "nullable|string",
            "emirates_id_no" => "nullable|string",
            "visa_expiry_date" => "nullable|string",
            "occupation" => "nullable|string",
            "years_in_role" => "nullable|string",
            "employer_name" => "nullable|string",
            "employer_address" => "nullable|string",
            "wealth_employment_income" => "nullable|string",
            "wealth_sale_of_inv_property" => "nullable|string",
            "wealth_business_income" => "nullable|string",
            "wealth_savings" => "nullable|string",
            "wealth_inheritance" => "nullable|string",
            "wealth_other" => "nullable|string",
            "wealth_label" => "nullable|string",
            "annual_income" => "nullable|string",
            "purchase_end_use" => "nullable|string",
            "purchase_savings" => "nullable|string",
            "purchase_other" => "nullable|string",
            "purchase_other_label" => "nullable|string",
            "payment_cash" => "nullable|string",
            "payment_cheque" => "nullable|string",
            "payment_bank_transfer" => "nullable|string",
            "payment_mortgage" => "nullable|string",
            "payment_crypto" => "nullable|string",
            "payment_other" => "nullable|string",
            "payment_other_label" => "nullable|string",
            "pep" => "nullable|string",
            "pep_other_label" => "nullable|string",
            "signature" => "nullable|string",

        ]);

      //  try {
            $folderMedia = FolderMedia::find($request->file_id);
            $source_file = FolderMedia::where('slug','lead-kyc-form-individual')->first();

            if (!is_null($source_file)) {
                if(!empty($folderMedia)) {
                    $folder = Folder::find($folderMedia->folder_id) ?? null;
                }else{
                    $folder = null;
                }
                $path = $folder->path ?? $source_file->path;

                $path = $this->fillAndGenerateKycIndividualFillable($validatedData, 'lead-kyc-form-individual', $path ?? '');

                if (is_null($path)) {
                    return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
                }

            if(!empty($folderMedia)) {
                $folderMedia->update([
                    "path" => $path["path"],
                    "extension" => "pdf",
                    "slug" => 'lead-kyc-form-individual-'.Str::random(10),
                    "size" => $path["size"] >> 10, //Size in KB
                    "updated_by_id" => auth()->id(),
                    "data" => $validatedData,
                    "data_editable" => true
                ]);
            }else{
                $folderMedia = new FolderMedia();
                $folderMedia->title = 'KYC Individual by ' . auth()->user()->name;
                $folderMedia->path = $path["path"];
                $folderMedia->extension = "pdf";
                $folderMedia->size = $path["size"] >> 10;
                $folderMedia->created_by_id = auth()->id();
                $folderMedia->type = 'Document';
                $folderMedia->data = $validatedData;
                $folderMedia->data_editable = true;
                $folderMedia->save();
            }
                return ['status' => '1', 'msg' => 'PDF generated successfully.', 'url' => $path["path"]];
            }else{
                return['status' => '0', 'msg' => 'Unable to generate file.'];
            }


    }
    public function generateKycEntityFillableV2(Request $request)
    {
        $validatedData = $request->validate([
            "file_id" => "nullable|exists:folder_media,id",
            "filename" => "required|string",
            "type" => "required|string",
            "lead_id" => "required|exists:leads,id",
            "full_legal_name" => "nullable|string",
            "trading_name" => "nullable|string",
            "company_trading_name" => "nullable|string",
            "company_trad_license_no" => "nullable|string",
            "coi" => "nullable|string",
            "doi" => "nullable|string",
            "address" => "nullable|string",
            "trade_license_authority" => "nullable|string",
            "phone" => "nullable|string",
            "email" => "nullable|string",
            "trade_license_issue_date" => "nullable|string",
            "trade_license_expiry_date" => "nullable|string",
            "ubo_name_1" => "nullable|string",
            "ubo_percentage_1" => "nullable|string",
            "ubo_name_2" => "nullable|string",
            "ubo_percentage_2" => "nullable|string",
            "company_person1_name" => "nullable|string",
            "company_person1_relationship" => "nullable|string",
            "company_person2_name" => "nullable|string",
            "company_person2_relationship" => "nullable|string",
            "company_product" => "nullable|string",
            "company_primary_countries" => "nullable|string",
            "company_individual_occupation" => "nullable|string",
            "name_of_employer" => "nullable|string",
            "deal_with_risky_countries" => "nullable|string",
            "politically_exposed" => "nullable|string",
            "company_person_name" => "nullable|string",
            "company_person_relationship" => "nullable|string",
            "declaration_date" => "nullable|string",
            "signature" => "nullable|string",
        ]);

        try {
            $folderMedia = FolderMedia::find($request->file_id);
            $source_file = FolderMedia::where('slug','lead-kyc-form-entity')->first();
            if (!is_null($source_file)) {
                if(!empty($folderMedia)) {
                    $folder = Folder::find($folderMedia->folder_id) ?? null;
                }else{
                    $folder = null;
                }
                $path = $folder->path ?? $source_file->path;
                $name = \Str::random(10).'lead-kyc-form-entity';
                $path = $this->fillAndGenerateKycEntityFillable($validatedData, $name, $path);

                if (is_null($path)) {
                    return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
                }
            if(!empty($folderMedia)) {
                $folderMedia->update([
                    "path" => $path["path"],
                    "extension" => "pdf",
                    "size" => $path["size"] >> 10, //Size in KB
                    "updated_by_id" => auth()->id(),
                    "data" => $validatedData,
                    "data_editable" => true
                ]);
            }

                return ['status' => '1', 'msg' => 'PDF generated successfully.', 'url' => $path["path"]];
            }
            return ['status' => '0', 'msg' => 'PDF not generated. 1'];

        } catch (Exception $e) {
            return ['status' => '0', 'msg' => 'PDF not generated. 2'];
        }
    }
    public function generateFormAContractSellerBrokerFillable(Request $request)
    {

        $validatedData = $request->validate([
            "file_id" => "nullable|exists:folder_media,id",
            "filename" => "required|string",
            "type" => "required|string",
            "contract_number" => "nullable|string",
            "agent_office_name" => "nullable|string",
            "agent_office_name_ar" => "nullable|string",
            "agent_license_authority" => "nullable|string",
            "agent_license_authority_ar" => "nullable|string",
            "agent_orn" => "nullable|string",
            "agent_license_number" => "nullable|string",
            "agent_fax" => "nullable|string",
            "agent_phone" => "nullable|string",
            "agent_address" => "nullable|string",
            "agent_address_ar" => "nullable|string",
            "agent_email" => "nullable|string",
            "agent_email_ar" => "nullable|string",
            "agent_name" => "nullable|string",
            "agent_name_ar" => "nullable|string",
            "agent_brn" => "nullable|string",
            "agent_mobile" => "nullable|string",
            "owner_name" => "nullable|string",
            "owner_name_ar" => "nullable|string",
            "owner_id_card_number" => "nullable|string",
            "owner_nationality" => "nullable|string",
            "owner_passport_number" => "nullable|string",
            "owner_passport_expiry_date" => "nullable|string",
            "owner_mobile" => "nullable|string",
            "owner_po_box" => "nullable|string",
            "owner_phone" => "nullable|string",
            "owner_fax" => "nullable|string",
            "owner_address" => "nullable|string",
            "owner_address_ar" => "nullable|string",
            "owner_email" => "nullable|string",
            "owner_email_ar" => "nullable|string",
            "property_status" => "nullable|string",
            "property_plot_number" => "nullable|string",
            "type_of_area" => "nullable|string",
            "title_deed_number" => "nullable|string",
            "property_location" => "nullable|string",
            "property_number" => "nullable|string",
            "property_type" => "nullable|string",
            "project_name" => "nullable|string",
            "property_area" => "nullable|string",
            "owner_association_no" => "nullable|string",
            "property_present_use" => "nullable|string",
            "community_number" => "nullable|string",
            "property_age" => "nullable|string",
            "car_parks" => "nullable|string",
            "bedrooms" => "nullable|string",
            "bathrooms" => "nullable|string",
            "kitchens" => "nullable|string",
            "unit_numbers" => "nullable|string",
            "floor_no" => "nullable|string",
            "no_of_floors" => "nullable|string",
            "no_of_shops" => "nullable|string",
            "facilities" => "nullable|string",
            "facilities_ar" => "nullable|string",
            "extra_facilities" => "nullable|string",
            "extra_facilities_ar" => "nullable|string",
            "additional_information" => "nullable|string",
            "listed_price" => "nullable|string",
            "original_price" => "nullable|string",
            "paid_amount" => "nullable|string",
            "balance_amount" => "nullable|string",
            "service_charge" => "nullable|string",
            "mortgage_status" => "nullable|string",
            "mortgage_reg_no" => "nullable|string",
            "property_bank" => "nullable|string",
            "mortgage_amount" => "nullable|string",
            "pre_closure_charge" => "nullable|string",
            "pre_closure_charge_ar" => "nullable|string",
            "payment_schedule" => "nullable|string",
            "payment_schedule_ar" => "nullable|string",
            "payment_date1" => "nullable|string",
            "payment_date2" => "nullable|string",
            "amount_aed1" => "nullable|string",
            "amount_aed2" => "nullable|string",
            "is_rented" => "nullable|string",
            "is_rented_ar" => "nullable|string",
            "contract_start_date" => "nullable|string",
            "contract_end_date" => "nullable|string",
            "commission_amount" => "nullable|string",
            "commission_amount_ar" => "nullable|string",
            "contract_type" => "nullable|string",
            "activity_reporting" => "nullable|string",
            "broker_name" => "nullable|string",
            "broker_name_ar" => "nullable|string",
            "broker_title" => "nullable|string",
            "broker_title_ar" => "nullable|string",
            "broker_date" => "nullable|string",
            "owner_name_en_1" => "nullable|string",
            "owner_name_en_2" => "nullable|string",
            "owner_name_en_3" => "nullable|string",
            "owner_name_ar_1" => "nullable|string",
            "owner_name_ar_2" => "nullable|string",
            "owner_name_ar_3" => "nullable|string",
            "legal_rep_name_en_1" => "nullable|string",
            "legal_rep_name_en_2" => "nullable|string",
            "legal_rep_name_en_3" => "nullable|string",
            "legal_rep_name_ar_1" => "nullable|string",
            "legal_rep_name_ar_2" => "nullable|string",
            "legal_rep_name_ar_3" => "nullable|string",
            "attorney_number1" => "nullable|string",
            "attorney_number2" => "nullable|string",
            "attorney_number3" => "nullable|string",
        ]);

      //  try {
            $folderMedia = FolderMedia::find($request->file_id);
            $source_file = FolderMedia::where('slug','lead-kyc-form-individual')->first();
            if (!is_null($source_file)) {
                if(!empty($folderMedia)) {
                    $folder = Folder::find($folderMedia->folder_id) ?? null;
                }else{
                    $folder = null;
                }
                $path = $folder->path ?? $source_file->path;
                $path = $this->writeFormAContractSellerBroker($validatedData, 'Form-A-contract-seller-broker', $path ?? '');

                if (is_null($path)) {
                    return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
                }
            if(!empty($folderMedia)) {
                $folderMedia->update([
                    "path" => $path["path"],
                    "extension" => "pdf",
                    "slug" => 'Form-A-contract-seller-broker-'.Str::random(10),
                    "size" => $path["size"] >> 10, //Size in KB
                    "updated_by_id" => auth()->id(),
                    "data" => $validatedData,
                    "data_editable" => true
                ]);
            }else{
                $folderMedia = new FolderMedia();
                $folderMedia->title = 'Form A by ' . auth()->user()->name;
                $folderMedia->path = $path["path"];
                $folderMedia->slug = 'Form-A-contract-seller-broker-'.Str::random(10);
                $folderMedia->extension = "pdf";
                $folderMedia->size = $path["size"] >> 10;
                $folderMedia->created_by_id = auth()->id();
                $folderMedia->type = 'Document';
                $folderMedia->data = $validatedData;
                $folderMedia->data_editable = true;
                $folderMedia->save();
            }
                return response()->json(['status' => '1', 'msg' => 'PDF generated successfully.', 'url' => $path["path"]]);
            }else{
                return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
            }




//        } catch (Exception $e) {
//            return response()->json(['status' => '0', 'msg' => $e->getMessage()]);
//            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
//        }
    }
    public function generateKeyHandoverFormFillable(Request $request)
    {

        $validatedData = $request->validate([
            "file_id" => "nullable|exists:folder_media,id",
            "filename" => "required|string",
            "type" => "required|string",
            "date" => "nullable|string",
            "time" => "nullable|string",
            "property_details" => "nullable|string",
            "main_door" => "nullable|string",
            "parking" => "nullable|string",
            "main_door_keys" => "nullable|string",
            "access_card" => "nullable|string",
            "main_door_access_card" => "nullable|string",
            "transporter" => "nullable|string",
            "main_door_remote_control" => "nullable|string",
            "remote_control" => "nullable|string",
            "others" => "nullable|string",
            "long_text" => "nullable|string",
            "user_name" => "nullable|string",
            "user_relative_name" => "nullable|string",

        ]);

        //  try {
        $folderMedia = FolderMedia::find($request->file_id);
        $source_file = FolderMedia::where('slug','key-handover-form')->first();
        if (!is_null($source_file)) {
            if(!empty($folderMedia)) {
                $folder = Folder::find($folderMedia->folder_id) ?? null;
            }else{
                $folder = null;
            }
            $path = $folder->path ?? $source_file->path;
            $path = $this->writeKeyHandoverForm($validatedData, 'key-handover-form', $path ?? '');

            if (is_null($path)) {
                return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
            }
            if(!empty($folderMedia)) {
                $folderMedia->update([
                    "path" => $path["path"],
                    "slug" => 'key-handover-form-'.Str::random(10),
                    "extension" => "pdf",
                    "size" => $path["size"] >> 10, //Size in KB
                    "updated_by_id" => auth()->id(),
                    "data" => $validatedData,
                    "data_editable" => true
                ]);
            }else{
                $folderMedia = new FolderMedia();
                $folderMedia->title = 'KYC Individual by ' . auth()->user()->name;
                $folderMedia->path = $path["path"];
                $folderMedia->slug = 'key-handover-form-'.Str::random(10);
                $folderMedia->extension = "pdf";
                $folderMedia->size = $path["size"] >> 10;
                $folderMedia->created_by_id = auth()->id();
                $folderMedia->type = 'Document';
                $folderMedia->data = $validatedData;
                $folderMedia->data_editable = true;
                $folderMedia->save();
            }
            return response()->json(['status' => '1', 'msg' => 'PDF generated successfully.', 'url' => $path["path"]]);
        }else{
            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
        }




//        } catch (Exception $e) {
//            return response()->json(['status' => '0', 'msg' => $e->getMessage()]);
//            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
//        }
    }
    public function generateLeasingLetterIntentPayableFillable(Request $request)
    {


        $validatedData = $request->validate([
            "file_id" => "nullable|exists:folder_media,id",
            "filename" => "required|string",
            "type" => "required|string",
            "date" => "nullable|string",
            "client_name" => "nullable|string",
            "property" => "nullable|string",
            "leasing_start_date" => "nullable|string",
            "annual_rent" => "nullable|string",
            "annual_rent_payable_to" => "nullable|string",
            "security_deposit" => "nullable|string",
            "security_deposit_payable_to" => "nullable|string",
            "agency_fee" => "nullable|string",
            "agency_fee_payable_to" => "nullable|string",


        ]);

        //  try {
        $folderMedia = FolderMedia::find($request->file_id);
        $source_file = FolderMedia::where('slug','leasing-letter-of-intent-payables')->first();
        if (!is_null($source_file)) {
            if(!empty($folderMedia)) {
                $folder = Folder::find($folderMedia->folder_id) ?? null;
            }else{
                $folder = null;
            }
            $path = $folder->path ?? $source_file->path;
            $path = $this->writeLeasingLetterIntentPayable($validatedData, 'Leasing-Letter-of-Intent-Payables', $path ?? '');

            if (is_null($path)) {
                return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
            }
            if(!empty($folderMedia)) {
                $folderMedia->update([
                    "path" => $path["path"],
                    "slug" => 'Leasing-Letter-of-Intent-Payables-'.Str::random(10),
                    "extension" => "pdf",
                    "size" => $path["size"] >> 10, //Size in KB
                    "updated_by_id" => auth()->id(),
                    "data" => $validatedData,
                    "data_editable" => true
                ]);
            }else{
                $folderMedia = new FolderMedia();
                $folderMedia->title = 'KYC Individual by ' . auth()->user()->name;
                $folderMedia->path = $path["path"];
                $folderMedia->slug = 'Leasing-Letter-of-Intent-Payables-'.Str::random(10);
                $folderMedia->extension = "pdf";
                $folderMedia->size = $path["size"] >> 10;
                $folderMedia->created_by_id = auth()->id();
                $folderMedia->type = 'Document';
                $folderMedia->data = $validatedData;
                $folderMedia->data_editable = true;
                $folderMedia->save();
            }
            return response()->json(['status' => '1', 'msg' => 'PDF generated successfully.', 'url' => $path["path"]]);
        }else{
            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
        }




//        } catch (Exception $e) {
//            return response()->json(['status' => '0', 'msg' => $e->getMessage()]);
//            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
//        }
    }
 public function generateLeasingLetterIntentPurchaseFillable(Request $request)
    {


        $validatedData = $request->validate([
            "file_id" => "nullable|exists:folder_media,id",
            "filename" => "required|string",
            "type" => "required|string",
            "date" => "nullable|string",
            "property" => "nullable|string",
            "subject" => "nullable|string",
            "purchaser" => "nullable|string",
            "offer" => "nullable|string",
            "agency_commission" => "nullable|string",
            "security_deposit" => "nullable|string",
            "held_by" => "nullable|string",
            "property_consultant" => "nullable|string",



        ]);

        //  try {
        if($request->creation_type == 'new'){
            $folderMedia = '';
        }else{
            $folderMedia = FolderMedia::find($request->file_id);
        }

        $source_file = FolderMedia::where('slug','sales-letter-of-intent-purchase')->first();
        if (!is_null($source_file)) {
            if(!empty($folderMedia)) {
                $folder = Folder::find($folderMedia->folder_id) ?? null;
            }else{
                $folder = null;
            }
            $path = $folder->path ?? $source_file->path;
            $path = $this->writeSalesLetterIntentPurchase($validatedData, 'Sales-Letter-Of-Intent-Purchase', $path ?? '');

            if (is_null($path)) {
                return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
            }
            if(!empty($folderMedia)) {
                $folderMedia->update([
                    "path" => $path["path"],
                    "extension" => "pdf",
                    "slug" => 'Sales-Letter-Of-Intent-Purchase-'.Str::random(10),

                    "size" => $path["size"] >> 10, //Size in KB
                    "updated_by_id" => auth()->id(),
                    "data" => $validatedData,
                    "data_editable" => true
                ]);
            }else{
                $folderMedia = new FolderMedia();
                $folderMedia->title = 'Leasing Letter Of Intent Purchase ' . auth()->user()->name;
                $folderMedia->path = $path["path"];
                $folderMedia->slug = 'Sales-Letter-Of-Intent-Purchase-'.Str::random(10);
                $folderMedia->extension = "pdf";
                $folderMedia->size = $path["size"] >> 10;
                $folderMedia->created_by_id = auth()->id();
                $folderMedia->type = 'Document';
                $folderMedia->data = $validatedData;
                $folderMedia->data_editable = true;
                $folderMedia->save();
            }
            return response()->json(['status' => '1', 'msg' => 'PDF generated successfully.', 'url' => $path["path"]]);
        }else{
            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
        }




//        } catch (Exception $e) {
//            return response()->json(['status' => '0', 'msg' => $e->getMessage()]);
//            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
//        }
    }
    public function generateLeasingReservationFormFillable(Request $request)
    {



        $validatedData = $request->validate([
            "file_id" => "nullable|exists:folder_media,id",
            "filename" => "required|string",
            "type" => "required|string",
            "creation_type" => "nullable|string",
            "property_name" => "nullable|string",
            "residential" => "nullable|string",
            "commercial" => "nullable|string",
            "unit" => "nullable|string",
            "unit_types" => "nullable|string",
            "unit_ref" => "nullable|string",
            "unit_area" => "nullable|string",
            "tenant" => "nullable|string",
            "individual" => "nullable|string",
            "corporate" => "nullable|string",
            "government" => "nullable|string",
            "occupants_name" => "nullable|string",
            "po_box" => "nullable|string",
            "adults" => "nullable|string",
            "children" => "nullable|string",
            "children_age" => "nullable|string",
            "marital_status" => "nullable|string",
            "tenant_passport" => "nullable|string",
            "visa_type" => "nullable|string",
            "dob" => "nullable|string",
            "visa_no" => "nullable|string",
            "profession" => "nullable|string",
            "nationality" => "nullable|string",
            "office_number" => "nullable|string",
            "mobile" => "nullable|string",
            "fax" => "nullable|string",
            "email" => "nullable|string",
            "company_name" => "nullable|string",
            "landlord_name" => "nullable|string",
            "contract_start_date" => "nullable|string",
            "lease_term" => "nullable|string",
            "annual_rent" => "nullable|string",
            "cheques" => "nullable|string",
            "security_deposit" => "nullable|string",
            "agency_fee" => "nullable|string",






        ]);

        //  try {
        if($request->creation_type == 'new'){
            $folderMedia = '';
        }else{
            $folderMedia = FolderMedia::find($request->file_id);
        }

        $source_file = FolderMedia::where('slug','leasing-reservation-form')->first();
        if (!is_null($source_file)) {
            if(!empty($folderMedia)) {
                $folder = Folder::find($folderMedia->folder_id) ?? null;
            }else{
                $folder = null;
            }
            $path = $folder->path ?? $source_file->path;
            $path = $this->writeLeasingReservationForm($validatedData, 'Leasing-Reservation-Form', $path ?? '');

            if (is_null($path)) {
                return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
            }
            if(!empty($folderMedia)) {
                $folderMedia->update([
                    "path" => $path["path"],
                    "slug" => 'Leasing-Reservation-Form-'.Str::random(10),
                    "extension" => "pdf",
                    "size" => $path["size"] >> 10, //Size in KB
                    "updated_by_id" => auth()->id(),
                    "data" => $validatedData,
                    "data_editable" => true
                ]);
            }else{
                $folderMedia = new FolderMedia();
                $folderMedia->title = 'Leasing Reservation Form ' . auth()->user()->name;
                $folderMedia->path = $path["path"];
                $folderMedia->slug = 'Leasing-Reservation-Form-'.Str::random(10);
                $folderMedia->extension = "pdf";
                $folderMedia->size = $path["size"] >> 10;
                $folderMedia->created_by_id = auth()->id();
                $folderMedia->type = 'Document';
                $folderMedia->data = $validatedData;
                $folderMedia->data_editable = true;
                $folderMedia->save();
            }
            return response()->json(['status' => '1', 'msg' => 'PDF generated successfully.', 'url' => $path["path"]]);
        }else{
            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
        }




//        } catch (Exception $e) {
//            return response()->json(['status' => '0', 'msg' => $e->getMessage()]);
//            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
//        }
    }
    public function generateSalesReservationFormFillable(Request $request)
    {

        $validatedData = $request->validate([
            "file_id" => "nullable|exists:folder_media,id",
            "filename" => "required|string",
            "type" => "required|string",
            "creation_type" => "nullable|string",
            "property_name" => "nullable|string",
            "residential" => "nullable|string",
            "commercial" => "nullable|string",
            "unit" => "nullable|string",
            "unit_types" => "nullable|string",
            "unit_ref" => "nullable|string",
            "unit_area" => "nullable|string",
            "tenant" => "nullable|string",
            "individual" => "nullable|string",
            "corporate" => "nullable|string",
            "government" => "nullable|string",
            "deal_type" => "nullable|string",
            "po_box" => "nullable|string",
            "adults" => "nullable|string",
            "children" => "nullable|string",
            "children_age" => "nullable|string",
            "marital_status" => "nullable|string",
            "tenant_passport" => "nullable|string",
            "visa_type" => "nullable|string",
            "dob" => "nullable|string",
            "visa_no" => "nullable|string",
            "profession" => "nullable|string",
            "nationality" => "nullable|string",
            "office_number" => "nullable|string",
            "mobile" => "nullable|string",
            "fax" => "nullable|string",
            "email" => "nullable|string",
            "company_name" => "nullable|string",
            "landlord_name" => "nullable|string",
            "contract_start_date" => "nullable|string",
            "lease_term" => "nullable|string",
            "annual_rent" => "nullable|string",
            "cheques" => "nullable|string",
            "security_deposit" => "nullable|string",
            "agency_fee" => "nullable|string",
        ]);

        //  try {
        if($request->creation_type == 'new'){
            $folderMedia = '';
        }else{
            $folderMedia = FolderMedia::find($request->file_id);
        }

        $source_file = FolderMedia::where('slug','sales-reservation-form')->first();
        if (!is_null($source_file)) {
            if(!empty($folderMedia)) {
                $folder = Folder::find($folderMedia->folder_id) ?? null;
            }else{
                $folder = null;
            }
            $path = $folder->path ?? $source_file->path;
            $path = $this->writeSalesReservationForm($validatedData, 'Sales-Reservation-Form', $path ?? '');

            if (is_null($path)) {
                return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
            }
            if(!empty($folderMedia)) {
                $folderMedia->update([
                    "path" => $path["path"],
                    "slug" => 'Sales-Reservation-Form-'.Str::random(10),
                    "extension" => "pdf",
                    "size" => $path["size"] >> 10, //Size in KB
                    "updated_by_id" => auth()->id(),
                    "data" => $validatedData,
                    "data_editable" => true
                ]);
            }else{
                $folderMedia = new FolderMedia();
                $folderMedia->title = 'Leasing Reservation Form ' . auth()->user()->name;
                $folderMedia->path = $path["path"];
                $folderMedia->extension = "pdf";
                $folderMedia->size = $path["size"] >> 10;
                $folderMedia->created_by_id = auth()->id();
                $folderMedia->type = 'Document';
                $folderMedia->data = $validatedData;
                $folderMedia->data_editable = true;
                $folderMedia->save();
            }
            return response()->json(['status' => '1', 'msg' => 'PDF generated successfully.', 'url' => $path["path"]]);
        }else{
            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
        }




//        } catch (Exception $e) {
//            return response()->json(['status' => '0', 'msg' => $e->getMessage()]);
//            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
//        }
    }

    public function generateKycComplianceDFormFillable(Request $request)
    {

//we: Ipsum consequatur
//trade_license: Vel qui rem est fug
//reg_no: Voluptate eius fuga
//company_name: Barnett and Frank Plc
//company_trade_license: Proident qui Nam qu
//share_holder1: Mollit corrupti qui
//share_holder_passport_no1: Rerum accusantium al
//share_holder2: Quod placeat saepe
//share_holder_passport_no2: Fuga Quis est ab ex
//share_holder3: Occaecat velit culp
//share_holder_passport_no3: Debitis mollit repel
//descriptions: [object Object],[object Object],[object Object],[object Object],[object Object],[object Object],[object Object],[object Object],[object Object],[object Object],[object Object],[object Object],[object Object]
//yes_no: Yes
        $validatedData = $request->validate([
            "file_id" => "nullable|exists:folder_media,id",
            "filename" => "required|string",
            "type" => "required|string",
            "creation_type" => "nullable|string",
            "we" => "nullable|string",
            "trade_license" => "nullable|string",
            "reg_no" => "nullable|string",
            "company_name" => "nullable|string",
            "company_trade_license" => "nullable|string",
            "share_holder1" => "nullable|string",
            "share_holder_passport_no1" => "nullable|string",
            "share_holder2" => "nullable|string",
            "share_holder_passport_no2" => "nullable|string",
            "share_holder3" => "nullable|string",
            "share_holder_passport_no3" => "nullable|string",
            "descriptions" => "nullable|string",
            "yes_no" => "nullable|string",
            "explanation" => "nullable|string",


        ]);

        //  try {
        if($request->creation_type == 'new'){
            $folderMedia = '';
        }else{
            $folderMedia = FolderMedia::find($request->file_id);
        }

        $source_file = FolderMedia::where('slug','kyc-compliance-declaration-form')->first();
        if (!is_null($source_file)) {
            if(!empty($folderMedia)) {
                $folder = Folder::find($folderMedia->folder_id) ?? null;
            }else{
                $folder = null;
            }
            $path = $folder->path ?? $source_file->path;
            $path = $this->writeKycComplianceDForm($validatedData, 'KYC-Compliance-Declaration-Form', $path ?? '');

            if (is_null($path)) {
                return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
            }
            if(!empty($folderMedia)) {
                $folderMedia->update([
                    "path" => $path["path"],
                    "slug" => 'kyc-compliance-declaration-form-'.Str::random(10),
                    "extension" => "pdf",
                    "size" => $path["size"] >> 10, //Size in KB
                    "updated_by_id" => auth()->id(),
                    "data" => $validatedData,
                    "data_editable" => true
                ]);
            }else{
                $folderMedia = new FolderMedia();
                $folderMedia->title = 'Leasing Reservation Form ' . auth()->user()->name;
                $folderMedia->path = $path["path"];
                $folderMedia->extension = "pdf";
                $folderMedia->size = $path["size"] >> 10;
                $folderMedia->created_by_id = auth()->id();
                $folderMedia->type = 'Document';
                $folderMedia->data = $validatedData;
                $folderMedia->data_editable = true;
                $folderMedia->save();
            }
            return response()->json(['status' => '1', 'msg' => 'PDF generated successfully.', 'url' => $path["path"]]);
        }else{
            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
        }




//        } catch (Exception $e) {
//            return response()->json(['status' => '0', 'msg' => $e->getMessage()]);
//            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
//        }
    }

    public function updateFile(Request $request)
    {
        $request->validate([
            "id" => "required|exists:folder_media,id",
            "title" => "required|string",
            "slug" => "required|string|unique:folder_media,slug," . $request->id,
            "description" => "nullable|string",
        ]);

        try {
            $folderMedia = FolderMedia::find($request->id);
            if (!is_null($folderMedia)) {
                $folderMedia->update([
                    'title' => $request->title,
                    'slug' => $request->slug,
                    'description' => $request->description,
                    'updated_by_id' => auth()->id(),
                ]);
                return response()->json(['status' => '1', 'msg' => 'PDF generated successfully.']);
            }
            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
        } catch (Exception $e) {
            return response()->json(['status' => '0', 'msg' => 'Unable to generate file.']);
        }

    }
}
