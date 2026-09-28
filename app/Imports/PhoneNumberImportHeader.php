<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PhoneNumberImportHeader implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public $headers;

    public function collection(Collection $collection)
    {
        if ($collection->isNotEmpty()) {
            $this->headers = $collection->first()->values();
        } else {
            $this->headers = collect();
        }
    }

    public function startRow(): int
    {
        return 2;
    }
}
