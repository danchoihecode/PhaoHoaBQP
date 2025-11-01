<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'slug', 'sku', 'serial_number',
        'stock_status', 'price', 'quantity', 'is_cheap_percent', 'is_cheap_online',
        'discount_price', 'publish','youtube_url'
    ];
    protected $guarded = ['id'];
    protected $casts = [
        'price' => 'integer',
        'discount_price' => 'integer',
        'is_cheap_percent' => 'boolean',
        'is_cheap_online' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function productAdditionalInfo()
    {
        return $this->hasMany(ProductAdditionalInfo::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

}
