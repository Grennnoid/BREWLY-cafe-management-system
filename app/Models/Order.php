<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';

    protected $fillable = [
        'customer_name',
        'table_id',
        'order_type',
        'status',
        'total_amount',
        'note',
        'ordered_at',
        'completed_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'ordered_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function table()
    {
        return $this->belongsTo(CafeTable::class, 'table_id', 'table_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'order_id');
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class, 'order_id', 'order_id')->orderBy('changed_at', 'desc');
    }

    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'PENDING' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
            'ACCEPTED' => 'bg-blue-100 text-blue-800 border-blue-300',
            'PREPARING' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            'READY' => 'bg-purple-100 text-purple-800 border-purple-300',
            'COMPLETED' => 'bg-green-100 text-green-800 border-green-300',
            'CANCELLED' => 'bg-red-100 text-red-800 border-red-300',
            default => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
}

