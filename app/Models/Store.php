<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $fillable = ['store_name', 'address', 'phone_number'];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_stores');
    }
}
