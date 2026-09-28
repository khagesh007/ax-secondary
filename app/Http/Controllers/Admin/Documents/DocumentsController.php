<?php

namespace App\Http\Controllers\Admin\Documents;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Traits\FileTrait;
class DocumentsController extends Controller
{
    use FileTrait;

    public function index()
    {

        return view('admin.documents.index');
    }

    public function show(Folder $folder) {

        return view('admin.documents.index', compact('folder'));
    }

}
