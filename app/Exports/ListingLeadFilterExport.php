<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\ListingLead;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ListingLeadFilterExport implements  FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $leads = ListingLead::where('source_id',129)->where('created_at','>=',now()->subMonths(2))->orderBy('id','desc')->get();

        return $leads->map(function ($lead) {
            return [
                'ID' => $lead->id,
                'Agent' => $lead->agent_id ? $lead->agent->full_name : '',
                'First Name' => $lead->first_name,
                'Last Name' => $lead->last_name,
                'Mobile' => $lead->mobile,
                'Email' => $lead->email,
                'Status' => $lead?->status?->name,
                'Source' => $lead?->source?->name,
                'Note' => $lead->note,
                'created_at' => date('d-m-Y',strtotime($lead->created_at)),
            ];
        });
    }
    public function headings(): array
    {
        return [
            'ID',
            'Agent',
            'First Name',
            'Last Name',
            'Mobile',
            'Email',
            'Status',
            'Source',
            'Note',
            'Created at'
        ];
    }
}
