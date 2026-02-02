<?php

namespace App\Src\Payments\Models;

use App\Models\User;
use App\Src\CashOperation\Models\CashOperationModel;
use App\Src\Installments\Models\InstallmentModel;
use App\Src\Payments\Enums\PaymentMethodsEnum;
use Illuminate\Database\Eloquent\Model;

class PaymentsModel extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'installment_id',
        'user_id',
        'amount',
        'payment_method',
        'payment_date',
        'proof_of_payment',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'payment_method' => PaymentMethodsEnum::class,
    ];

    public function installment()
    {
        return $this->belongsTo(InstallmentModel::class, 'installment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cashOperation()
    {
        return $this->hasOne(CashOperationModel::class, 'payment_id');
    }
}
