<?php

namespace App\Http\Controllers\Admin;

use App\Models\Store;
use App\Http\Controllers\Controller;

class StoreController extends Controller
{

    public function index()
    {


        return $this->baseAction(function ()  {
            // Lấy tất cả cửa hàng và chỉ chọn các trường cần thiết
            $stores = Store::get();

            // Trả về danh sách cửa hàng dưới dạng JSON
            return $stores;
        }, __("Get stores success"), __("Get stores error"));
    }
    public function show($id)
    {
        // Tìm cửa hàng theo ID
        $store = Store::find($id);

        // Kiểm tra xem cửa hàng có tồn tại không
        if (!$store) {
            return response()->json([
                'message' => 'Store not found'
            ], 404);
        }

        // Trả về thông tin cửa hàng dưới dạng JSON
        return response()->json([
            'id' => $store->id,
            'store_name' => $store->store_name,
            'address' => $store->address
        ]);
    }
}
