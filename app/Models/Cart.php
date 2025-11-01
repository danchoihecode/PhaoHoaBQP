<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = [
        'session_id'
    ];


    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }
}
