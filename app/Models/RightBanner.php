<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RightBanner extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'img_url', 'url_link', 'is_visible'];
    protected $guarded = ['id'];
    protected $casts = [
        'is_visible' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
