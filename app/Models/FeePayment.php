<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeePayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "user_id",
        "fee_id",
        "amount"
    ];

    public function userFeePayment()
    {
        return $this->BelongsTo(User::class, "user_id", "id");
    }

    public function feePayment()
    {
        return $this->BelongsTo(Fee::class, "fee_id", "id");
    }
}