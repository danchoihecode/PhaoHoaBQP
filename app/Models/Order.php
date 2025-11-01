<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['gender',
        'customer_name', 'customer_phone', 'orther_request', 'delivery_method',
        'order_status', 'total_amount', 'payment_method', 'payment_status'
    ];
    protected $appends = ['address'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function deliveryAddress()
    {
        return $this->hasOne(DeliveryAddress::class);
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'order_stores');
    }

     public function getAddressAttribute()
    {
        if ($this->delivery_method === 'giao_tan_noi') {
            // Kiểm tra và trả về địa chỉ từ bảng DeliveryAddress
            return $this->deliveryAddress ? "{$this->deliveryAddress->street_address}, {$this->deliveryAddress->province}" : 'Địa chỉ không xác định';
        } elseif ($this->delivery_method === 'nhan_tai_sieu_thi') {
            // Kiểm tra và trả về địa chỉ từ bảng Store
            $store = $this->stores->first();
            return $store ? "{$store->store_name}, {$store->address}" : 'Địa chỉ không xác định';
        }

        return 'Địa chỉ không xác định';
    }
}
