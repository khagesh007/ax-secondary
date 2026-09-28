<?php
namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeadsNotClickedExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Lead::with('agent') // Eager load the agent relationship
            ->where('source_id', 31)
            ->whereNotNull('agent_id')
            ->where('lead_medium_id', 4)
            ->whereBetween('created_at', ['2024-09-17 00:00:00', '2024-09-25 00:00:00'])
            ->whereHas('shortlink', function ($query) {
                $query->whereNull('opted_in');
            })
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Agent',
            'Manager',
            'Name',
            'Email',
            'Mobile',
            'Source',
            'Medium',
            'Status',
            'Updated At',
            'Created At',

            'shortlink_id',
            'shortlink',
            'created_at',
            'updated_at',
            'opted_in',
            'count',

        ];
    }

    public function map($lead): array
    {
        return [
            $lead->id,
            $lead->agent ? $lead->agent->full_name : 'N/A',
            $lead->agent ? $lead->agent->parent->full_name : 'N/A',
            $lead->first_name,
            $lead->email,
            $lead->mobile,
            $lead->leadSource->name,
            $lead->leadMedium->name,
            $lead->leadStatus->name,
            $lead->updated_at,
            $lead->created_at,
            $lead->shortlink->id,
            $lead->shortlink->short_link,
            $lead->shortlink->created_at,
            $lead->shortlink->updated_at,
            $lead->shortlink->opted_in,
            $lead->shortlink->count,

        ];
    }
}
