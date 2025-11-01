<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use Illuminate\Http\Request;

class GiftController extends Controller
{
    //
    public function index()
    {


        return $this->baseAction(function () {

            $gifts = Gift::select('id', 'description', 'value')->whereNotNull('image_url')->get();
            return ['gifts' => $gifts];
        }, __("Get Gift success"), __("Get Gift error"));
    }
}
