<?php

namespace App\Exports;

use App\Models\Debt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DebtsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnWidths, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Debt::where("status", "Active")->get()->map(function ($debt, $index) {
            return [
                "No" => $index + 1,
                "Name" => $debt->loanDebt->appliedLoan->name,
                "Kiasi Alichokopa" => $debt->loanDebt->appliedLoan->amount,
                "Rejesho pamoja na Riba" => $debt->loanDebt->appliedLoan->total_amount_to_be_paid,
                "Penalty" => $debt->total_debt - $debt->loanDebt->appliedLoan->total_amount_to_be_paid,
                "Pesa Iliyo rejeshwa" => $debt->total_debt - $debt->remaining_debt === 0 ? "0" : $debt->total_debt - $debt->remaining_debt,
                "Jumla ya deni" => $debt->remaining_debt,

            ];
        });
    }
    public function headings(): array
    {
        return ["No", "Name", "Kiasi Alichokopa", "Rejesho", "Penalty", "Amelipa", "Jumla ya deni"];
    }

    public function columnWidths(): array
    {
        return [
            "A" => 5,
            'C' => 16,
            'B' => 30,
            'B' => 30,
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
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

            'C' => ['font' => ['size' => 12]],
            'B' => ['font' => ['size' => 12]],
            'D' => ['font' => ['size' => 12]],
            'E' => ['font' => ['size' => 12]],
        ];
    }
}