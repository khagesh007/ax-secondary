<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WhatsappLeadsExport implements FromArray, WithHeadings
{
    protected $leads;

    public function __construct(array $leads)
    {
        $this->leads = $leads;
    }

    public function array(): array
    {
        return $this->leads;
    }

    public function headings(): array
    {
        return [
            'ID', 'Phone', 'User ID', 'First Name', 'Last Name', 'User Phone', 'License', 'Property ID', 'Reference', 'Location'
        ];
    }
}
