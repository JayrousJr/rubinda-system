<?php

namespace App\Policies;

use App\Models\FinancialData;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FinancialDataPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {

        return true;

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FinancialData $financialData): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FinancialData $financialData): bool
    {
        if ($user->hasRole(["Secretary", "Accountant"])) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FinancialData $financialData): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FinancialData $financialData): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FinancialData $financialData): bool
    {
        return false;
    }
}