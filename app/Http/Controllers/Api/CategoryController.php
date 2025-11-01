<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    function getCategoryNameBySlug($slug){
        return $this->baseAction(function () use($slug) {

            $category = Category::where('slug', $slug)->select('name')->first();
            return $category;

        }, __("Get category success"), __("Get category error"));

    }
}
