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
            'rubro' => $data->rubro,
            'phone' => $data->phone,
            'reference_phone' => $data->referencePhone,
            'address' => $data->address,
            'second_address' => $data->secondAddress,
            'notes' => $data->notes,
            'status' => $data->status,
        ]);

        return $client;
    }
}
