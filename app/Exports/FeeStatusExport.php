<?php
namespace App\Exports;

use App\Models\FeeStatus;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class FeeStatusExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnWidths, WithStyles
{
    protected $feeId;

    // Constructor to accept the fee_id
    public function __construct($feeId)
    {
        $this->feeId = $feeId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return FeeStatus::with('fee', 'user')
            ->where('fee_id', $this->feeId)
            ->get()
            ->map(function ($status, $index) {
                return [
                    "No" => $index + 1,
                    "Ada ya Mwezi na Mwaka" => $status->fee->name,
                    "Jina la Mwana chama" => $status->user->name,
                    "Kiasi cha Ada" => $status->user->fee,
                    'Status' => $status->is_paid ? 'Paid' : 'UnPaid',
                ];
            });
    }

    // Define headings for the export file
    public function headings(): array
    {
        return ["No", "Ada ya Mwezi na Mwaka", "Jina la Mwana chama", "Kiasi cha Ada", "Status"];
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