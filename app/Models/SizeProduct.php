<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SizeProduct extends Pivot
{
    protected $table = 'size_product';

    public $timestamps = false;

    protected $fillable = [
        'size_id',
        'product_id',
        'size_order',
    ];
}
