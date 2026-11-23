<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    protected $primaryKey = 'receipt_id';

    protected $fillable = [
        'payment_id',
        'issued_by',
        'receipt_number',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'payment_id');
    }

    public function issuedByStaff()
    {
        return $this->belongsTo(Staff::class, 'issued_by', 'staff_id');
    }
}

