<?php

namespace App\Models;

use App\Traits\SearchElastic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use SearchElastic;

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

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_id', 'product_id');
    }

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

    public function getPrimaryCategory()
    {
        return $this->categories()->first();
    }

    public function getPrimarySize()
    {
        return $this->sizes()->first();
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

    public function toSearchableArray()
    {
        return [
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'description' => $this->description,
            'price' => $this->price,
            'category_name' => $this->getPrimaryCategory() ? $this->getPrimaryCategory()->category_name : null,
            'brand_name' => $this->brand ? $this->brand->brand_name : null
        ];
    }
}
