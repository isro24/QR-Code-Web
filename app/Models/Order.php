<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_name', 'payment_type', 'table_number', 'total_price', 'status'];
    public $timestamps = true;

    public function items()
    {
        return $this->hasMany(OrderItem::class);
        return $this->belongsTo(Table::class);
    }
}
