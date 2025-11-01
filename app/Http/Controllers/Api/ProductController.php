<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\Services\ProductService;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getProductsByCategorySlug($categorySlug, Request $request)
    {


        return $this->baseAction(function () use($categorySlug, $request){

            $products = $this->productService->getProductsByCategorySlug($categorySlug);
            return $products;
        }, __("Get product success"), __("Get product error"));
    }

    // public function getProductsByCategorySlug($categorySlug)
    // {
    //     // // Tìm danh mục dựa trên slug
    //     // $category = Category::where('slug', $categorySlug)->first();

    //     // if (!$category) {
    //     //     return response()->json(['error' => 'Category not found'], 404);
    //     // }

    //     // // Lấy danh sách sản phẩm thuộc danh mục
    //     // $query = $category->products();

    //     // $total = $query->count();
    //     // $products = $query->get();
    //     // // Tính toán số sao trung bình
    //     // foreach ($products as $product) {
    //     //     $product->average_rating = $product->getAverageRatingAttribute();
    //     // }
    //     // return [
    //     //     'products' => $products,
    //     //     'total' => $total,
    //     // ];


    //     return $this->baseAction(function () use ($categorySlug) {
    //         // Tìm danh mục dựa trên slug
    //         $category = Category::where('slug', $categorySlug)->first();

    //         // Lấy danh sách sản phẩm thuộc danh mục
    //         $query = $category->products();
    //         // dd($category);
    //         $total = $query->count();
    //         $products = $query->get();
    //         // Tính toán số sao trung bình
    //         foreach ($products as $product) {
    //             // dd($product);
    //             $product->average_rating = $product->getAverageRatingAttribute();
    //             $product->product_link = $product->getProductLinkAttribute();
    //             $product->formatted_price = $product->getFormattedPriceAttribute();
    //             $serverIp = request()->getSchemeAndHttpHost();

    //             if (!Str::startsWith($product->image_url, 'http')) {
    //                 $product->image_url = $serverIp . '/' . $product->image_url;
    //             }
    //         }
    //         $data = [
    //             'category' => $category,
    //             'products' => $products,
    //             'total' => $total,
    //         ];

    //         return $data;
    //     }, __("Get product success"), __("Get product error"));
    // }



    public function getFirstTwentyProducts()
    {

        return $this->baseAction(function () {

            $products = $this->productService->getFirstTwentyProducts();
            return $products;
        }, __("Get product success"), __("Get product error"));
    }



    public function getProductDetailsBySlug($slug)
    {


        return $this->baseAction(function () use ($slug) {
            $product = $this->productService->getProductDetailsBySlug($slug);
            return $product;

        }, __("Get product success"), __("Get product error"));
    }






    public function updateDescription(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string',
        ]);

        $product = Product::findOrFail($id);
        $product->description = $request->description;
        $product->save();

        return response()->json([
            'message' => 'Product description updated successfully!',
            'product' => $product
        ]);
    }
}
