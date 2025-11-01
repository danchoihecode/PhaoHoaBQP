<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $this->id,
            'slug' => 'required:unique:categories|:max255',
            'parent_id' => 'nullable|exists:categories,id'
        ];
    }
}
