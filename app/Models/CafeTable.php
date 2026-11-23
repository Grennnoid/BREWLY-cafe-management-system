<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CafeTable extends Model
{
    use HasFactory;

    protected $table = 'cafe_tables';
    protected $primaryKey = 'table_id';

    protected $fillable = [
        'table_number',
        'capacity',
        'status',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'table_id', 'table_id');
    }
}

