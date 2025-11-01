<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'image_url', 'value'];
    protected $guarded = ['id'];
    protected $casts = [
        'value' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_gifts');
    }
}
