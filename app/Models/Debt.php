<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        "loan_id",
        "user_id",
        "total_debt",
        "remaining_debt",
        "status",
        "original_amount",
        "last_increment_date"
    ];
    public function loanDebt()
    {
        return $this->BelongsTo(Loan::class, "loan_id", "id");
    }
    // Accessing overdue date
    public function getOverdueDateAttribute()
    {
        return $this->created_at->addDays(90);
    }
    // Access days remaining until overdue
    public function getDaysRemainingAttribute()
    {
        if ($this->status == "Paid") {
            return "Cleared";
        }
        $daysRemaining = Carbon::now()->diffInDays($this->overdue_date, false);
        return ceil($daysRemaining);
    }


    // Method to increment remaining debt
    public function incrementDebt()
    {
        // Calculate the initial due date (90 days from creation)
        $dueDate = Carbon::parse($this->created_at)->addDays(90);
        dump('1. Due Date: ' . $dueDate);

        if (Carbon::now()->greaterThan($dueDate)) {
            // Get the date of the last increment, or set it to the initial due date if null
            $lastIncrementDate = $this->last_increment_date ? Carbon::parse($this->last_increment_date) : $dueDate;
            dump('2. Last Increment Date: ' . $lastIncrementDate);

            // Calculate the days overdue and the current period since the due date
            $daysOverdue = abs(Carbon::now()->diffInDays($dueDate));
            $currentPeriod = ceil($daysOverdue / 30);
            dump('3. Days Overdue:', $daysOverdue);
            dump('4. Current Overdue Period (30-day intervals):', $currentPeriod);

            // Calculate the overdue period as of the last increment date
            $lastIncrementDays = abs($lastIncrementDate->diffInDays($dueDate));
            $lastIncrementPeriod = ceil($lastIncrementDays / 30);
            dump('5.1 Last Increment Days From:' . $lastIncrementDate . " to " . $dueDate . " is: " . $lastIncrementDays);
            dump('5. Last Increment Period (30-day intervals):', $lastIncrementPeriod);
            $diffInPeriod = abs($currentPeriod - $lastIncrementPeriod);
            // Only increment if the current period is greater than the last increment period
            if ($currentPeriod > $lastIncrementPeriod) {
                // Calculate the increase amount for one overdue period
                $increaseAmount = ceil(intval($this->original_amount / 30)) * $diffInPeriod;
                dump('6. Increase Amount for This Period:', $increaseAmount);

                // Update debt amounts
                $this->total_debt += $increaseAmount;
                $this->remaining_debt += $increaseAmount;

                // Update last_increment_date to mark this increment period
                $this->last_increment_date = Carbon::now();
                dump('7. New Last Increment Date Set To: ' . $this->last_increment_date);
                dump("ceil 0.03: ", ceil(0.03));
                // Save changes to the database
                $this->save();
                dump('8. Debt updated successfully for Debt ID:', $this->id);
            } else {
                dump('9. No increment needed for Debt ID:', $this->id);
            }
        } else {
            dump('10. Debt not yet overdue for Debt ID:', $this->id);
        }
    }

}