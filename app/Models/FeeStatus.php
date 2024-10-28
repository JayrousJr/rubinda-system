<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "fee_id",
        "user_id",
        "is_paid",
    ];

    public function fee()
    {
        return $this->belongsTo(Fee::class, "fee_id", "id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }
}