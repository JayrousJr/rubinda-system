<?php

namespace App\Exports;

use App\Models\Debt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class DebtsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnWidths, WithStyles, WithColumnFormatting, WithProperties, WithDrawings
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
            'E' => 30,
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
            'F' => ['font' => ['size' => 12]],
            'G' => [
                'font' => [
                    'size' => 12,
                    'bold' => true,
                ]
            ],
        ];
    }
    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
    public function properties(): array
    {
        return [
            'creator' => 'Rubinda Family',
            'lastModifiedBy' => 'Rubinda Family',
            'title' => 'Debts Export',
            'description' => 'All Debts',
            'subject' => 'Invoices',
            'keywords' => 'debts,export,spreadsheet',
            'category' => 'Debts',
            'manager' => 'Rubinda Family',
            'company' => 'rubinda Family',
        ];
    }
    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path('/storage/image/logo.png'));
        $drawing->setHeight(90);
        $drawing->setCoordinates('B3');

        return $drawing;
    }
}