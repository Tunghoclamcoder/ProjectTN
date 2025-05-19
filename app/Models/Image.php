<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $table = 'images';
    protected $primaryKey = 'image_id';
    protected $fillable = ['image_url'];

    public $timestamps = false;

    public function products()
    {
        return $this->belongsToMany(Product::class, 'image_product', 'image_id', 'product_id')
            ->using(ImageProduct::class)
            ->withPivot('image_order', 'image_role');
    }
}
