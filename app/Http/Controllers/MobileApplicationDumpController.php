<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\MobileApplicationDump;

class MobileApplicationDumpController extends Controller
{

    // Store a new dump
    public function store(Request $request)
    {
        abort_if($request->api_token != "fYlQO814toCEdmdp", 404);
        $request->validate([
            'payload' => 'nullable',
            'response' => 'nullable',
            'title'    => 'nullable'
        ]);

        $dump = MobileApplicationDump::create([
            'payload' => $request->input('payload'),
            'response' => $request->input('response'),
            'title' => $request->input('title')
        ]);

        return response()->json($dump, 200);
    }
    
}
