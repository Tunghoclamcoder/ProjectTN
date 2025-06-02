<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $table = 'brands';
    protected $primaryKey = 'brand_id';
    public $timestamps = false;
    protected $fillable = [
        'brand_name',
        'description',
        'brand_image'
    ];
    public function products()
    {
        return $this->hasMany(Product::class, 'brand_id', 'brand_id');
    }

    public function getBrandImageUrlAttribute()
    {
        if ($this->brand_image) {
            return asset('images/brands/' . $this->brand_image);
        }
        return null;
    }
}
