<?php


namespace App\Http\Requests;

use Illuminate\Support\Str;

class UserUpdateRequest extends BaseRequest
{

    /**
     * Get the validation ac that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        return [
            'name' => 'required|string',
            'email' => 'required|email',
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
