<?php

namespace App\Observers;

use App\Models\FeePayment;
use App\Models\FeeStatus;

class FeePaymentObserver
{
    /**
     * Handle the FeePayment "created" event.
     */
    public function created(FeePayment $feePayment): void
    {
        FeeStatus::updateOrCreate(
            [
                'fee_id' => $feePayment->fee_id,
                'user_id' => $feePayment->user_id,
            ],
            ['is_paid' => true]
        );
    }

    /**
     * Handle the FeePayment "updated" event.
     */
    public function updated(FeePayment $feePayment): void
    {
        //
    }

    /**
     * Handle the FeePayment "deleted" event.
     */
    public function deleted(FeePayment $feePayment): void
    {
        //
    }

    /**
     * Handle the FeePayment "restored" event.
     */
    public function restored(FeePayment $feePayment): void
    {
        //
    }

    /**
     * Handle the FeePayment "force deleted" event.
     */
    public function forceDeleted(FeePayment $feePayment): void
    {
        //
    }
}