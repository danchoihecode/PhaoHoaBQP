<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    // protected $fillable = ['product_id', 'name', 'email', 'rating', 'comment'];

    protected $fillable = ['product_id', 'rating', 'email', 'name', 'content'];
    protected $guarded = ['id'];
    protected $casts = [
        'rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
