<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class DealCommissionExportExcel implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct(public $data)
    {
        $this->data = $data;
    }
//    public function array(): array
//    {
//        $data = [
//            ['Header 1', 'Header 2'],
//            ['Data 1', 'Data 2'],
//            // Add more data as needed
//        ];
//        return $data;
//       // return $this->data;
//    }
    public function view(): View
    {

        return view('excel.dealReport', [
            'data' => $this->data
        ]);
    }
}
