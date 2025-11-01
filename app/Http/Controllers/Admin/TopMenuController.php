<?php

namespace App\Http\Controllers\Admin;

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
                    'id' => $item['id'],
                    'priority' => $item['priority']
                ];
            }, $items);

            return $filteredItems;
        }, __("Get categories success"), __("Get categories error"));
    }


    public function updatePriorities(Request $request)
    {
        $data = $request->validate([
            '*.id' => 'required|exists:categories,id',
            '*.priority' => 'required|integer'
        ]);



        return $this->baseActionTransaction(function () use ($data) {
            // Truy vấn để lấy thông tin từ bảng categories
            $categories = Category::whereIn('id', array_column($data, 'id'))->get()->keyBy('id');

            // Xây dựng mảng để lưu
            $value = array_map(function ($item) use ($categories) {
                $category = $categories[$item['id']] ?? null;
                return [
                    'id' => $item['id'],
                    'name' => $category ? $category->name : null,
                    'slug' => $category ? $category->slug : null,
                    'priority' => $item['priority']
                ];
            }, $data);

            // Sắp xếp mảng theo priority
            usort($value, function ($a, $b) {
                return $a['priority'] <=> $b['priority'];
            });

            // Lưu hoặc cập nhật cấu hình
            $config = Config::updateOrCreate(
                ['key' => 'top_menu'],
                ['value' => json_encode($value)]
            );

            return $config;
        }, __("Create category success"), __("Create category error"));
    }


    public function updateTopMenu(Request $request)
    {
        $data = $request->validate([
            '*.id' => 'required|exists:categories,id',
            '*.priority' => 'required|integer'
        ]);

        // Truy vấn để lấy thông tin từ bảng categories
        $categories = Category::whereIn('id', array_column($data, 'id'))->get()->keyBy('id');

        // Xây dựng mảng để lưu
        $value = array_map(function ($item) use ($categories) {
            $category = $categories[$item['id']] ?? null;
            return [
                'id' => $item['id'],
                'name' => $category ? $category->name : null,
                'slug' => $category ? $category->slug : null,
                'priority' => $item['priority']
            ];
        }, $data);

        // Sắp xếp mảng theo priority
        usort($value, function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });

        // Lưu hoặc cập nhật cấu hình
        $config = Config::updateOrCreate(
            ['key' => 'top_menu'],
            ['value' => json_encode($value)]
        );

        return response()->json([
            'success' => true,
            'message' => 'Top menu updated successfully!',
            'config' => $config
        ]);
    }
}
