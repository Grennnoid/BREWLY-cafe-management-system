<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'staff';
    protected $primaryKey = 'staff_id';

    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the password for the staff member (ERD defines column as password_hash).
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function statusLogs()
    {
        return $this->hasMany(OrderStatusLog::class, 'changed_by', 'staff_id');
    }

    public function confirmedPayments()
    {
        return $this->hasMany(Payment::class, 'confirmed_by', 'staff_id');
    }

    public function issuedReceipts()
    {
        return $this->hasMany(Receipt::class, 'issued_by', 'staff_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'ADMIN';
    }

    public function isCashier(): bool
    {
        return $this->role === 'CASHIER';
    }

    public function isKitchen(): bool
    {
        return $this->role === 'KITCHEN';
    }
}

