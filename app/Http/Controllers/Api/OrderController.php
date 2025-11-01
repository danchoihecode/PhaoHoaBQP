<?php

namespace App\Http\Controllers\Api;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Store;
use RuntimeException;
use App\Models\CartItem;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\DeliveryAddress;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    //
    public function store(Request $request)
    {
        // Xác thực request
        $validated = $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'gender' => 'required|string|in:Anh,Chị',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'orther_request' => 'nullable|string',
            'delivery_method' => 'required|string|in:giao_tan_noi,nhan_tai_sieu_thi',
            'province' => 'required_if:delivery_method,giao_tan_noi|string',
            'street_address' => 'required_if:delivery_method,giao_tan_noi|string',
            'store_id' => 'required_if:delivery_method,nhan_tai_sieu_thi|exists:stores,id'
        ]);

        // dd($request->gender);
        // Khởi tạo và lưu đơn hàng



        return $this->baseActionTransaction(function () use ($request) {

            $order = Order::create([
                'gender' => $request->gender,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'orther_request' => $request->orther_request,
                'delivery_method' => $request->delivery_method,
                'order_status' => 'pending', // Mặc định là pending
                'total_amount' => 0 // Sẽ cập nhật sau
            ]);
            // dd($order);
            // Kiểm tra và lưu thông tin giao hàng
            $deliveryAddressString = null;
            $storeInfo = null;

            // Xử lý logic dựa trên phương thức giao hàng
            if ($request->delivery_method == 'giao_tan_noi') {
                $deliveryAddress = DeliveryAddress::create([
                    'order_id' => $order->id,
                    'province' => $request->province,
                    'street_address' => $request->street_address,
                ]);

                $deliveryAddressString = "{$deliveryAddress->street_address}, {$deliveryAddress->province}";
            } elseif ($request->delivery_method == 'nhan_tai_sieu_thi') {
                $order->stores()->attach($request->store_id); // Lưu thông tin cửa hàng
                $store = Store::find($request->store_id);

                $deliveryAddressString="{$store->store_name}, {$store->address}";
            }

            // Xử lý các mặt hàng trong giỏ
            $totalAmount = 0;
            $cartItems = CartItem::where('cart_id', $request->cart_id)->get();
            foreach ($cartItems as $item) {
                $product = $item->product;
                if ($item->quantity > $product->quantity) {
                    // Kiểm tra tồn kho
                    throw new RuntimeException("Insufficient stock for {$product->name}. Only {$product->quantity} left.");
                }

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $product->price,
                ]);

                $product->decrement('quantity', $item->quantity);
                $totalAmount += $item->quantity * $product->price;
            }

            // Cập nhật tổng tiền
            $order->update(['total_amount' => $totalAmount]);


             // Xóa giỏ hàng và các mục trong giỏ
             CartItem::where('cart_id', $request->cart_id)->delete();
             Cart::where('id', $request->cart_id)->delete();

            // Trả về kết quả
            return [
                'order_id' => $order->id,
                'gender' => $order->gender,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'orther_request' => $order->orther_request,
                'delivery_address' => $deliveryAddressString,
                'total_amount' => $order->total_amount

            ];
        }, __("create order success"), __("create order error"));
    }
}
