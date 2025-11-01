<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'sometimes|unique:articles,title,' . $this->article . '|max:255',
            'content' => 'sometimes',
            'author' => 'sometimes',
            'category' => 'sometimes',
            'product_id' => 'sometimes|exists:products,id',
        ];
    }
}