<?php

namespace App\Http\Controllers\Admin\Offplan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\ExcelPublishPropertyJob;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PropertyImport;


class PropertyImportController extends Controller
{
    public function index()
    {
        return view('admin.offplan.excel.excel_import');
    }

    public function handle(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx',
        ]);
        // dd($request->file('file'));
        if ($request->hasFile('file')) {
            try {
                $excelFile = $request->file('file');
                Excel::import(new PropertyImport(), $request->file('file'));

                // $path = $excelFile->store('temp'); // Change 'temp' to your desired storage path
                // ExcelPublishPropertyJob::dispatch($excelFile);
                return redirect()->back()->with('success', 'File imported successfully!');
            } catch (\Exception $e) {
                dd($e);
                return redirect()->back()->with('error', 'Error importing file: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'No file uploaded.');
    }
}
