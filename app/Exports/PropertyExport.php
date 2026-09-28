<?php

namespace App\Exports;

use App\Models\Property;
use App\Models\User;
use App\Models\PropertyNote;
use App\Traits\FiltersTrait;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PropertyExport implements FromCollection, WithHeadings, WithMapping
{
    use FiltersTrait;

    public function __construct(private $columns,  public $request)
    {

    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $q = Property::query();
      return $this->applyPropertyFilters($q, $this->request)->with(["community", "subCommunity", "communityProperty", "bed", "bath", "agent", "owner", "price", "portals.portal",'owner'])->get();
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
                $columns[] = $property->propertyCategory->title ?? "";
            }elseif ($column == "bed"){
                $columns[] = $property->bed->name ?? "";
            }elseif ($column == "bath"){
                $columns[] = $property->bath->name ?? "";
            }elseif ($column == "furnished"){
                $columns[] = Property::FURNISHED_SELECT[$property->furnished] ?? "";
            }elseif ($column == "featured"){
                $columns[] = $property->featured ? "Yes" : "No";
            }elseif ($column == "exclusive"){
                $columns[] = $property->exclusive ? "Yes" : "No";
            }elseif ($column == "publish_status"){
                $columns[] = Property::PUBLISH_STATUS_SELECT[$property->publish_status] ?? "";
            }elseif ($column == "agent"){
                $columns[] = $property->agent->name ?? "";
            }
            elseif ($column == "manager"){
                $columns[] = $property->agent?->parent?->full_name ?? "";
            }
            elseif ($column == "completion_status"){
                $columns[] = $property->completion_status ?? "";
            }elseif ($column == "contactNo"){
                $columns[] = $property->owner->mobile ?? "";
            }elseif ($column == "contractNo"){
                $columns[] = $property->contract_number ?? "";
            }elseif ($column == "owner"){
                $columns[] = ($property->owner->first_name ?? "") . " ". ($property->owner->last_name ?? "");
            }elseif ($column == "price"){
                $columns[] = $property->price->price ?? "";
            }elseif ($column == "recentNote"){
                $latestNote = PropertyNote::where('property_id', $property->id)->latest()->first();
                $columns[] = $latestNote->note ?? "";
            }elseif ($column == "updated_by"){
                $updatedBy = User::where('id',$property->updated_by_id)->first();
                $columns[] = $updatedBy->full_name ?? "";
            }elseif ($column == "created_by"){
                $createdBy = User::where('id',$property->created_by_id)->first();
                $columns[] = $createdBy->full_name ?? "";
            }
            elseif ($column == "created_at"){
                $columns[] = isset($property->created_at) ? date('d-m-y H:i', strtotime($property->created_at)) : "";
            }
            elseif ($column == "updated_at"){
                $columns[] = isset($property->updated_at) ? date('d-m-y H:i', strtotime($property->updated_at)) : "";
            }
            elseif ($column == "published_date"){
                $columns[] = isset($property->published_date) ? date('d-m-y H:i', strtotime($property->published_date)) : "";
            }
            elseif ($column == "rera_permit_status"){
                if($property->rera_permit_number =='' || $property->rera_permit_number == null){
                    $columns[] = "Not Available";
                }else {
                    if (Carbon::today()->greaterThan($property->rera_issue_date)) {
                        $columns[] = "Expired";
                    } else {
                        $columns[] = "Valid";
                    }
                }
            }elseif ($column == "portals"){
                $portals = [];
                foreach($property->portals ?? [] as $property_portal){
                    array_push($portals, $property_portal?->portal?->name);
                }
                $columns[] = implode(",", $portals);
            }
            else{
                $columns[] = $property->$column ?? "";
            }
        }



        return $columns;
    }

    public function headings(): array
    {
        $allColumns = getPropertyExportColumns();
        $columns = [];
        foreach ($this->columns ?? [] as $column){
            if (!empty($allColumns[$column])){
                $columns[] = $allColumns[$column];
            }
        }
        return $columns;
    }
}
