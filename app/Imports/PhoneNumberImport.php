<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PhoneNumberImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public $headers;
    public $firstRow;

    public function collection(Collection $collection)
    {
        $this->columnValues = collect();

        // Ensure collection is not empty
        if ($collection->isNotEmpty()) {
            // Skip the first row (headers) and get the values of the specified column
            $collection->shift(); // Remove the first row (headers)
            foreach ($collection as $row) {
                $this->columnValues->push($row);
            }
        }
    }

    public function getSpecificColumnValue($index)
    {
        // Return the specific column value based on the index
        return $this->firstRow->get($index, null);
    }
    public function getColumnValues($index)
    {
        // Extract and return the values from the specified column index
        return $this->columnValues->map(function ($item) use ($index) {
            return $item->get($index, null);
        })->filter(); // Remove null values if any
    }
}
