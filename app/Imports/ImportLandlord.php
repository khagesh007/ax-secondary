<?php

namespace App\Imports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportLandlord implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        try{
        if ($row[0] != '' && $row[0] != 'Id') {
            $title = trim($row[1]);
            return new Contact([
                'my_crm_id' => $row[0],
                'title' => $title!='' ? ucfirst($title) : null,
                'first_name' => $row[2],
                'last_name' => $row[3].' '.$row[4],
                'dob' => date('Y-m-d', strtotime($row[5])) ?? null,
                'email' => $row[6],
                'mobile' => $row[7],
                'address' => $row[8],
                'passport' => $row[9],
                'passport_expiry' => date('Y-m-d',strtotime($row[10])),
                'contact_type_id' => '3',
                
            ]);
        }
    }catch(\Exception $e){
        return response()->json(['status'=>'0','error' => $e->getMessage()], 500);
      
    }
}

}
