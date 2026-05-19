<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $users;

    public function __construct($users = null)
    {
        $this->users = $users;
    }

    public function collection()
    {
        if ($this->users) {
            return $this->users;
        }
        return User::with('roles', 'division', 'cluster')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Full Name',
            'Email',
            'Phone',
            'Role',
            'Division',
            'Cluster',
            'Status',
            'Last Login',
            'Created At'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->full_name,
            $user->email,
            $user->phone ?? 'N/A',
            $user->roles->first()->name ?? 'No Role',
            $user->division->name ?? 'N/A',
            $user->cluster->name ?? 'N/A',
            $user->email_verified_at ? 'Active' : 'Inactive',
            $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'Never',
            $user->created_at->format('Y-m-d H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
