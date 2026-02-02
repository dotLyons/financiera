<?php

namespace App\Src\Collectors\Actions;

use App\Models\User;
use App\Src\Collectors\DTOs\CreateCollectorData;
use Illuminate\Support\Facades\Hash;

class CreateCollectorAction
{
    public function execute(CreateCollectorData $data): User
    {
        return User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make('cobrador'),
            'role' => 'collector',
            'is_active' => true,
        ]);
    }
}
