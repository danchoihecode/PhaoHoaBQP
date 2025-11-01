<?php

namespace App\Http\Requests;
use Illuminate\Support\Str;

class SendVerifyRequest extends BaseRequest
{

    /**
     * Get the validation ac that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'email' => 'required|exists:users,email',
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
