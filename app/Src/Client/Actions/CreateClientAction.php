<?php

namespace App\Src\Client\Actions;

use App\Src\Client\DTOs\CreateClientData as DTOsCreateClientData;
use App\Src\Client\Models\ClientModel;

class CreateClientAction
{
    public function execute(DTOsCreateClientData $data): ClientModel
    {
        return ClientModel::create([
            'last_name' => $data->lastName,
            'first_name' => $data->firstName,
            'dni' => $data->dni,
            'rubro' => $data->rubro,
            'phone' => $data->phone,
            'reference_phone' => $data->referencePhone,
            'address' => $data->address,
            'second_address' => $data->secondAddress,
            'notes' => $data->notes,
            'created_by' => $data->createdBy,
            'status' => 'active', // Default por lógica
        ]);
    }
}
