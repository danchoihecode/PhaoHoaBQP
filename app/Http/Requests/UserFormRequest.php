<?php

namespace App\Http\Requests;
use Illuminate\Support\Str;

class UserFormRequest extends BaseRequest
{

    /**
     * Get the validation ac that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $pass = $this->method() == 'POST' ? 'required|min:6' : 'nullable|min:6';
        $email = $this->method() == 'POST' ? 'required|email|unique:users,email' : 'required|email';
        return [
            'name'=> 'required|string',
            'email' => $email,
            'password' => $pass,
            'phone' => 'nullable',

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
