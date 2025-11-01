<?php 

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{

    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ];
    }

}    