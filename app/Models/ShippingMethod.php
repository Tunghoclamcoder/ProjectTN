<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $table = 'shipping_methods';
    protected $primaryKey = 'method_id';
    protected $fillable = ['method_name', 'shipping_fee'];

    public $timestamps = false;
}
