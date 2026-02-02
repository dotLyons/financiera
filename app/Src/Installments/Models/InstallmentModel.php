<?php

namespace App\Src\Installments\Models;

use App\Src\Credits\Models\CreditsModel;
use App\Src\Installments\Enums\InstallmentStatusEnum;
use App\Src\Payments\Models\PaymentsModel;
use Illuminate\Database\Eloquent\Model;

class InstallmentModel extends Model
{
    protected $table = 'installments';

    protected $fillable = [
        'credit_id',
        'installment_number',
        'amount',
        'amount_paid',
        'due_date',
        'status',
    ];

    protected function casts()
    {
        return [
            'due_date' => 'date',
            'amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'status' => InstallmentStatusEnum::class,
        ];
    }

    public function credit()
    {
        return $this->belongsTo(CreditsModel::class, 'credit_id');
    }

    /**
     * Pagos asociados a esta cuota.
     */
    public function payments()
    {
        return $this->hasMany(PaymentsModel::class, 'installment_id');
    }

    public function getRemainingBalanceAttribute()
    {
        return $this->amount - $this->amount_paid;
    }
}
