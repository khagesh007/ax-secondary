<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private $columns)
    {

    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::with(["parent", "children", "country", "state"])->get();
    }

    public function map($user): array
    {
        $columns = [];
        foreach ($this->columns ?? [] as $column){
            if ($column == "parent"){
                $columns[] = $user->parent->name ?? "";
            }elseif ($column == "children"){
                $columns[] = $user->children->name ?? "";
            }elseif ($column == "country"){
                $columns[] = $user->country->name ?? "";
            }
            elseif ($column == "state"){
                $columns[] = $user->state->name ?? "";
            }elseif($column == "role"){
                $columns[] = getRoles($user->id) ?? "";

            }elseif($column == "parent"){

                $columns[] = $user->parent->full_name ?? "";
            }
            else{
                $columns[] = $user->$column ?? "";
            }
        }
        return $columns;
    }

    public function headings(): array
    {
        $allColumns = getUserExportColumns();
        $columns = [];
        foreach ($this->columns ?? [] as $column){
            if (!empty($allColumns[$column])){
                $columns[] = $allColumns[$column];
            }
        }
        return $columns;
    }
}
