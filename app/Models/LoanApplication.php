<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "user_id",
        "duration",
        "amount",
        "total_amount_to_be_paid",
        "status",
        "maelezo",
        "name"
    ];

    public function loanRequest()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }
}