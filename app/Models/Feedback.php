<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';
    protected $primaryKey = 'feedback_id';

    protected $fillable = [
        'customer_id',
        'order_id',
        'comment',
        'rating'
    ];

    // Relationship with Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    // Relationship with Product
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}
