<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{


    public function rules()
    {
        return [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|unique:products,slug,' . $this->id, // Cần cung cấp id của sản phẩm hiện tại
            'sku' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'stock_status' => 'nullable|in:Còn hàng,Hết hàng',
            'price' => 'nullable|integer',
            'discount_price' => 'nullable|integer',
            'youtube_url' => 'nullable|max:255',
            'quantity' => 'nullable|integer',
            'is_cheap_percent' => 'nullable|boolean',
            'is_cheap_online' => 'nullable|boolean',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'images' => 'nullable|array',
            'images.*.img_url' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'images.*.is_main' => 'nullable|boolean',
            'publish' => 'nullable|boolean',
            'additional_info' => 'nullable|array',
            'additional_info.*.key' => 'nullable|string',
            'additional_info.*.content' => 'nullable|string',
        ];
    }
}
