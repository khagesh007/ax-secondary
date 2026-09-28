<?php

use App\Models\Community;
use App\Models\Deal;
use App\Models\Folder;
use App\Models\Lead;
use App\Models\Property;
use App\Models\Region;
use App\Models\SubCommunity;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (!function_exists('generateFolderSlug')) {

    function generateFolderSlug($name, $folderId = null, $increaseBy = 0)
    {

        $slug = Str::slug($name);
        if ($increaseBy > 0) {
            $slug .= "-" . $increaseBy;
        }

        $exists = Folder::where("slug", $slug)->when(!empty($folderId), fn ($query) => $query->where("id", "!=", $folderId))->exists();
        if ($exists) {
            return generateFolderSlug($name, $folderId, ++$increaseBy);
        }
        return $slug;
    }
}

if (!function_exists('getFolderChildrenIdsByID')) {
    function getFolderChildrenIdsByID($folderId, $includeFolderId = true)
    {

        $folder = Folder::find($folderId);
        $arr = [];
        if ($includeFolderId && !empty($folderId)) {
            $arr[] = $folderId;
        }
        renderFolderChildren($folder, $arr);
        return Folder::whereIn("id", $arr)->pluck("id")->all();
    }
}

if (!function_exists('renderFolderChildren')) {
    function renderFolderChildren($folder, &$arr = [])
    {
        if ($folder != null) {
            if ($folder->children->isNotEmpty()) {
                foreach ($folder->children ?? [] as $child) {
                    if (!in_array($child->id, $arr)) {
                        $arr[] = $child->id;
                        $arr = renderFolderChildren($child, $arr);
                    }
                }
            }
        }
        return $arr;
    }
}

if (!function_exists('getParentFoldersByID')) {
    function getParentFoldersByID($folderId, $includeFolderId = true)
    {

        $folder = Folder::find($folderId);
        $arr = [];
        if ($includeFolderId && !empty($folderId)) {
            $arr[] = [
                "id" => $folder->id,
                "name" => $folder->name,
                "slug" => $folder->slug,
            ];
        }
        renderFolderParent($folder, $arr);
        return $arr;
    }
}

if (!function_exists('renderFolderParent')) {
    function renderFolderParent($folder, &$arr = [])
    {
        if ($folder != null) {
            if (!is_null($folder->parent)) {
                if (!in_array($folder->parent->id, array_column($arr, "id"))) {
                    $arr[] = [
                        "id" => $folder->parent->id,
                        "name" => $folder->parent->name,
                        "slug" => $folder->parent->slug,
                    ];
                    $arr = renderFolderParent($folder->parent, $arr);
                }
            }
        }
        return $arr;
    }
}


