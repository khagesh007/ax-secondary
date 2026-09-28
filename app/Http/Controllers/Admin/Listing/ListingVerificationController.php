<?php

namespace App\Http\Controllers\Admin\Listing;

use App\Http\Controllers\Controller;
use App\Http\Resources\ListingVerification\PropertyResource;
use App\Models\Property;
use App\Models\PropertyMedia;
use App\Services\Portal\ListingVerificationService;
use App\Traits\FileTrait;
use App\Traits\FiltersTrait;
use App\Traits\PDFTrait;
use App\Traits\TelegramTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ListingVerificationController extends Controller
{
    use FileTrait, FiltersTrait, TelegramTrait, PDFTrait;


    public function __construct(protected ListingVerificationService $listingVerificationService)
    {
    }

    public function index()
    {
        return view('admin.listing_verifications.index');
    }

    public function submitForVerification(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:properties,id',
            'documents' => 'nullable|array',
            'other_documents' => 'nullable|array',
        ]);

        try {

            $property = Property::find($request->id);

            $isEligible = $this->listingVerificationService->checkEligibility($property->id);
            if (!$isEligible){
                return response()->json([
                    'status' => false,
                    'message' => 'Listing is not eligible for verification.',
                ]);
            }

            $documents = [];

            foreach ($request->documents ?? [] as $document) {
               $selected = isset($document["selected"]) ? ($document["selected"] == "true" || $document["selected"] == true) ? true : "false" : "false" ;
                if ($selected == true){
                    if (!empty($document["type"])){
                        $documents[$document["type"]][] = $document["path"];
                    }
                }
            }

            $otherDocuments = [];

            foreach ($request->other_documents ?? [] as $key => $document) {
                if (!empty($document["file"])) {
                    if (!empty($document["type"])){
                        $path = $this->uploadDocument($document["file"], "listings/" . $property->ref_no . "/" . $document['title']);
                        if (!empty($path)) {
                            $customDoc = new PropertyMedia();
                            $customDoc->property_id = $property->id;
                            $customDoc->type = 'Other';
                            $customDoc->title = $document["title"] ?? "Other";
                            $customDoc->path = $path ?? null;
                            $customDoc->created_by_id = auth()->id();
                            $original = $customDoc->getOriginal() ?? [];
                            $dirty = $customDoc->getDirty() ?? [];
                            $customDoc->save();

                            $otherDocuments[$document['type']][] = $path;

                            activity()
                                ->performedOn($customDoc)
                                ->causedBy(auth()->user())
                                ->withProperties([
                                    'attributes' => $dirty,
                                    'old' => $original,
                                    'property_id' => $property->id
                                ])
                                ->event('created')
                                ->log(auth()->user()->first_name . ' has uploaded ' . $customDoc->title . ' document');
                        }
                    }
                }
            }

            if (!empty($otherDocuments)) {
                foreach ($otherDocuments as $key => $document) {
                    $documents[$key] = array_merge($documents[$key] ?? [], $document);
                }
            }

            $submitted = $this->listingVerificationService->submitForVerification($property->id, $documents);
            if (!$submitted){
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to submit listing for verification.',
                ]);
            }

            return response()->json([
                'status' => true,
                'data' => [],
                'message' => 'Listing verification submitted successfully.'
            ]);
        }catch (\Exception $exception){
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
            ]);
        }
    }

    public function reSubmitForVerification(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:listing_verifications,id',
        ]);

        try {
            $submitted = $this->listingVerificationService->reSubmitForVerification($request->id);
            if (!$submitted) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to re-submit listing for verification.',
                ]);
            }

            return response()->json([
                'status' => true,
                'data' => [],
                'message' => 'Listing verification re-submitted successfully.'
            ]);
        }catch (\Exception $exception){
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
            ]);
        }
    }

    public function getUserAndStatusWiseCount(Request $request)
    {
        $date = $request->date;

        $params = [
            'date' => $date,
            'start_date' => $request->start_date != null ? Carbon::parse($request->start_date)->format('Y-m-d') : null,
            'end_date' => $request->end_date != null ? Carbon::parse($request->end_date)->format('Y-m-d') : null,
            'group' => 'secondary',
        ];

        return response()->json([
            'status' => true,
            'data' => [
                'status_count' => $this->listingVerificationService->statusWiseCount($params),
                'user_and_status_count' => $this->listingVerificationService->userAndStatusWiseCount($params),
            ],
        ]);
    }

    public function list(Request $request)
    {
        $date = $request->date;

        $params = [
            'date' => $date,
            'start_date' => $request->start_date != null ? Carbon::parse($request->start_date)->format('Y-m-d') : null,
            'end_date' => $request->end_date != null ? Carbon::parse($request->end_date)->format('Y-m-d') : null,
            'per_page' => $request->per_page,
            'order_by' => $request->order_by,
            'order' => $request->order,
            'status' => $request->status,
        ];

        return response()->json([
            'status' => true,
            'data' => $this->listingVerificationService->getList($params),
        ]);
    }

    public function getPropertiesForVerification(Request $request)
    {
        $params = [
            'term' => $request->term,
        ];

        return response()->json([
            'status' => true,
            'data' => PropertyResource::collection($this->listingVerificationService->getPropertiesForVerifications($params)),
        ]);
    }
}
