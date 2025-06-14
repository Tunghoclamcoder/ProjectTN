<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    protected $table = 'order_details';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'sold_price',
        'sold_quantity'
    ];

    protected $casts = [
        'sold_price' => 'decimal:0',
        'sold_quantity' => 'integer'
    ];

    // Relationship with Order
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    // Relationship with Product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    // Calculate subtotal for this item
    public function getSubtotal()
    {
        return $this->sold_price * $this->sold_quantity;
    }
}
