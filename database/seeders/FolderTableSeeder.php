<?php

namespace Database\Seeders;

use App\Models\Folder;
use App\Models\FolderMedia;
use App\Traits\FileTrait;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FolderTableSeeder extends Seeder
{
    use FileTrait;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $folder = Folder::updateOrCreate([
            "slug" => "document-templates",
        ], [
            "name" => "Document Templates",
            "folder_order" => 1,
        ]);

        $folderMedia = [
            [
                "folder_id" => $folder->id,
                "title" => "Agent to Agent Agreement Form.pdf",
                "slug" => "a2a-agreement-form",
                "description" => "Agent to Agent Agreement Form",
                "path" => null,
                "disk" => "s3",
                "order" => 1,
                "status" => true,
                "extension" => "pdf",
                "size" => 765,
            ],
            [
                "folder_id" => $folder->id,
                "title" => "Contract Between Buyer & Broker Form B.pdf",
                "slug" => "contract-between-buyer-and-broker-form-b",
                "description" => "Contract Between Buyer & Broker Form B",
                "path" => null,
                "disk" => "s3",
                "order" => 2,
                "status" => true,
                "extension" => "pdf",
                "size" => 1229,
            ],
            [
                "folder_id" => $folder->id,
                "title" => "Tenancy Form Dubai.pdf",
                "slug" => "tenancy-form-dubai",
                "description" => "Tenancy form dubai",
                "path" => null,
                "disk" => "s3",
                "order" => 3,
                "status" => true,
                "extension" => "pdf",
                "size" => 337,
            ],
            [
                "folder_id" => $folder->id,
                "title" => "Marketing Form A - Leasing Fillable.pdf",
                "slug" => "marketing-form-a-leasing-fillable",
                "description" => "Marketing Form A - Leasing Fillable",
                "path" => null,
                "disk" => "s3",
                "order" => 4,
                "status" => true,
                "extension" => "pdf",
                "size" => 1127,
            ],
        ];


        $path = "file-manager/" . $folder["slug"];

        foreach ($folderMedia as $media) {
            $sampleFileName = public_path("document-templates/".$media["slug"]."-sample.pdf");

            $path .= "/".$media["slug"].".pdf";

            Storage::disk($media["disk"])->put($path, file_get_contents($sampleFileName));
            $url = Storage::disk($media["disk"])->url($path);

            $extension = pathinfo($url, PATHINFO_EXTENSION);
            $media["path"] = $url;
            $media["type"] = getFolderMediaTypeByExtension($extension);
            $media["extension"] = $extension;
            $media["size"] =  Storage::disk($media["disk"])->size($path) >> 10;
            $media["data"] = null;
            $media["data_editable"] = false;
            FolderMedia::updateOrCreate([
                "slug" => $media["slug"]
            ], $media);
        }
    }
}
