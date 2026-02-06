<?php

namespace App\Src\Client\Models;

use App\Src\Client\Enum\ClientStatusEnum;
use App\Src\Credits\Models\CreditsModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientModel extends Model
{
    use SoftDeletes;

    protected $table = 'clients';

    protected $fillable = [
        'last_name',
        'first_name',
        'dni',
        'rubro',
        'phone',
        'reference_phone',
        'address',
        'second_address',
        'status',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => ClientStatusEnum::class,
        ];
    }

    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->last_name} {$this->first_name}",
        );
    }

    /**
     * Obtener los créditos asociados al cliente.
     * Un Cliente tiene MUCHOS Créditos.
     */
    public function credits()
    {
        return $this->hasMany(CreditsModel::class, 'client_id');
    }

    /**
     * Opcional: Solo créditos activos
     */
    public function activeCredits()
    {
        return $this->credits()->where('status', 'active');
    }

    /**
     * Usuario que creó el cliente.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
