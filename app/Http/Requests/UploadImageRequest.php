<?php

namespace App\Http\Requests;
use Illuminate\Support\Str;

class UploadImageRequest extends BaseRequest
{

    /**
     * Get the validation ac that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image'=> 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

}
