<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersAuthToken implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {

        $users = User::where('status', 1)->whereDoesntHave('googleauthtoken')->whereHas('roles', function ($query) {
            $query->whereIn('name', ['Agent', 'Line Manager', 'Team Leader', 'Sales Director']);
        })->with('parent')->get();

        return $users->map(function ($user) {
            return [
                'ID' => $user->id,
                'First Name' => $user->full_name,
                'Mobile' => $user->mobile,
                'Email' => $user->email,
                'Role' => getUserRoles($user->id),
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
            'Role',
            'Parent Full Name',
        ];
    }
}
