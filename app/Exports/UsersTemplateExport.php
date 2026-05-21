<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            [
                'John Doe',
                'john@example.com',
                'user',
                'Marketing',
                'Cluster A',
                '1234567890',
                'Active'
            ],
            [
                'Jane Smith',
                'jane@example.com',
                'admin',
                'IT',
                'Cluster B',
                '0987654321',
                'Active'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'full_name',
            'email',
            'role',
            'division',
            'cluster',
            'phone',
            'status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']]]],
        ];
    }
}
