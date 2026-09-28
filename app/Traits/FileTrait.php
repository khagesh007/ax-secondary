<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ZipArchive;
trait FileTrait
{

    //Change default $disk to s3
    public function uploadImageAndThumbWithWatermarkApi(
        $file,
        int $width = 1200,
        int $height = 900,
        string $path = "",
        string $disk = "s3",
        string $watermark = "assets/images/logo/watermarkv3.png"
    ) {

        try {
            $thumbWidth = ($width / 300) * 75;
            $thumbHeight = ($height / 300) * 75;
            $extension = 'jpg';//$file->getClientOriginalExtension();

            $fileName = time() . date("YmdHis") . "." . $extension;

            $original = Image::make($file)->encode($extension)->stream();
           // header('Content-Type: image/png');


            $originalPath = Storage::disk($disk)->put("images/" . (!empty($path) ? $path . "/" : "") . "original_" . $fileName, $original->__toString());

            $originalUrl = Storage::disk($disk)->url("images/" . (!empty($path) ? $path . "/" : "") . "original_" . $fileName, $original->__toString());

            $thumbnail = Image::make($file)->resize(
                $thumbWidth,
                $thumbHeight
            )->encode($extension)->stream();

            $thumbnailPath = Storage::disk($disk)->put("images/" . (!empty($path) ? $path . "/" : "") . "thumb_".  $fileName, $thumbnail->__toString());
            $thumbnailUrl = Storage::disk($disk)->url("images/" . (!empty($path) ? $path . "/" : "") . "thumb_" . $fileName);

            if (!empty($watermark)) {
                $image = Image::make($file)->resize($width, $height,function ($constraint) {
                        $constraint->aspectRatio();
                    })->insert(
                    public_path($watermark),
                    "center",
                )->encode($extension)->stream();
            } else {
                $image = Image::make($file)->resize($width, $height,function ($constraint) {
                        $constraint->aspectRatio();
                    })->encode($extension)->stream();
            }
            $imagePath = Storage::disk($disk)->put("images/" . (!empty($path) ? $path . "/" : "") . $fileName, $image->__toString());
            $imageUrl = Storage::disk($disk)->url("images/" . (!empty($path) ? $path . "/" : "") . $fileName);

            return [
                "image" => $imageUrl,
                "thumbnail" => $thumbnailUrl,
                "original" => $originalUrl
            ];
        } catch (\Exception $exception) {
            $image['error'] = "cannot upload file";
            return $image;
            print_r($exception->getMessage());
            die();
        }
    }
    public function uploadImageAndThumbWithWatermark(
        $file,
        int $width = 1200,
        int $height = 900,
        string $path = "",
        string $disk = "s3",
        string $watermark = "assets/images/logo/watermarkv3.png"
    ) {
        try {
            $thumbWidth = ($width / 300) * 75;
            $thumbHeight = ($height / 300) * 75;
            $extension = $file->getClientOriginalExtension();
            $randomString = Str::random(10);
            $fileName = time().date("YmdHis").$randomString.".".$extension;


            $original = Image::make($file->getRealPath())->encode($extension)->stream();
            $originalPath = Storage::disk($disk)->put("images/".(!empty($path) ? $path . "/" : "")."original_".$fileName, $original->__toString());
            $originalUrl = Storage::disk($disk)->url("images/".(!empty($path) ? $path . "/" : "")."original_".$fileName, $original->__toString());

            $thumbnail = Image::make($file->getRealPath())->resize($thumbWidth,
                $thumbHeight)->encode($extension)->stream();
            $thumbnailPath = Storage::disk($disk)->put("images/".(!empty($path) ? $path . "/" : "")."thumb_".$fileName, $thumbnail->__toString());
            $thumbnailUrl = Storage::disk($disk)->url("images/".(!empty($path) ? $path . "/" : "")."thumb_".$fileName);

            // if (!empty($watermark)) {
            //     // $watermarkPath = public_path("assets/images/logo/watermarkv3.png");
            //     $watermarkPath = public_path("assets/images/logo/watermarkv2.png");
            //     $watermarkImage = Image::make($watermarkPath);

            //     // Calculate the desired watermark size based on the main image size
            //     $watermarkWidth = $width * 0.15; // 50% of the main image width
            //     $watermarkHeight = $height * 0.15; // 50% of the main image height

            //     // Resize the watermark image while maintaining its resolution
            //     $watermarkImage1 = $watermarkImage->resize($watermarkWidth, $watermarkHeight, function ($constraint) {
            //         $constraint->aspectRatio();
            //         $constraint->upsize(); // Prevent upsizing of the watermark image
            //     });
            //     $image = Image::make($file->getRealPath())
            //     ->resize($width, $height, function ($constraint) {
            //         $constraint->aspectRatio();
            //     })
            //     ->insert($watermarkImage1, "top-right", 90, 40)
            //     ->encode($extension)
            //     ->stream();
            //     // $image = Image::make($file->getRealPath())->resize($width, $height,function ($constraint) {
            //     //         $constraint->aspectRatio();
            //     //     })->insert(public_path($watermark),
            //     //     "top-right", 80, 40)->encode($extension)->stream();
            // } else {
            //     $image = Image::make($file->getRealPath())->resize($width, $height,function ($constraint) {
            //             $constraint->aspectRatio();
            //         })->encode($extension)->stream();
            // }
           // $imagePath = Storage::disk($disk)->put("images/".(!empty($path) ? $path . "/" : "").$fileName, $image->__toString());
           // $imageUrl = Storage::disk($disk)->url("images/".(!empty($path) ? $path . "/" : "").$fileName);

            if (!empty($watermark)) {
                $watermarkPath = public_path("assets/images/logo/watermarkv3.png");
                $watermarkImage = Image::make($watermarkPath);

                // Calculate the desired watermark size based on the main image size
                $watermarkWidth = $width * 1; // 50% of the main image width
                $watermarkHeight = $height * 1; // 50% of the main image height

                // Resize the watermark image while maintaining its resolution
                $watermarkImage1 = $watermarkImage->resize($watermarkWidth, $watermarkHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize(); // Prevent upsizing of the watermark image
                });
                $imagev2 = Image::make($file->getRealPath())
                ->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->insert($watermarkImage1, "center")
                ->encode($extension)
                ->stream();
                // $image = Image::make($file->getRealPath())->resize($width, $height,function ($constraint) {
                //         $constraint->aspectRatio();
                //     })->insert(public_path($watermark),
                //     "top-right", 80, 40)->encode($extension)->stream();
            }
            else{
                $imagev2 = Image::make($file->getRealPath())->resize($width, $height,function ($constraint) {
                        $constraint->aspectRatio();
                    })->encode($extension)->stream();

            }
            $imagePathv2 = Storage::disk($disk)->put("images/".(!empty($path) ? $path . "/" : "")."watermarkv3/".$fileName, $imagev2->__toString());
            $imageUrlv2 = Storage::disk($disk)->url("images/".(!empty($path) ? $path . "/" : "")."watermarkv3/".$fileName);

            return [
                "image" => $imageUrlv2,
                "image_v2" => $imageUrlv2,
                "thumbnail" => $thumbnailUrl,
                "original" => $originalUrl
            ];
        } catch (\Exception $exception) {
            print_r($exception->getMessage());
            die();
        }
    }

    public function uploadImageAndThumbOld(
        $file,
        string $path = "",
        string $disk = "s3",
    ) {
        try {
            $thumbWidth = 200;
            $thumbHeight = 200;
            $extension = $file->getClientOriginalExtension();
            $fileName = time().date("YmdHis").".".$extension;

            $thumbnail = Image::make($file->getRealPath())->resize($thumbWidth, $thumbHeight)->encode($extension)->stream();
            $thumbnailPath = Storage::disk($disk)->put("images/".(!empty($path) ? $path . "/" : "")."thumb_".$fileName, $thumbnail->__toString());
            $thumbnailUrl = Storage::disk($disk)->url("images/".(!empty($path) ? $path . "/" : "")."thumb_".$fileName);

            $image = Image::make($file->getRealPath())->encode($extension)->stream();
            $imagePath = Storage::disk($disk)->put("images/".(!empty($path) ? $path . "/" : "").$fileName, $image->__toString());
            $imageUrl = Storage::disk($disk)->url("images/".(!empty($path) ? $path . "/" : "").$fileName);

            return [
                "image" => $imageUrl,
                "thumbnail" => $thumbnailUrl,
            ];
        } catch (\Exception $exception) {
            print_r($exception->getMessage());
            die();
        }
    }
    public function uploadImageAndThumb($file, string $path = "", string $disk = "s3") {
        try {
            $thumbSize = 200;
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . date("YmdHis") . "." . $extension;

            $image = Image::make($file->getRealPath());

            // Crop and resize the image to a square of 200x200
            $image->fit($thumbSize, $thumbSize);

            // Save the thumbnail and the original image to the storage disk
            Storage::disk($disk)->put("images/" . (!empty($path) ? $path . "/" : "") . "thumb_" . $fileName, $image->encode($extension)->__toString());
            $thumbnailUrl = Storage::disk($disk)->url("images/" . (!empty($path) ? $path . "/" : "") . "thumb_" . $fileName);

            Storage::disk($disk)->put("images/" . (!empty($path) ? $path . "/" : "") . $fileName, file_get_contents($file));
            $imageUrl = Storage::disk($disk)->url("images/" . (!empty($path) ? $path . "/" : "") . $fileName);

            return [
                "image" => $imageUrl,
                "thumbnail" => $thumbnailUrl,
            ];
        } catch (\Exception $exception) {
            print_r($exception->getMessage());
            die();
        }
    }

    // Uploads the images for users from bitrix url
    public function uploadImageAndThumbFromUrl(string $imageUrl, string $path = "", string $disk = "s3")
    {
        try {
            $thumbWidth = 200;
            $thumbHeight = 200;
            $randomString = Str::random(10);
            $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);
            $fileName = time().$randomString.date("YmdHis").".".$extension;

            $imageContents = file_get_contents($imageUrl);
            $imagePath = "images/".(!empty($path) ? $path . "/" : "").$fileName;
            Storage::disk($disk)->put($imagePath, $imageContents);
            $imageUrl = Storage::disk($disk)->url($imagePath);

            // Generate and store the thumbnail
            $thumbnailImage = Image::make($imageContents)->fit($thumbWidth, $thumbHeight);
            $thumbnailPath = "images/".(!empty($path) ? $path . "/" : "")."thumb_".$fileName;
            Storage::disk($disk)->put($thumbnailPath, $thumbnailImage->encode($extension)->__toString());
            $thumbnailUrl = Storage::disk($disk)->url($thumbnailPath);

            return [
                "image" => $imageUrl,
                "thumbnail" => $thumbnailUrl,
            ];
        } catch (\Exception $exception) {
            return null;
            throw $exception;
        }
    }


    //Change default $disk to s3
    public function uploadImage(
        $file,
        int $width = 0,
        int $height = 0,
        string $prefix = "",
        string $path = "",
        string $disk = "s3"
    ): string {
        try {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().date("YmdHis").".".$extension;
            if ($width > 0 && $height > 0) {
                $image = Image::make($file->getRealPath())->resize($width, $width)->encode($extension)->stream();
            } else {
                $image = Image::make($file->getRealPath())->encode($extension);
            }
            $imagePath = Storage::disk($disk)->put("images/".(!empty($path) ? $path . "/" : "").$prefix.$fileName, $image->__toString());
            return Storage::disk($disk)->url("images/".(!empty($path) ? $path . "/" : "").$prefix.$fileName);
        } catch (\Exception $exception) {
            print_r($exception->getMessage());
            die();
        }
    }

    public function uploadDocument($file, string $path = "", string $disk = "s3")
    {

        try {
            $extension = $file->getClientOriginalExtension();
            $time = time().date("YmdHis");
            $file_path = $time;
            $fileName = $time.".".$extension;
            $document = Storage::disk($disk)->put("documents/".(!empty($path) ? str_replace(' ', '-', $path) . "" : ""), $file);
            $url = Storage::disk($disk)->url($document);
            return $url;
        } catch (\Exception $exception) {
            throw($exception);
            die();
        }
    }

    public function uploadCallRecording($file, string $path = "")
    {
        try {
            $disk = "s3";
            $extension = $file->getClientOriginalExtension();
            $time = time().date("YmdHis");
            $file_path = $time;
            $fileName = $time.".".$extension;
            $document =Storage::disk($disk)->putFileAs("recordings/".$path, $file, $fileName,);
            $url = Storage::disk($disk)->url($document);
            $data = [
                'file' => $file,
                'path' => $path,
                'disk' => $disk,
                'extension' => $extension,
                'time' => $time,
                'file_path' => $file_path,
                'fileName' => $fileName,
                'url' => $url
            ];

            Log::channel('mobileApi')->info('uploadRecording: File Details' , ['data' => $data]);


            return $url;
        } catch (\Exception $exception) {
            throw($exception);
            die();
        }
    }

    public function uploadDocumentFromUrl($url, string $path = "", string $disk = "s3")
    {
        try {
            $extension = pathinfo($url, PATHINFO_EXTENSION);

            $time = time().date("YmdHis");
            $randomString = Str::random(10);
            $fileName = time().date("YmdHis").$randomString.".".$extension;
            $fileContents = file_get_contents($url);

            $documentPath = "documents/".(!empty($path) ? str_replace(' ', '-', $path) . "/" : "") . $fileName;
            Storage::disk($disk)->put($documentPath, $fileContents);
            $url = Storage::disk($disk)->url($documentPath);
            return $url;
        } catch (\Exception $exception) {
            return false;
            throw $exception;
        }
    }
    public function uploadSvg($file, string $path = "", string $disk = "s3")
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $time = time().date("YmdHis");
            $file_path = $time;
            $fileName = $time.".".$extension;
            $document = Storage::disk($disk)->put("amenities".(!empty($path) ? str_replace(' ', '-', $path) . "" : ""), $file);
            $url = Storage::disk($disk)->url($document);
            return $url;
        } catch (\Exception $exception) {
            throw($exception);
            die();
        }
    }

    public function copyPhoto($url, $path = "", $prefix = "", string $disk = "s3",)
    {

        try {
            $file = Image::make($url);
            $extension = \File::extension($url);
            $file->encode($extension);
            $fileName = basename($url);
            $imagePath = Storage::disk($disk)->put("images/".(!empty($path) ? $path . "/" : "")."copy_".$fileName, $file);
            $imageUrl = Storage::disk($disk)->url("images/".(!empty($path) ? $path . "/" : "")."copy_".$fileName);
            return $imageUrl;
        }catch (\Exception $exception){
            print_r($exception->getMessage());
            return false;
            die();
        }
    }

    public function duplicateDocument($url, $path = "", $prefix = "", string $disk = "s3",)
    {

        try {
            $file = Image::make($url);
            $extension = \File::extension($url);
            $file->encode($extension);
            $fileName = basename($url);
            $imagePath = Storage::disk($disk)->put("images/".(!empty($path) ? $path . "/" : "")."copy_".$fileName, $file);
            $imageUrl = Storage::disk($disk)->url("images/".(!empty($path) ? $path . "/" : "")."copy_".$fileName);
            return $imageUrl;
        }catch (\Exception $exception){
            print_r($exception->getMessage());
            return false;
            die();
        }
    }
    public function removePhotoFromBucket($media_id){
        $photo_id = $media_id;
        $photo = \App\Models\PropertyMedia::where('id', $photo_id)->first();
        try{
              if($photo){
                $path=parse_url($photo->thumbnail);
                if(Storage::disk('s3')->exists($path['path'])) {
                    Storage::disk('s3')->delete($path['path']);
                }

                $path=parse_url($photo->path);
                if(Storage::disk('s3')->exists($path['path'])) {
                    Storage::disk('s3')->delete($path['path']);
                }
                if($photo->original){
                    $count = \App\Models\PropertyMedia::where('original', $photo->original)->count();
                    if($count == 1){
                        $path=parse_url($photo->original);
                        if(Storage::disk('s3')->exists($path['path'])) {
                            Storage::disk('s3')->delete($path['path']);
                        }
                    }

                }
                return true;
            }
        }catch(\Exception $exception){
            print_r($exception->getMessage());
            return false;
            die();
        }

    }
    public function removeDocumentFromBucket($file_path){

        try{
            if($file_path){
                $path = parse_url($file_path);
                if(Storage::disk('s3')->exists($path['path'])) {
                    Storage::disk('s3')->delete($path['path']);
                }
                return true;
            }
        }catch(\Exception $exception){
            print_r($exception->getMessage());
            return false;
            die();
        }

    }

    public function uploadImageAndThumbWithWatermarkFromURL(
        string $imageUrl,
        int $width = 1200,
        int $height = 900,
        string $path = "",
        string $disk = "s3",
        string $watermark = "assets/images/logo/watermarkv3.png"
    ) {
        try {
            $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);
            $randomString = Str::random(10);
            $randomString2 = Str::random(10);
            $fileName = time() . date("YmdHis") . $randomString . "." . $extension;
            $fileNameNew = time() . date("YmdHis") . $randomString2 . "." . $extension;

            // Download the image from the URL
            $imageContent = file_get_contents($imageUrl);
            if(env('APP_ENV') == 'local'){
                $tesingpath = 'testing/';
            }else{
                $tesingpath = '';
            }

            $original = Image::make($imageContent)->encode($extension)->stream();
            $originalPath = Storage::disk($disk)->put("images/".$tesingpath . (!empty($path) ? $path . "/" : "") . "original_" . $fileName, $original->__toString());
            $originalUrl = Storage::disk($disk)->url("images/".$tesingpath . (!empty($path) ? $path . "/" : "") . "original_" . $fileName, $original->__toString());

            $thumbWidth = ($width / 300) * 75;
            $thumbHeight = ($height / 300) * 75;
            $thumbnail = Image::make($imageContent)->resize($thumbWidth, $thumbHeight)->encode($extension)->stream();
            $thumbnailPath = Storage::disk($disk)->put("images/".$tesingpath . (!empty($path) ? $path . "/" : "") . "thumb_" . $fileName, $thumbnail->__toString());
            $thumbnailUrl = Storage::disk($disk)->url("images/".$tesingpath . (!empty($path) ? $path . "/" : "") . "thumb_" . $fileName);

            if (!empty($watermark)) {
                $watermarkPath = public_path("assets/images/logo/watermarkv3.png");
                $watermarkImage = Image::make($watermarkPath);

                $watermarkWidth = $width * 1; // 50% of the main image width
                $watermarkHeight = $height * 1;
                $watermarkImage1 = $watermarkImage->resize($watermarkWidth, $watermarkHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize(); // Prevent upsizing of the watermark image
                });
                $imageNew = Image::make($imageContent)->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->insert($watermarkImage1, "center")
                ->encode($extension)->stream();
            } else {
                $imageNew = Image::make($imageContent)->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                })->encode($extension)->stream();
            }

            $imagePath = Storage::disk($disk)->put("images/".$tesingpath . (!empty($path) ? $path . "/" : "") . $fileNameNew, $imageNew->__toString());
            $imageUrlNew = Storage::disk($disk)->url("images/".$tesingpath . (!empty($path) ? $path . "/" : "") . $fileNameNew);

            ///create image with old watermark

            // if (!empty($watermark)) {
            //     $watermarkPath = public_path("assets/images/logo/watermarkv2.png");
            //     $watermarkImage = Image::make($watermarkPath);

            //     $watermarkWidth = $width * 0.15; // 50% of the main image width
            //     $watermarkHeight = $height * 0.15; // 50% of the main image height

            //     $watermarkImage1 = $watermarkImage->resize($watermarkWidth, $watermarkHeight, function ($constraint) {
            //         $constraint->aspectRatio();
            //         $constraint->upsize(); // Prevent upsizing of the watermark image
            //     });


            //     $image = Image::make($imageContent)
            //     ->resize($width, $height, function ($constraint) {
            //         $constraint->aspectRatio();
            //     })
            //     ->insert($watermarkImage1, "top-right", 90, 40)
            //     ->encode($extension)
            //     ->stream();
            // } else {
            //     $image = Image::make($imageContent)->resize($width, $height, function ($constraint) {
            //         $constraint->aspectRatio();
            //     })->encode($extension)->stream();
            // }

            // $imagePath = Storage::disk($disk)->put("images/".$tesingpath . (!empty($path) ? $path . "/" : "") . $fileName, $image->__toString());
            // $imageUrl = Storage::disk($disk)->url("images/".$tesingpath . (!empty($path) ? $path . "/" : "") . $fileName);



            return [
                "image" => $imageUrlNew,
                "image_v3" => $imageUrlNew,
                "thumbnail" => $thumbnailUrl,
                "original" => $originalUrl
            ];
        } catch (\Exception $exception) {
            print_r($exception->getMessage());
            die();
        }
    }

    public function uploadThumb($file, string $path = "", string $disk = "s3") {
        try {
            $thumbSize = 200;
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . date("YmdHis") . "." . $extension;

            $image = Image::make($file->getRealPath());

            // Crop and resize the image to a square of 200x200
            $image->fit($thumbSize, $thumbSize);

            // Save the thumbnail and the original image to the storage disk
            Storage::disk($disk)->put("images/" . (!empty($path) ? $path . "/" : "") . "thumb_" . $fileName, $image->encode($extension)->__toString());
            $thumbnailUrl = Storage::disk($disk)->url("images/" . (!empty($path) ? $path . "/" : "") . "thumb_" . $fileName);

            return [
                "thumbnail" => $thumbnailUrl,
            ];
        } catch (\Exception $exception) {
            print_r($exception->getMessage());
            die();
        }
    }

    public function copyFileFromUrlToFolder($url, $name, $folderName, $disk = "s3"){
        try{
            if(empty($name)){
                $name = pathinfo($url, PATHINFO_FILENAME);
            }

            $dir = storage_path("app/public/");
            File::ensureDirectoryExists($dir . $folderName);
            $file = Http::get($url)->body();
            $extension = pathinfo($url, PATHINFO_EXTENSION);
            $fileName = $name. ".". $extension;
            $path = $folderName . "/" . $fileName;
            if(!Storage::disk($disk)->exists("public/".$path)){
                Storage::disk($disk)->put("public/".$path, $file);
            }

            return [
                "path" => $path,
                "extension" => $extension,
                "filename" => $fileName,
                "size" => Storage::disk($disk)->size("public/".$path)
            ];
        }catch(Exception $exception){
            print_r($exception->getMessage());
            return null;
        }
    }

    public function makeZipFromSignleFolder($folderName, $folderPrefix = "listings") {
        try {
            File::ensureDirectoryExists(public_path("archive"));
            $zipName = "archive/". $folderName . ".zip";
            $zip = new ZipArchive;

            if(($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE)) === true){
                foreach(Storage::allFiles("file-manager/" . (!empty($folderPrefix) ? $folderPrefix . "/" : "") . $folderName) as $file){
                    $name = basename($file);
                    $zip->addFile(storage_path("app/" . $file), $name);
                }
                $zip->close();
            }
            return public_path($zipName);
        } catch (Exception $exception) {
            return null;
        }
    }

    function makeZipFromFolder($folderPath, $fileName = "", $folderPrefix = ""){

        try{
            File::ensureDirectoryExists(storage_path("app/public/". $folderPath));
            $zipName = (!empty($fileName) ? $fileName : time()) . ".zip";
            $zip = new ZipArchive();
            if (($zip->open(storage_path("app/public/" . $folderPath) . $zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE)) === true) {
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator(storage_path("app/public/" . (!empty($folderPrefix) ? $folderPrefix . "/" : "") . $folderPath)),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $file) {
                    $filePath = $file->getRealPath();

                    if ($file->isDir() || !$filePath) {
                        continue;
                    }
                    $relativePath = substr($filePath, strlen(storage_path("app/public/" . (!empty($folderPrefix) ? $folderPrefix . "/" : "") . $folderPath)));

                    $zip->addFile($filePath, $relativePath);
                }
                $zip->close();
            }

            return $folderPath . "" . $zipName;
        }catch(Exception $exception){
            print_r($exception->getMessage());die;
            return null;
        }
    }

    public function uploadFileToFileManagerFolder($file, string $folderPath, string $disk = "s3")
    {
        try {
            $document = Storage::disk($disk)->put((!empty($folderPath) ? str_replace(' ', '-', $folderPath) . "" : "file-manager"), is_string($file) ? file_get_contents($file) : $file);
            $url = Storage::disk($disk)->url($document);
            $size = Storage::disk($disk)->size($document);
            return [
                "path" => $url,
                "disk" => $disk,
                "size" => $size,
            ];
        } catch (\Exception $exception) {
            return null;
        }
    }

    function makeZipFromUrls(array $urls, string $path, string $fileName = "", string $disk = "s3"){

        try{
            $zipName = (!empty($fileName) ? $fileName : time()) . ".zip";
            $zip = new ZipArchive();
            if (($zip->open($zipName, ZipArchive::CREATE | ZipArchive::OVERWRITE)) === true) {

                foreach ($urls as $url) {
                    $fileName = basename($url);
                    $zip->addFromString($fileName, file_get_contents($url));
                }
                $zip->close();
            }
            $path .= "/".$zipName;
            Storage::disk($disk)->put($path, file_get_contents($zipName));
            $url = Storage::disk($disk)->url($path);
            $size = Storage::disk($disk)->size($path);
            unlink($zipName);
        return [
            "path" => $url,
            "disk" => $disk,
            "size" => $size
        ];
        }catch(Exception $exception){
            return null;
        }
    }

    public function uploadDocumentAndThumb($file, string $path = "", string $disk = "s3")
    {

        try {
            $extension = $file->getClientOriginalExtension();
            $time = time().date("YmdHis");
            $file_path = $time;
            $fileName = $time.".".$extension;
            $document = Storage::disk($disk)->put("documents/".(!empty($path) ? str_replace(' ', '-', $path) . "" : ""), $file);
            $url = Storage::disk($disk)->url($document);
            $pdf = new \Spatie\PdfToImage\Pdf($file->getRealPath());
            $pdf->saveImage("documents/".(!empty($path) ? str_replace(' ', '-', $path) . "" : ""). "thumb_" . $fileName);
            return [
                "path" => $url,
                "thumbnail" => $pdf,
            ];
        } catch (\Exception $exception) {
            print_r($exception->getMessage());
            die();
        }
    }
    public function isFileUrl($url) {
        $headers = get_headers($url, 1);
        if (isset($headers['Content-Type'])) {
            $type = is_array($headers['Content-Type']) ? $headers['Content-Type'][0] : $headers['Content-Type'];
            // Check if the content-type of the URL is not a webpage (text/html or application/xml)
            if ($type != 'text/html' && $type != 'application/xml') {
                return true;
            }
        }
        return false;
    }
    public function copyDocument($url, $path, $prefix = "", string $disk = "s3",)
    {
        if(!$this->isFileUrl($url)){
            return false;
        }
        try{
            $file = Http::get($url)->body();
            $extension = pathinfo($url, PATHINFO_EXTENSION);
            $name = basename($url);

            $fileName = $name;
            $path = (!empty($path) ? str_replace(' ', '-', $path) . "" : "") . "/" . $fileName;
            Storage::disk($disk)->put("documents/".$path, $file);
            return Storage::disk($disk)->url("documents/".$path);

        }catch(Exception $exception){
            print_r($exception->getMessage());
            return false;
        }
    }




    public function removeFile($path, $disk = "s3") {


        try {
            $filePath = parse_url($path);
            if(Storage::disk($disk)->exists($filePath["path"])){
                return Storage::disk($disk)->delete($filePath["path"]);
            }
            return false;
        } catch (Exception $exception) {
            return false;
        }
    }

    public function uploadImageAndThumbWithWatermarkFromURLV2(
        string $imageUrl,
        int $width = 1200,
        int $height = 900,
        string $path = "",
        string $disk = "s3",
        string $watermark = "assets/images/logo/watermarkv3.png"
    ) {
        try {
            $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);
            $randomString = Str::random(10);
            $fileName = time() . date("YmdHis") . $randomString . "." . $extension;

            // Download the image from the URL
            $imageContent = file_get_contents($imageUrl);
            if(env('APP_ENV') == 'local'){
                $tesingpath = 'testing/';
            }else{
                $tesingpath = '';
            }

            $original = Image::make($imageContent)->encode($extension)->stream();

            if (!empty($watermark)) {
                $watermarkPath = public_path($watermark);
                $watermarkImage = Image::make($watermarkPath);
                // Calculate the desired watermark size based on the main image size
                $watermarkWidth = $width * 1; // 100% of the main image width
                $watermarkHeight = $height * 1; // 100% of the main image height
                // Resize the watermark image while maintaining its resolution
                $watermarkImage1 = $watermarkImage->resize($watermarkWidth, $watermarkHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize(); // Prevent upsizing of the watermark image
                });
                $image = Image::make($imageContent)
                    ->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->insert($watermarkImage1, "center")
                    ->encode($extension)
                    ->stream();
            } else {
                $image = Image::make($imageContent)->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                })->encode($extension)->stream();
            }

            $imagePath = Storage::disk($disk)->put("images/".$tesingpath . (!empty($path) ? $path . "/" : "") . $fileName, $image->__toString());
            $imageUrl = Storage::disk($disk)->url("images/".$tesingpath . (!empty($path) ? $path . "/" : "") . $fileName);

            return [
                "image" => $imageUrl

            ];
        } catch (\Exception $exception) {
            print_r($exception->getMessage());
            die();
        }
    }
    public function uploadImageAndThumbWithWatermarkFromURLV2Copy(
        string $imageUrl,
        int $width = 1200,
        int $height = 900,
        string $path = "",
        string $disk = "s3",
        string $watermark = "assets/images/logo/watermarkv3.png"
    ) {
        //by khagesh on 2021-06-26
        //this function is copied from above code and is used for data copying with watermark in queue
        //it can later be deleted if not needed after data copying is completed
        try {
            $extension = pathinfo($imageUrl, PATHINFO_EXTENSION);
            $randomString = Str::random(10);
            $fileName = time() . date("YmdHis") . $randomString . "." . $extension;


            if(!$this->isFileUrl($imageUrl)){
                return [
                    "image" => ''
                ];
            }
            // Download the image from the URL
            $imageContent = @file_get_contents($imageUrl);

            if ($imageContent === false) {
                return [
                    "image" => ''
                ];
            }else{
            if(env('APP_ENV') == 'local'){
                $tesingpath = 'testing/';
            }else{
                $tesingpath = '';
            }

            $original = Image::make($imageContent)->encode($extension)->stream();

            if (!empty($watermark)) {
                $watermarkPath = public_path($watermark);
                $watermarkImage = Image::make($watermarkPath);
                // Calculate the desired watermark size based on the main image size
                $watermarkWidth = $width * 1; // 100% of the main image width
                $watermarkHeight = $height * 1; // 100% of the main image height
                // Resize the watermark image while maintaining its resolution
                $watermarkImage1 = $watermarkImage->resize($watermarkWidth, $watermarkHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize(); // Prevent upsizing of the watermark image
                });
                $image = Image::make($imageContent)
                    ->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->insert($watermarkImage1, "center")
                    ->encode($extension)
                    ->stream();
            } else {
                $image = Image::make($imageContent)->resize($width, $height, function ($constraint) {
                    $constraint->aspectRatio();
                })->encode($extension)->stream();
            }

            $imagePath = Storage::disk($disk)->put("images/".$tesingpath . (!empty($path) ? $path . "/" : "") . $fileName, $image->__toString());
            $imageUrl = Storage::disk($disk)->url("images/".$tesingpath . (!empty($path) ? $path . "/" : "") . $fileName);

            return [
                "image" => $imageUrl
            ];
            }
        } catch (\Exception $exception) {
            Log::info("failed to copy and watermark image for property: ".$exception->getMessage());
            return [
                "image" => ''
            ];
            print_r($exception->getMessage());
            die();
        }
    }


    public function uploadAudioAsMp3FromUrl($url, string $path = '', string $disk = 's3') : ?string
    {
        // This function downloads an audio file from a given URL and saves it as an MP3 file in the specified path on the given disk.
        // It returns the URL of the uploaded MP3 file or null if the upload fails.
        if(!$this->isFileUrl($url)){
            return null;
        }

        try {
            $randomString = Str::random(10);
            $time = time().date("YmdHis");

            // Download file
            $fileContents = @file_get_contents($url);
            if ($fileContents === false) {
                Log::error("Failed to download audio file from URL: {$url}");
                return null;
            }

            // Force .mp3 extension regardless of original format
            $fileName = $time . $randomString . ".mp3";
            $documentPath = "audios/" . ($path ? str_replace(' ', '-', $path) . "/" : "") . $fileName;

            Storage::disk($disk)->put($documentPath, $fileContents);
            $fileUrl = Storage::disk($disk)->url($documentPath);

            return $fileUrl;

        } catch (\Exception $exception) {
            Log::error("Failed to upload audio from URL: " . $exception->getMessage());
            return null;
        }
    }

}
