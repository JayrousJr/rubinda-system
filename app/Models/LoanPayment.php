<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanPayment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        "debt_id",
        "amount_paid",
        "date",
        "user_id",
    ];

    public function debtPayment()
    {
        return $this->belongsTo(Debt::class, "debt_id", "id");
    }
    public function userLoanPayment()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }
}