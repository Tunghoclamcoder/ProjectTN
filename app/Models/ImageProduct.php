<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ImageProduct extends Pivot
{
    protected $table = 'image_product';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'image_id',
        'image_order',
        'image_role'
    ];
}
