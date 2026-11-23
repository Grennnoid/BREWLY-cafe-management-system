<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_id';

    protected $fillable = [
        'order_id',
        'confirmed_by',
        'method',
        'status',
        'amount_paid',
        'change_amount',
        'paid_at',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function confirmedByStaff()
    {
        return $this->belongsTo(Staff::class, 'confirmed_by', 'staff_id');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'payment_id', 'payment_id');
    }
}

