<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        "user_id",
        "duration",
        "amount",
        "total_amount_to_be_paid",
        "status",
        "loan_application_id"
    ];
    public function appliedLoan()
    {
        return $this->BelongsTo(LoanApplication::class, "loan_application_id", "id");
    }

    public function userLoan()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }
}