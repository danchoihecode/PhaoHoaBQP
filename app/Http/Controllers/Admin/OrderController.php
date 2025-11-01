<?php

namespace App\Http\Controllers\Admin;

use App\Main\Services\OrderService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        return $this->baseAction(function () {
            $data = $this->orderService->getAllOrders();
            return $data;
        }, __("Get order success"), __("Get order error"));
    }

    public function show($id)
    {
        return $this->baseAction(function () use ($id) {
            $data = $this->orderService->getOrderById($id);
            return $data;
        }, __("Get order success"), __("Get order error"));
    }

    public function updateStatus(Request $request, $id)
    {
        $status = $request->order_status;
        return $this->baseActionTransaction(function () use ($status,$id ) {
            $data = $this->orderService->updateOrderStatus($status, $id);
            return $data;
        }, __("Update order status success"), __("Update order status error"));
    }
}
