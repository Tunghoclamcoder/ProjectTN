<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
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
        'NumberOfSize',
        'NumberOfCategory',
        'NumberOfImage'
    ];

    protected $casts = [
        'status' => 'boolean',
        'price' => 'integer',
        'discount' => 'decimal:2',
        'NumberOfSize' => 'integer',
        'NumberOfCategory' => 'integer',
        'NumberOfImage' => 'integer'
    ];

    public $timestamps = false;

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id')
            ->using(CategoryProduct::class)
            ->withPivot('category_order', 'category_role');
    }

    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'image_product', 'product_id', 'image_id')
            ->using(ImageProduct::class)
            ->withPivot('image_order', 'image_role');
    }

    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'size_product', 'product_id', 'size_id')
            ->using(SizeProduct::class)
            ->withPivot('size_order');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class, 'size_id');
    }
    public function getMainImage()
    {
        return $this->images()
            ->wherePivot('image_role', 'main')
            ->orderByPivot('image_order')
            ->first();
    }

    public function getSubImages()
    {
        return $this->images()
            ->wherePivot('image_role', 'sub')
            ->orderByPivot('image_order')
            ->get();
    }

    public function getDiscountedPrice()
    {
        return $this->price * (1 - ($this->discount / 100));
    }
}
