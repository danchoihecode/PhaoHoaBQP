<?php

namespace App\Main\Services;

use App\Main\Repositories\OrderRepository;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class OrderService
{

    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getAllOrders(): Collection
    {
        return Order::with('deliveryAddress', 'stores')->get();
    }

    public function getOrderById(int $id): Order
    {
        // Thêm mối quan hệ 'deliveryAddress' và 'stores' vào Eager Loading
        return Order::with('orderItems.product.images', 'deliveryAddress', 'stores')->findOrFail($id);
    }

    public function updateOrderStatus(string $status, int $id): bool
    {
        $order = Order::find($id);

        if (!$order) {
            throw new ModelNotFoundException("Order not found.");
        }

        $order->order_status = $status;
        return $order->save();
    }

}
