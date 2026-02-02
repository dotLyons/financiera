<?php

namespace App\Src\Collectors\Actions;

use App\Models\User;
use App\Src\Collectors\DTOs\UpdateCollectorData;

class UpdateCollectorAction
{
    public function execute(User $user, UpdateCollectorData $data): User
    {
        $user->update([
            'name' => $data->name,
            'email' => $data->email,
            'is_active' => $data->isActive,
        ]);

        return $user;
    }
}
