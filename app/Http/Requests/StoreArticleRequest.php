<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|unique:articles|max:255',
            'content' => 'required',
            'author' => 'required|max:255',
            'category' => 'required|max:255',
            'release_date' => 'required|date',
            'product_id' => 'required|exists:products,id',
        ];
    }
}