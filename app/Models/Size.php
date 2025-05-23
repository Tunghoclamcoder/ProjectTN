<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $table = 'sizes';
    protected $primaryKey = 'size_id';

    protected $fillable = [
        'size_name'
    ];
    public $timestamps = false;

    public function products()
    {
        return $this->belongsToMany(Product::class, 'size_product', 'size_id', 'product_id')
            ->using(sizeProduct::class)
            ->withPivot('size_order');
    }
}
