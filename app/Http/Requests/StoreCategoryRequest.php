<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|unique:categories|max:255',
            'slug' => 'required:unique:categories|:max255',
        ];
    }
}
