<?php

namespace App\Exports;

use App\Models\Debt;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class DebtFromView implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        return view("exports.debts", [
            "debts" => Debt::where("status", "Active")->get(),
        ]);

    }
}