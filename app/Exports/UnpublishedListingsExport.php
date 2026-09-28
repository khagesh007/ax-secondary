<?php
namespace App\Exports;

use App\Models\Property;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UnpublishedListingsExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        $threeMonthsAgo = Carbon::now()->subMonths(3);

        return Property::query()
            // ->select('id', 'ref_no', 'publish_status', 'admin_unpublish_reason','location','agent')
            ->whereHas('propertycycles', function ($query) use ($threeMonthsAgo) {
                $query->where('status', 'UNPUBLISHED')->where('created_at', '>=', $threeMonthsAgo);
            });
    }

    public function headings(): array
    {
        return [
            'Property ID',
            'Reference Number',
            'Title',
            'Agent',
            'Location',
            'Status',
            'Reason for Unpublishing',
            'Last Update',
            'Last Updated By',
        ];
    }

    public function map($property): array
    {
        return [
            $property->id,
            $property->ref_no,
            $property->title,
            $property?->agent?->full_name??'-',
            $property?->location??'-',
            $property->publish_status,
            $property->admin_unpublish_reason ?? 'N/A',
            date('d-m-y, h:i',strtotime($property->updated_at)) ?? 'N/A',
            $property?->updatedBy?->full_name
        ];
    }
}
