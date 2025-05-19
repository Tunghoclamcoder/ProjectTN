<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'product_name',
        'status',
        'quantity',
        'price',
        'discount',
        'description',
        'brand_id',
        'material_id',
        'NumberOfCategory',
        'NumberOfImage'
    ];

    protected $casts = [
        'status' => 'boolean',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'quantity' => 'integer',
        'NumberOfCategory' => 'integer',
        'NumberOfImage' => 'integer'
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'brand_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id', 'material_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id')
            ->using(CategoryProduct::class)
            ->withPivot('category_order', 'category_role');
    }

    public function images()
    {
        return $this->belongsToMany(Image::class, 'image_product', 'product_id', 'image_id')
            ->using(ImageProduct::class)
            ->withPivot('image_order', 'image_role');
    }

    public $timestamps = false;
}
