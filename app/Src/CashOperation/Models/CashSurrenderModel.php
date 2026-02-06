<?php

namespace App\Src\CashOperation\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CashSurrenderModel extends Model
{
    protected $table = 'cash_surrenders';

    protected $fillable = [
        'collector_id',
        'admin_id',
        'amount',
        'payment_method',
        'notes',
        'surrendered_at'
    ];

    protected $casts = [
        'surrendered_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    // Relación: Quién entregó
    public function collector()
    {
        return $this->belongsTo(User::class, 'collector_id');
    }

    // Relación: Quién recibió
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
