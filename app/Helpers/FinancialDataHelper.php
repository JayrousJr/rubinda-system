<?php
namespace App\Helpers;

use App\Models\FinancialData;
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;
use Stancl\Tenancy\Facades\Tenancy;

function funds()
{
    $data = FinancialData::query()->get();
    // dd($data);
    return json_decode(response()->json($data->first())->getContent());
}