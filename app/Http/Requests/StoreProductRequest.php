<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends BaseRequest
{

    public function rules(): array
    {
        return [

                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'slug' => 'required|string|unique:products,slug',
                'sku' => 'required|string',
                'serial_number' => 'string|nullable',
                'stock_status' => 'required|in:Còn hàng,Hết hàng',
                'price' => 'required|integer',
                'discount_price' => 'required|integer',
                'youtube_url' => 'nullable|max:255',
                'quantity' => 'required|integer',
                'is_cheap_percent' => 'boolean',
                'is_cheap_online' => 'boolean',
                'category_ids' => 'required|array',
                'category_ids.*' => 'integer|exists:categories,id',
                'images' => 'required|array',
                'images.*.img_url' => 'required|image|max:2048',
                'images.*.is_main' => 'required|boolean',
                'publish' => 'boolean',
                'additional_info' => 'required|array',
                'additional_info.*.key' => 'required|string',
                'additional_info.*.content' => 'required|string',

        ];
    }
}
