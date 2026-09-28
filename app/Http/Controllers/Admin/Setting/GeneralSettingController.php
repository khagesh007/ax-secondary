<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;

class GeneralSettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.general.index');
    }
    public function store(Request $request)
    {
        $request->validate([
            'slug' => 'required',
            'value' => 'required',
            'module' => 'required',
            'key' => 'required',
        ]);
        $slug = strtolower($request->module).'-'.$request->key;

        $generalSetting = GeneralSetting::updateOrCreate(['module' => $request->module, 'slug' => $slug, 'key' => $request->key], ['value' => $request->value]);
        return redirect()->route('admin.settings.general.index')->with('success', 'General Setting created successfully.');
    }

    public function getSettings(Request $request)
    {
        $generalSetting = GeneralSetting::where('module', $request->module)->get();
        return view('admin.settings.general.partials._tab')
            ->with('keys', $generalSetting)
            ->with('module', $request->module)
            ->with('slug', strtolower($request->module));
    }
    public function delete(Request $request)
    {
       if(GeneralSetting::where('id', $request->id)->delete()){
           return response()->json(['status' => '1','message' => 'General Setting deleted successfully.']);
       }else{
              return response()->json(['status' => '0','message' => 'General Setting not deleted successfully.']);
       }
    }
}
