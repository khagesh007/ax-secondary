<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class UsersImport implements ToCollection
{
    public function collection(Collection $rows)
    {

        foreach ($rows as $row) 
        {
            //dd($row);
            $user = User::where('first_name', $row[1])->where('last_name', $row[2])->update([
                'telegram_chatid' => $row[5],
            ]);
            //dd($user);
        }
    }
}