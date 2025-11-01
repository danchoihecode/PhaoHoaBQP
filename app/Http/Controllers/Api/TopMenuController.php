<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Config;
use Illuminate\Http\Request;

class TopMenuController extends Controller
{
    //
    public function index()
    {

        return $this->baseAction(function () {

            $config = Config::where('key', 'top_menu')->first();

            $items = json_decode($config->value, true);
            usort($items, function ($item1, $item2) {
                return $item1['priority'] <=> $item2['priority'];
            });

            $filteredItems = array_map(function ($item) {
                return [
                    'name' => $item['name'],
                    'slug' => $item['slug']
                ];
            }, $items);

            return $filteredItems;

            return $items;
        }, __("Get categories success"), __("Get categories error"));
    }
}
