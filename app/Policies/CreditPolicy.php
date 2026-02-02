<?php

namespace App\Policies;

use App\Models\User;
use App\Src\Credits\Models\CreditsModel;

class CreditPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, CreditsModel $credit): bool
    {
        return $user->role === 'admin' || $credit->collector_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, CreditsModel $credit): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, CreditsModel $credit): bool
    {
        return $user->role === 'admin';
    }
}
