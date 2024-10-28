<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnWidths, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::all()->map(function ($user, $index) {
            return [
                "No" => $index + 1,
                "Name" => $user->name,
                "Email" => $user->email,
                "Role" => $user->role,
                "Fee Amount" => $user->fee,
            ];
        });
    }
    public function headings(): array
    {
        return ["No", "Name", "Email", "Role", "Fee Amount"];
    }

    public function columnWidths(): array
    {
        return [
            "A" => 5,
            'C' => 55,
            'B' => 45,
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 13
                ]
            ],
            "A" => [
                'font' => [
                    'bold' => true,
                    'size' => 12
                ],
            ],

            'C' => ['font' => ['size' => 12], ['italic' => true]],
            'B' => ['font' => ['size' => 12]],
            'D' => ['font' => ['size' => 12]],
            'E' => ['font' => ['size' => 12]],
        ];
    }
}