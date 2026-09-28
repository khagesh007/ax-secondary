<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersDeviceExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $users = User::where('status', 1)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Agent');
            })
            ->doesntHave('devices')
            ->with('parent')
            ->get();

        return $users->map(function ($user) {
            return [
                'ID' => $user->id,
                'First Name' => $user->full_name,
                'Mobile' => $user->mobile,
                'Email' => $user->email,
                'Parent Full Name' => $user->parent ? $user->parent->first_name . ' ' . $user->parent->last_name : '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Mobile',
            'Email',
            'Parent Full Name',
        ];
    }
}
