<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoryProduct extends Pivot
{
    protected $table = 'category_product';

    public $timestamps = false;

    protected $fillable = [
        'category_id',
        'product_id',
        'category_order',
        'category_role'
    ];
}
