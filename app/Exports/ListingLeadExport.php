<?php

namespace App\Exports;

use App\Models\ListingLead;
use App\Traits\FiltersTrait;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\User;


class ListingLeadExport implements FromCollection, WithHeadings, WithMapping
{
    use FiltersTrait;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct(private $columns,  public $request)
    {

    }

    public function collection()
    {
        $q = ListingLead::query();
        return $this->applyLeadPoolFilters($q, $this->request)->with(['source', 'medium', 'status', 'agent', 'type', 'bed', 'bath', 'updatedBy'])->get();
    }
    public function map($property): array
    {
        $columns = [];
        foreach ($this->columns ?? [] as $column){
            if ($column == "community"){
                $columns[] = $property->community->name ?? "";
            }elseif ($column == "sub_community"){
                $columns[] = $property->subCommunity->name ?? "";
            }elseif ($column == "community_property"){
                $columns[] = $property->communityProperty->name ?? "";
            }elseif ($column == "property_category"){
                $columns[] = $property->communityProperty->name ?? "";
            }elseif ($column == "bed"){
                $columns[] = $property->bed->name ?? "";
            }elseif ($column == "bath"){
                $columns[] = $property->bath->name ?? "";
            }elseif ($column == "agent"){
                $columns[] = $property->agent->name ?? "";
            }elseif ($column == "updated_by"){
                $updatedBy = User::where('id',$property->updated_by_id)->first();
                $columns[] = $updatedBy->full_name ?? "";
            }
            else{
                $columns[] = $property->$column ?? "";
            }
        }
        return $columns;

    }

    public function headings(): array
    {
        $allColumns = getListingLeadsExportColumns();
        $columns = [];
        foreach ($this->columns ?? [] as $column) {
            if (!empty($allColumns[$column])) {
                $columns[] = $allColumns[$column];
            }
        }
        return $columns;
    }
}
