<?php

namespace App\Http\Controllers\Admin\Google;

use App\Http\Controllers\Controller;
use App\Models\DrivePhoto;
use App\Models\Property;
use App\Models\PropertyMedia;
use App\Traits\FileTrait;
use DB;
use Google_Client;
use Google_Service_Drive;
use Illuminate\Http\Request;
use Image;
use Storage;

class GoogleDriveController extends Controller
{
    use FileTrait;
    public function _fetechImages(Request $request)
    {

        try {
            if (strpos($request->folder_id, '/') !== false) {
                $folderID = substr($request->folder_id, strrpos($request->folder_id, '/') + 1);
            } else {
                $folderID = $request->folder_id;
            }
            // $folderID='1QjomxoKCotPem7BIUP6pfgGiYlBDcClU';
            $client = new Google_Client();
            $client->setAuthConfig(storage_path('app/totemic-creek-420508-c3dde8ada755.json'));
            $client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
            $client->addScope(Google_Service_Drive::DRIVE_READONLY);
            $client->addScope('https://www.googleapis.com/auth/drive.readonly');
            $client->addScope('https://www.googleapis.com/auth/drive');
            $client->addScope('https://www.googleapis.com/auth/drive.file');
            $client->setAccessType('offline');
            $client->setSubject('secondary-photos@totemic-creek-420508.iam.gserviceaccount.com');
            $service = new Google_Service_Drive($client);
            //check if user has acccess to folderId
            $folder = $service->files->get($folderID, ['fields' => 'id']);
            $files = $service->files->listFiles(['q' => "'$folderID' in parents", 'includeItemsFromAllDrives' => true, 'supportsAllDrives' => true]);
            $images = [];
            // dd($files->getFiles());
            foreach ($files->getFiles() as $file) {
                if (strpos($file->getMimeType(), 'image') !== false) {
                    $images[] = [
                        'id' => $file->getId(),
                        'name' => $file->getName(),
                        'downloadUrl' => 'https://drive.google.com/uc?export=download&id=' . $file->getId(),
                        'viewLink' => $file->getWebViewLink(), // Use getWebViewLink() for viewable link
                        'custom' => 'https://drive.google.com/file/d/' . $file->getId() . '/view', // You can add custom data here
                    ];
                    $image_id = $file->getId();
                    $find = DrivePhoto::where('image_id', $image_id)->first();
                    if ($find) {

                    } else {
                        // $imageContent = file_get_contents('https://drive.google.com/uc?export=download&id=' . $file->getId());
                        $imageContent = $service->files->get($file->getId(), ['alt' => 'media'])->getBody()->getContents();

                        $imagePath = $this->uploadImagetoAWS($imageContent);
                        DB::beginTransaction();
                        $drivepic = new DrivePhoto();
                        $drivepic->property_id = $request->id;
                        $drivepic->image_id = $image_id;
                        $drivepic->name = $file->getName();
                        $drivepic->path = $imagePath['thumbnail'];
                        $drivepic->type = 'Photo';
                        $drivepic->folder_id = $folderID;
                        $drivepic->save();
                        DB::commit();
                    }
                }
            }

            $images = DrivePhoto::where('folder_id', $folderID)->get();

            return view('admin.listing.includes.partials._drive_images')
                ->with('images', $images);

        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            return view('admin.listing.includes.partials._drive_images_not_found')
                ->with('code', $th->getCode());

        }
    }

