<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Main\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class ProductController extends Controller
{

    protected $productService;
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('limit');
        // dd($request);
        return $this->baseAction(function () use ($perPage) {
            $serverIp = request()->getSchemeAndHttpHost();

            $data = $this->productService->getAllProducts($perPage);
            foreach ($data as $product) {
                // dd($product);
                if (!Str::startsWith($product->image_url, 'http')) {
                    $product->image_url = $serverIp . '/' . $product->image_url;
                }
            }

            return $data;
        }, __("Get product success"), __("Get product error"));
    }


    public function getListProducts(Request $request)
    {


        return $this->baseAction(function () use ($request) {
            $perPage = $request->input('limit', 10); // Mặc định là 10 nếu không có per_page
            $products = Product::with(['images' => function ($query) {
                $query->select('product_id', 'img_url')->where('is_main', true); // Lấy ra ảnh chính của sản phẩm
            }])
                ->select('id', 'name', 'stock_status', 'price', 'discount_price')
                ->paginate($perPage);

            // Định dạng lại dữ liệu để chỉ lấy các trường cần thiết
            $formattedProducts = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'stock_status' => $product->stock_status,
                    'price' => $product->price,
                    'discount_price' => $product->discount_price,
                    'image' => $product->images->first()->img_url ?? null,
                ];
            });

            return [
                'products' => $formattedProducts,
                'pagination' => [
                    'last_page' => $products->lastPage()
                ],
            ];
        }, __("Get product success"), __("Get product error"));
    }


    public function searchListProducts(Request $request)
    {

        return $this->baseAction(function () use ($request) {
            $query = Product::query();
            // $query->where('publish', 1);
            // Tìm kiếm theo tên sản phẩm
            if ($request->has('name')) {
                $query->where('name', 'like', '%' . $request->input('name') . '%');
            }

            // Lọc theo trạng thái kho
            if ($request->has('stock_status')) {
                $query->where('stock_status', $request->input('stock_status'));
            }
            $perPage = $request->input('limit', 10); // Mặc định là 10 nếu không có per_page
            // Tải trước các mối quan hệ cần thiết
            $products = $query->with(['images' => function ($query) {
                $query->select('product_id', 'img_url')->where('is_main', true); // Lấy ra ảnh chính của sản phẩm
            }])->select('id', 'name', 'stock_status', 'price', 'discount_price')
                ->paginate($perPage);

            // dd($products);
            // Định dạng lại dữ liệu để chỉ lấy các trường cần thiết
            $formattedProducts = $products->map(function ($product) {

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'stock_status' => $product->stock_status,
                    'price' => $product->price,
                    'discount_price' => $product->discount_price,
                    'image' => $this->productService->formatImageUrl($product->images->first()->img_url ?? null),
                ];
            });

            return [
                'products' => $formattedProducts,
                'pagination' => [
                    'last_page' => $products->lastPage()
                ],
            ];
        }, __("Get product success"), __("Get product error"));
    }

    public function show($id)
    {
        return $this->baseAction(function () use ($id) {
            // dd($id);
            $data = $this->productService->getProductDetailsById($id);
            return $data;
        }, __("Get product success"), __("Get product error"));
    }

    // public function storev2(StoreProductRequest $request)
    // {
    //     // dd($request->validated());
    //     $productData = $request->validated();

    //     $categories = $request->input('categories');

    //     // dd($categories);

    //     // $data = $request->all();

    //     if ($request->hasFile('image')) {
    //         // dd($request->hasFile('image'));
    //         $file = $request->file('image');
    //         $timestamp = time();
    //         $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    //         $slugifiedName = Str::slug($originalName, '-');
    //         $extension = $file->extension();
    //         $newFilename = $timestamp . '-' . $slugifiedName . '.' . $extension;
    //         $request->image->move(public_path('images'), $newFilename);
    //         // dd($productData['image_url']);
    //         $productData['image_url'] = 'images/' . $newFilename;
    //         // dd($productData['image_url']);

    //         // dd($data);
    //     }

    //     return $this->baseActionTransaction(function () use ($productData, $categories) {
    //         // dd($productData);

    //         $product = $this->productService->createProduct($productData);
    //         // dd($product);
    //         $product->categories()->sync($categories);

    //         if (!Str::startsWith($product->image_url, 'http')) {
    //             $serverIp = request()->getSchemeAndHttpHost();
    //             $product->image_url = $serverIp . '/' . $product->image_url;
    //         }

    //         return $product;
    //     }, __("Create product success"), __("Create product error"));
    // }


    public function store(StoreProductRequest $request)
    {
        // Giả sử $productService được inject vào controller hoặc được lấy từ container service
        $productService = $this->productService;

        return $this->productCreationTransaction($productService, function (&$uploadedImages, $productService) use ($request) {
            $validated = $request->validated();
            $product = Product::create($validated);



            $categories = [];
            foreach ($request->category_ids as $categoryId) {
                $categories[$categoryId] = ['created_at' => now(), 'updated_at' => now()];
            }

            $product->categories()->sync($categories);

            foreach ($request->images as $image) {
                $imageUrl = $productService->moveImage($image['img_url']);
                $product->images()->create([
                    'img_url' => $imageUrl,
                    'is_main' => $image['is_main']
                ]);
                $uploadedImages[] = $imageUrl;  // Thêm đường dẫn ảnh vào mảng $uploadedImages
            }
            foreach ($request->additional_info as $info) {
                $product->productAdditionalInfo()->create([
                    'key' => $info['key'],
                    'content' => $info['content'],
                ]);
            }

            return $product;
        }, __("Create product success"), __("Create product error"));
    }


    // public function update(UpdateProductRequest $request, $id)
    // {
    //     return $this->baseActionTransaction(function () use ($request, $id) {

    //         $data = $request->all();
    //         if ($request->hasFile('image')) {

    //             $file = $request->file('image');
    //             $timestamp = time();
    //             $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    //             $slugifiedName = Str::slug($originalName, '-');
    //             $extension = $file->extension();
    //             $newFilename = $timestamp . '-' . $slugifiedName . '.' . $extension;
    //             $request->image->move(public_path('images'), $newFilename);
    //             $data['image_url'] = 'images/' . $newFilename;
    //         }

    //         $data = $this->productService->updateProduct($id, $data);

    //         if ($request->has('category_ids')) {
    //             $this->productService->updateProductCategories($id, $request->get('category_ids'));
    //         }

    //         if (!Str::startsWith($data->image_url, 'http')) {
    //             $serverIp = request()->getSchemeAndHttpHost();
    //             $data->image_url = $serverIp . '/' . $data->image_url;
    //         }
    //         return $data;
    //     }, __("Update product success"), __("Update product error"));
    // }

    public function update(UpdateProductRequest $request, $id)
    {
        $productService = $this->productService;


        return $this->productUpdateTransaction($productService, function (&$updatedImages, $productService) use ($request, $id) {
            $product = Product::findOrFail($id);
            // dd($product->productAdditionalInfo());
            $validated = $request->validated();
            // dd($validated);
            $product->update($validated);

            //Cập nhật các danh mục sản phẩm
            if ($request->has('category_ids')) {
                $categories = [];
                foreach ($request->category_ids as $categoryId) {
                    $categories[$categoryId] = ['updated_at' => now()];
                }
                $product->categories()->sync($categories);
            }

            // Cập nhật ảnh sản phẩm
            // if ($request->has('images')) {
            //     foreach ($request->images as $image) {
            //         $imageUrl = $productService->moveImage($image['img_url']);
            //         $product->images()->updateOrCreate(
            //             ['img_url' => $image['img_url']],
            //             ['img_url' => $imageUrl, 'is_main' => $image['is_main']]
            //         );
            //         $updatedImages[] = $imageUrl;
            //     }
            // }


            if ($request->has('images')) {
                $ids = [];
                foreach($request->images as $image1){
                    if(!empty($image1['id'])){
                        $ids[] = $image1['id'];
                    }
                }
                $product->images()->whereNotIn('id', $ids)->delete();

                foreach ($request->images as $image) {
                    // Kiểm tra nếu có ID và có file ảnh
                    if (!empty($image['id']) && !empty($image['img_url'])) {
                        $imageUrl = $productService->moveImage($image['img_url']);
                        // dd($image['id']);
                        // Cập nhật bản ghi có sẵn với ảnh mới và trạng thái is_main
                        $product->images()->updateOrCreate(
                            ['id' => $image['id']],
                            ['img_url' => $imageUrl, 'is_main' => $image['is_main']]
                        );
                        $updatedImages[] = $imageUrl;
                    }
                    // Kiểm tra nếu có ID nhưng không có file ảnh
                    elseif (!empty($image['id']) && empty($image['img_url'])) {
                        // Cập nhật chỉ trường is_main
                        // dd($image['id']);
                        $product->images()->where('id', $image['id'])->update(['is_main' => $image['is_main']]);
                    }
                    // Kiểm tra nếu không có ID nhưng có file ảnh
                    elseif (empty($image['id']) && !empty($image['img_url'])) {
                        $imageUrl = $productService->moveImage($image['img_url']);
                        // Tạo mới bản ghi với ảnh và trạng thái is_main
                        $product->images()->create([
                            'img_url' => $imageUrl,
                            'is_main' => $image['is_main']
                        ]);
                        $updatedImages[] = $imageUrl;
                    }
                }
            }
            //Cập nhật thông tin bổ sung
            if ($request->has('additional_info')) {
                $ids = [];
                foreach($request->additional_info as $info1){
                    if(!empty($info1['id'])){
                        $ids[] = $info1['id'];
                    }
                }
                $product->productAdditionalInfo()->whereNotIn('id', $ids)->delete();

                foreach ($request->additional_info as $info) {

                    // Kiểm tra nếu có ID và có file ảnh
                    if (!empty($info['id']) && !empty($info['key']) ) {

                        // dd($iconUrl);
                        // dd($image['id']);
                        // Cập nhật bản ghi có sẵn với ảnh mới và trạng thái is_main
                        $product->productAdditionalInfo()->update(
                            ['key' => $info['key'], 'content' => $info['content']]
                        );
                    }
                    // Kiểm tra nếu có ID nhưng không có file ảnh
                    elseif (!empty($info['id']) && empty($info['key'])) {
                        // Cập nhật chỉ trường is_main
                        $product->productAdditionalInfo()->where('id', $info['id'])->update(['content' => $info['content']]);
                    }
                    // Kiểm tra nếu không có ID nhưng có file ảnh
                    elseif (empty($info['id']) && !empty($info['key'])) {

                        // Tạo mới bản ghi với ảnh và trạng thái is_main
                        $product->productAdditionalInfo()->create([
                            'key' => $info['key'],
                            'content' => $info['content']
                        ]);
                    }
                }
            }

            return $product;
        }, __("Update product success"), __("Update product error"));
    }

    public function destroy($id)
    {
        return $this->baseActionTransaction(function () use ($id) {
            $data = $this->productService->deleteProduct($id);
            return $data;
        }, __("Delete product success"), __("Delete product error"));
    }
}
