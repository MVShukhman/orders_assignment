<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'address',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
