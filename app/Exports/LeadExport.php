<?php

namespace App\Exports;

use App\Models\Lead;
use App\Models\User;
use App\Models\LeadNote;
use App\Traits\FiltersTrait;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeadExport implements FromCollection, WithHeadings, WithMapping
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
        $q = Lead::query();

        return $this->applyLeadFilters($q, $this->request)->get();

    }

    public function map($lead): array
    {
        // "id" => "id",
        // "agent_id" => "Agent",
        // "manager_id" => "Manager",
        // 'for' => 'Purpose',
        // 'first_name' => 'First Name',
        // 'last_name' => 'Last Name',
        // 'email' => 'Email',
        // 'mobile' => 'Mobile',
        // 'type_id' => 'Lead Type',
        // 'source_id' => 'Lead Source',
        // 'lead_medium_id' => 'Lead Medium',
        // 'lead_status_id' => 'Lead Status',
        // 'created_at' => 'Created at',
        // 'updated_at' => 'Updated at'

        $columns = [];
        foreach ($this->columns ?? [] as $column){
            if ($column == "id"){
                $columns[] = $lead->id ?? "";
            }
            elseif ($column == "agent_id"){
                $columns[] = $lead->agent->name ?? "";
            }
            elseif ($column == "manager_id"){
                $columns[] = $lead->manager->name ?? "";
            }
            elseif ($column == "for"){
                $columns[] = $lead->for ?? "";
            }
            elseif ($column == "first_name"){
                $columns[] = $lead->first_name ?? "";
            }
            elseif ($column == "last_name"){
                $columns[] = $lead->last_name ?? "";
            }
            elseif ($column == "email"){
                $columns[] = $lead->email ?? "";
            }
            elseif ($column == "mobile"){
                $columns[] = $lead->mobile ?? "";
            }
            elseif ($column == "type_id"){
                $columns[] = $lead->leadType->name ?? "";
            }
            elseif ($column == "source_id"){
                $columns[] = $lead->leadSource->name ?? "";
            }
            elseif ($column == "lead_medium_id"){
                $columns[] = $lead->leadMedium->name ?? "";
            }
            elseif ($column == "lead_status_id"){
                $columns[] = $lead->leadStatus->name ?? "";
            }
            elseif ($column == "created_at"){
                $columns[] = $lead->created_at ?? "";
            }
            elseif ($column == "updated_at"){
                $columns[] = $lead->updated_at ?? "";
            }


        }
        return $columns;
    }

    public function headings(): array
    {
        $allColumns = getLeadsExportColumns();
        $columns = [];
        foreach ($this->columns ?? [] as $column){
            if (!empty($allColumns[$column])){
                $columns[] = $allColumns[$column];
            }
        }
        return $columns;
    }
}
