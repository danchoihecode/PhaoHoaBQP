<?php

namespace App\Http\Requests;
use Illuminate\Support\Str;

class ChangePasswordRequest extends BaseRequest
{

    /**
     * Get the validation ac that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'required|min:6',
            'new_password' => 'required|min:6',

        ];
    }

    public function messages()
    {
        return [

        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([

            'slug' => Str::slug($this->slug),

        ]);
    }
}
