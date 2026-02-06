<?php

namespace App\Src\CashOperation\Models;

use App\Models\User;
use App\Src\CashOperation\Enums\CashOperationTypeEnum;
use App\Src\Payments\Models\PaymentsModel;
use Illuminate\Database\Eloquent\Model;

class CashOperationModel extends Model
{
    protected $table = 'cash_operations';

    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'concept',
        'payment_id',
        'operation_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'type' => CashOperationTypeEnum::class,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payment()
    {
        return $this->belongsTo(PaymentsModel::class, 'payment_id');
    }
}