if (!function_exists('createPropertyFolder')) {
    function createPropertyFolder($property)
    {
        if (is_null($property)) return false;
        try {
            $property->load([
                "subCommunity:id,name,slug,community_id",
                "subCommunity.community:id,name,slug,region_id",
                "subCommunity.community.region:id,name"
            ]);

            $folderPath = "file-manager/";

            $parentId = null;

            if (!is_null($property?->subCommunity?->community?->region)) {
                $name = $property->subCommunity->community->region->name;
                $slug = \Illuminate\Support\Str::slug($property->subCommunity->community->region->name);
                $folderPath .= $slug . "/";
                $folder = Folder::where("slug", $slug)->first();

                if (is_null($folder)) {
                    $folder = Folder::create([
                        "name" => $name,
                        "slug" => $slug,
                        "path" => rtrim($folderPath, "/"),
                        "parent_id" => $parentId,
                        "model_type" => Region::class,
                        "model_id" => $property->subCommunity->community->region->id,
                    ]);
                }
                $folder->parent_id = $parentId;
                $folder->save();
                $parentId = $folder->id;
            }

            if (!is_null($property?->subCommunity?->community)) {
                $name = $property->subCommunity->community->name;
                $slug = $property->subCommunity->community->slug;
                $folderPath .= $slug . "/";
                $folder = Folder::where("slug", $slug)->first();

                if (is_null($folder)) {
                    $folder = Folder::create([
                        "name" => $name,
                        "slug" => $slug,
                        "path" => rtrim($folderPath, "/"),
                        "parent_id" => $parentId,
                        "model_type" => Community::class,
                        "model_id" => $property->subCommunity->community->id,
                    ]);
                }
                $folder->parent_id = $parentId;
                $folder->save();
                $parentId = $folder->id;
            }
            if (!is_null($property?->subCommunity)) {
                $name = $property->subCommunity->name;
                $slug = $property->subCommunity->slug;
                $folderPath .= $slug . "/";
                $folder = Folder::where("slug", $slug)->first();

                if (is_null($folder)) {
                    $folder = Folder::create([
                        "name" => $name,
                        "slug" => $slug,
                        "path" => rtrim($folderPath, "/"),
                        "parent_id" => $parentId,
                        "model_type" => SubCommunity::class,
                        "model_id" => $property->subCommunity->id,
                    ]);
                }
                $folder->parent_id = $parentId;
                $folder->save();
                $parentId = $folder->id;
            }

            $name = $property->ref_no;
            $slug = \Illuminate\Support\Str::slug($property->ref_no);
            $folderPath .= $slug . "/";
            $folder = Folder::where("slug", $slug)->first();
            if (is_null($folder)) {
                $folder = Folder::create([
                    "name" => $name,
                    "slug" => $slug,
                    "path" => rtrim($folderPath, "/"),
                    "parent_id" => $parentId,
                    "model_type" => Property::class,
                    "model_id" => $property->id,
                ]);
            }
            $folder->parent_id = $parentId;
            $folder->save();
            return [
                "id" => $folder->id,
                "name" => $folder->name,
                "slug" => $folder->slug,
                "folder_path" => rtrim($folderPath, "/"),
            ];
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('createDealFolder')) {
    function createDealFolder()
    {
        try {

            $dir = storage_path("app/public/file-manager/deals/");
            $folderPath = "file-manager/deals/";

            //$name = "Created by " . auth()->user()->name;
            $name = "Created by ";
            $slug = \Illuminate\Support\Str::slug($name);;
            $dir .= $slug . "/";
            $folderPath .= $slug . "/";
            $folder = Folder::where("slug", $slug)->first();
            if (is_null($folder)) {
                $folder = Folder::create([
                    "name" => $name,
                    "slug" => $slug,
                    "parent_id" => null,
                    "model_type" => Deal::class,
                    "model_id" => null,
                ]);
            }
            $folder->save();
            File::ensureDirectoryExists($dir);
            return [
                "id" => $folder->id,
                "folder_path" => $folderPath
            ];
        } catch (Exception $e) {

            return false;
        }
    }
}

if (!function_exists('getFolderMediaTypeByExtension')) {
    function getFolderMediaTypeByExtension($extension)
    {
        if ($extension == "jpg" || $extension == "png" || $extension == "gif") {
            return "Photos";
        }

        if ($extension == "pdf" || $extension == "doc" || $extension == "docx") {
            return "Document";
        }

        return "Other";
    }
}

if (!function_exists('convertAndFormatFileSize')) {
    function convertAndFormatFileSize($kilobyte, $precision = 2)
    {

        $bytes = $kilobyte * 1024;

        $units = array('B', 'KB', 'MB', 'GB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1000));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1000, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}


if (!function_exists('getFileTypeByExtension')) {
    function getFileTypeByExtension($extension)
    {

        if ($extension == "jpg" || $extension == "jpeg" || $extension == "png" || $extension == "gif" || $extension == "bmp" || $extension == "webp") {
            return "image";
        }

        if ($extension == "pdf") {
            return "pdf";
        }

        if ($extension == "doc" || $extension == "docx") {
            return "doc";
        }

        return null;
    }
}

if (!function_exists('createLeadFolder')) {
    function createLeadFolder($leadId)
    {
        try {


            $folderPath = "file-manager/leads/";

            $parentId = null;

            $name = "test-file-name";
            $slug = \Illuminate\Support\Str::slug($name);
            $folderPath .= $slug . "/";
            $folder = Folder::where("slug", $slug)->first();
            if (is_null($folder)) {
                $folder = Folder::create([
                    "name" => $name,
                    "slug" => $slug,
                    "path" => rtrim($folderPath, "/"),
                    "parent_id" => $parentId,
                    "model_type" => Lead::class,
                    "model_id" => $leadId,
                ]);
            }
            $folder->parent_id = $parentId;
            $folder->save();
            return [
                "id" => $folder->id,
                "name" => $folder->name,
                "slug" => $folder->slug,
                "folder_path" => rtrim($folderPath, "/"),
            ];
        } catch (Exception $e) {
            return false;
        }
    }
}
