<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $primaryKey = 'category_id';
    protected $fillable = ['category_name'];

    public $timestamps = false;

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_product', 'category_id', 'product_id')
            ->using(CategoryProduct::class)
            ->withPivot('category_order', 'category_role');
    }
}
