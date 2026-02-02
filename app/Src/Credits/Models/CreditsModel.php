<?php

namespace App\Src\Credits\Models;

use App\Models\User;
use App\Src\Client\Models\ClientModel;
use App\Src\Credits\Enums\CreditStatusEnum;
use App\Src\Credits\Enums\PaymentFrequencyEnum;
use App\Src\Installments\Models\InstallmentModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditsModel extends Model
{
    use SoftDeletes;

    protected $table = 'credits';

    protected $fillable = [
        'client_id',
        'collector_id',
        'amount_net',
        'amount_total',
        'interest_rate',
        'installments_count',
        'payment_frequency',
        'start_date',
        'status',
    ];

    // Relación con el Cliente
    public function client()
    {
        return $this->belongsTo(ClientModel::class, 'client_id');
    }

    // Relación con el Cobrador (Usuario)
    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }

    public function installments()
    {
        return $this->hasMany(InstallmentModel::class, 'credit_id');
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'amount_net' => 'decimal:2',
            'amount_total' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'status' => CreditStatusEnum::class,
            'payment_frequency' => PaymentFrequencyEnum::class,
        ];
    }
}
