<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    //
    public function getCartId()
    {
        // Tạo một cart mới
        $cart = new Cart();
        $cart->save();  // Lưu cart vào database để tạo ID mới



        return $this->baseAction(function () use ($cart) {

            return ['cart_id' => $cart->id];
        }, __("Get cart success"), __("Get cart error"));
    }


    public function addToCart(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'product_id' => 'required|exists:products,id',
            // 'quantity' => 'required|integer|min:1'
        ]);

        return $this->baseAction(function () use ($request) {
            $cartId = $request->cart_id;
            $productId = $request->product_id;
            $quantity = 1;

            $cartItem = CartItem::where('cart_id', $cartId)
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $quantity;
            } else {
                $cartItem = new CartItem([
                    'cart_id' => $cartId,
                    'product_id' => $productId,
                    'quantity' => $quantity
                ]);
            }

            $cartItem->save();

            return  $this->show($cartId);;
        }, __("Get cart success"), __("Get cart error"));
    }


    public function removeFromCart(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'product_id' => 'required|exists:products,id',
            // 'quantity' => 'required|integer|min:1'
        ]);
        $cartId = $request->cart_id;
        $productId = $request->product_id;



        return $this->baseAction(function () use ($cartId, $productId) {
            $cart = Cart::find($cartId);

            if (!$cart) {
                return ['message' => 'Giỏ hàng không tồn tại'];
            }

            $cartItem = CartItem::where('cart_id', $cartId)->where('product_id', $productId)->first();

            if (!$cartItem) {
                return ['message' => 'Sản phẩm không tồn tại trong giỏ hàng'];
            }

            $cartItem->delete();

            return $this->show($cartId);
        }, __("Remove cart item success"), __("Remove cart item error"));
    }

    public function increaseQuantity(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'product_id' => 'required|exists:products,id',
            // 'quantity' => 'required|integer|min:1'
        ]);
        $cartId = $request->cart_id;
        $productId = $request->product_id;


        return $this->baseAction(function () use ($cartId, $productId) {
            $cart = Cart::find($cartId);

            if (!$cart) {
                return ['message' => 'Giỏ hàng không tồn tại'];
            }

            $cartItem = CartItem::where('cart_id', $cartId)->where('product_id', $productId)->first();

            if (!$cartItem) {
                return ['message' => 'Sản phẩm không tồn tại trong giỏ hàng'];
            }

            $cartItem->quantity += 1;
            if ($cartItem->quantity <= 1000)
                $cartItem->save();

            return $this->show($cartId);
        }, __("Get increase cart item success"), __("Get increase cart item error"));
    }

    public function decreaseQuantity(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'product_id' => 'required|exists:products,id',
            // 'quantity' => 'required|integer|min:1'
        ]);
        $cartId = $request->cart_id;
        $productId = $request->product_id;


        return $this->baseAction(function () use ($cartId, $productId) {
            $cart = Cart::find($cartId);

            if (!$cart) {
                return ['message' => 'Giỏ hàng không tồn tại'];
            }

            $cartItem = CartItem::where('cart_id', $cartId)->where('product_id', $productId)->first();

            if (!$cartItem) {
                return ['message' => 'Sản phẩm không tồn tại trong giỏ hàng'];
            }

            if ($cartItem->quantity > 1) {
                $cartItem->quantity -= 1;
                $cartItem->save();
            }


            return $this->show($cartId);
        }, __("Get decrease cart item success"), __("Get decrease cart item error"));
    }


    public function updateQuantity(Request $request)
    {
        $request->validate([
            'cart_id' => 'required|exists:carts,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        $cartId = $request->cart_id;
        $productId = $request->product_id;
        $quantity = $request->quantity;


        return $this->baseAction(function () use ($cartId, $productId, $quantity) {
            $cart = Cart::find($cartId);

            if (!$cart) {
                return ['message' => 'Giỏ hàng không tồn tại'];
            }

            $cartItem = CartItem::where('cart_id', $cartId)->where('product_id', $productId)->first();

            if (!$cartItem) {
                return ['message' => 'Sản phẩm không tồn tại trong giỏ hàng'];
            }


            if ($quantity > 0 && $quantity <= 1000) {
                $cartItem->quantity = $quantity;
                $cartItem->save();
            }


            return $this->show($cartId);
        }, __("Get decrease cart item success"), __("Get decrease cart item error"));
    }

    public function getCart($cartId)
    {

        return $this->baseAction(function () use ($cartId) {

            $cart = Cart::with(['cartItems.product.images' => function ($query) {
                $query->where('is_main', true);
            }])->find($cartId);
            if (!$cart) {
                return ['message' => 'Giỏ hàng không tồn tại'];
            }

            $totalQuantity = 0;
            $totalPrice = 0;
            $products = [];

            foreach ($cart->cartItems as $item) {
                $product = $item->product;
                $totalQuantity += $item->quantity;
                $totalPrice += ($product->discount_price ?? $product->price) * $item->quantity;

                $products[] = [
                    'id' => $product->id,
                    'slug' => $product->slug,
                    'name' => $product->name,
                    'image' => $this->formatImageUrl($product->images->first()->img_url),
                    'price' => $product->price,
                    'discount_price' => $product->discount_price,
                    'quantity' => $item->quantity
                ];
            }

            $summary = [
                'totalQuantity' => $totalQuantity,
                'totalPrice' => $totalPrice,
                'products' => $products
            ];

            return $summary;
        }, __("Get cart item success"), __("Get cart item error"));
    }
    public function show($cartId)
    {
        $cart = Cart::with(['cartItems.product.images' => function ($query) {
            $query->where('is_main', true);
        }])->find($cartId);
        if (!$cart) {
            return ['message' => 'Giỏ hàng không tồn tại'];
        }

        $totalQuantity = 0;
        $totalPrice = 0;
        $products = [];

        foreach ($cart->cartItems as $item) {
            $product = $item->product;
            $totalQuantity += $item->quantity;
            $totalPrice += ($product->discount_price ?? $product->price) * $item->quantity;

            $products[] = [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->name,
                'image' => $this->formatImageUrl($product->images->first()->img_url),
                'price' => $product->price,
                'discount_price' => $product->discount_price,
                'quantity' => $item->quantity
            ];
        }

        $summary = [
            'totalQuantity' => $totalQuantity,
            'totalPrice' => $totalPrice,
            'products' => $products
        ];


        return $summary;
    }


    public function formatImageUrl($imageUrl)
    {
        // Lấy base URL từ request hiện tại
        if ($imageUrl && !Str::startsWith($imageUrl, 'http')) {
            return url($imageUrl);
        }
        return $imageUrl;
    }
}
