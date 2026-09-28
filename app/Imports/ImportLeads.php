<?php

namespace App\Imports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportLeads implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {


        if ($row[0] != '' && $row[0] != 'Id') {
            if ($row[15] == 'notYetContacted'){
                $sub_status = '4';
            } elseif ($row[15] == 'prospect') {
                $sub_status = '26';
            }elseif ($row[15] == 'calledNoReply') {
                $sub_status = '5';
            }
            if ($row[16] == 'propertyfinder'){
                $source = '31';
            } elseif ($row[16] == 'online') {
                $source = '45';
            }elseif ($row[16] == 'other') {
                $source = '44';
            }
            return new Lead([
                'my_crm_id' => $row[0],
                'first_name' => $row[2],
                'last_name' => $row[3].' '.$row[4],
                'email' => $row[5],
                'phone' => $row[6],
                'mobile' => $row[7],
                'address' => $row[8],
                // 'lead_sub_status_id ' => $sub_status ?? null,
                'source_id  ' => $source ?? null,
                'created_at ' => $row[21] ?? null,

            ]);
        }

    }
}
