<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $primaryKey = 'cart_id';

    protected $fillable = [
        'session_id',
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id', 'cart_id');
    }

    public function getTotalAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->menu ? $item->quantity * $item->menu->price : 0;
        });
    }

    public function getTotalCountAttribute()
    {
        return $this->items->sum('quantity');
    }
}

