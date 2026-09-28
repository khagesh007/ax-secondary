<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BitrixLead implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $leads = Lead::where('status', 1)->where('lead_status_id',9)->where('source_id',110)->get();

        return $leads->map(function ($lead) {
            return [
                'ID' => $lead->id,
                'Bitrix ID' => $lead->bitrix_id,
                'Agent' => $lead->agent_id ? $lead->agent->full_name : '',
                'First Name' => $lead->first_name,
                'Last Name' => $lead->first_name,
                'Mobile' => $lead->mobile,
                'Email' => $lead->email,
                'Status' => $lead->leadStatus->name,
                'Source' => $lead->leadSource->name,
                'Medium' => $lead->leadMedium->name,
                'Type' => $lead->leadType->name,
                'Note' => $lead->note,
                'Message' => $lead->message,


            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Bitrix ID',
            'Agent',
            'First Name',
            'Last Name',
            'Mobile',
            'Email',
            'Status',
            'Source',
            'Medium',
            'Type',
            'Note',
            'Message',
        ];
    }
}