    public function fetechImages(Request $request)
    {
          // Fetch images from the database based on folder ID
        try {
            // Make sure $folderID is valid
            if (strpos($request->folder_id, '/') !== false) {
                $folderID = substr($request->folder_id, strrpos($request->folder_id, '/') + 1);
            } else {
                $folderID = $request->folder_id;
            }

            $client = new Google_Client();
            $client->setAuthConfig(storage_path('app/totemic-creek-420508-c3dde8ada755.json'));
            $client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
            $client->addScope(Google_Service_Drive::DRIVE_READONLY);
            $client->addScope('https://www.googleapis.com/auth/drive.readonly');
            $client->addScope('https://www.googleapis.com/auth/drive');
            $client->addScope('https://www.googleapis.com/auth/drive.file');
            $client->addScope('https://www.googleapis.com/auth/drive.photos.readonly'); // Add scope for photos if needed
            $client->setAccessType('offline');
            $client->setSubject('secondary-photos@totemic-creek-420508.iam.gserviceaccount.com');
            $service = new Google_Service_Drive($client);

            // List files in the folder
            $files = $service->files->listFiles([
                'q' => "'$folderID' in parents",
                'includeItemsFromAllDrives' => true,
                'supportsAllDrives' => true,
            ]);
            $images = [];
            foreach ($files->getFiles() as $file) {
                if (strpos($file->getMimeType(), 'image') !== false) {
                    $images[] = [
                        'id' => $file->getId(),
                        'name' => $file->getName(),
                        'downloadUrl' => 'https://drive.google.com/uc?export=download&id=' . $file->getId(),
                        'viewLink' => $file->getWebViewLink(),
                        'custom' => 'https://drive.google.com/file/d/' . $file->getId() . '/view',
                    ];

                    $image_id = $file->getId();
                    $find = DrivePhoto::where('image_id', $image_id)->first();
                    if (!$find) {
                        $imageContent = $service->files->get($file->getId(), ['alt' => 'media'])->getBody()->getContents();
                        $imagePath = $this->uploadImagetoAWS($imageContent);

                        DB::beginTransaction();
                        $drivepic = new DrivePhoto();
                        $drivepic->property_id = $request->id; // Assuming $request->id exists
                        $drivepic->image_id = $image_id;
                        $drivepic->name = $file->getName();
                        $drivepic->path = $imagePath['thumbnail'];
                        $drivepic->type = 'Photo';
                        $drivepic->folder_id = $folderID;
                        $drivepic->save();
                        DB::commit();
                    }
                }
            }

            // Fetch images from the database based on folder ID
            $images = DrivePhoto::where('folder_id', $folderID)->get();

            return view('admin.listing.includes.partials._drive_images')
                ->with('images', $images);

        } catch (\Throwable $th) {
            DB::rollBack();
            info($th);
            return view('admin.listing.includes.partials._drive_images_not_found')
                ->with('code', $th->getCode());
        }
    }

    public function uploadImagetoAWS($fileContent, string $path = "", string $disk = "s3")
    {

        try {
            $thumbSize = 200;
            $fileName = time() . "_" . uniqid() . ".jpg";
            $image = Image::make($fileContent);
            // Storage::disk($disk)->put("images/google-drive/" . (!empty($path) ? $path . "/" : "") . "thumb_" . $fileName, $image->encode('jpg')->__toString());
            // $thumbnailUrl = Storage::disk($disk)->url("images/google-drive/" . (!empty($path) ? $path . "/" : "") . "google_" . $fileName);

            Storage::disk($disk)->put("images/" . (!empty($path) ? $path . "/" : "") . "thumb_" . $fileName, $image->encode('jpg')->__toString());
            $thumbnailUrl = Storage::disk($disk)->url("images/" . (!empty($path) ? $path . "/" : "") . "thumb_" . $fileName);

            return [
                "thumbnail" => $thumbnailUrl,
            ];
        } catch (\Exception $exception) {
            print_r($exception->getMessage());
            die();
        }
    }

    public function attachImage(Request $request)
    {
        $request->validate([
            'images' => 'required',
            'id' => 'required',
        ]);
        $property = Property::find($request->id);
        $totalImages = count($request->images);
        $processedImages = 0;
        foreach ($request->images as $key => $image_id) {
            $find = DrivePhoto::where('id', $image_id)->first();
            $data = $this->uploadImageAndThumbWithWatermarkFromURL($find->path);
            if (!empty($data)) {
                $media = new PropertyMedia();
                $media->property_id = $request->id;
                $media->temp_id = $request->temp_id;
                $media->path = $data["image_v3"] ?? null;
                $media->original = $data["original"] ?? null;
                $media->thumbnail = $data["thumbnail"] ?? null;
               // $media->with_watermark = $data["image_v3"] ?? null;
                $media->order = PropertyMedia::count() + 1;
                $media->type = "Photos";
                $media->created_by_id = auth()->id();
                $dirty = $media->getDirty() ?? [];
                $media->save();
                $processedImages++;
                // Calculate progress percentage
                $progress = ($processedImages / $totalImages) * 100;

                // Send progress as part of the response
                $result = array(
                    'status' => '1',
                    'msg' => "Photo uploaded successfully.",
                    'data' => $media,
                    'progress' => $progress,
                );
                // ob_flush();
                // flush(); // Flush output buffer to send the progress information immediately

            }
        }

        $result = array('status' => '1', 'msg' => "Photo uploaded successfully.", 'data' => $media);
        return json_encode($result);
    }
}
