<?php

namespace App\Src\Client\Actions;

use App\Src\Client\DTOs\UpdateClientData;
use App\Src\Client\Models\ClientModel;

class UpdateClientAction
{
    public function execute(ClientModel $client, UpdateClientData $data): ClientModel
    {
        $client->update([
            'last_name' => $data->lastName,
            'first_name' => $data->firstName,
            'dni' => $data->dni,
            'phone' => $data->phone,
            'address' => $data->address,
            'notes' => $data->notes,
            'status' => $data->status,
        ]);

        return $client;
    }
}
