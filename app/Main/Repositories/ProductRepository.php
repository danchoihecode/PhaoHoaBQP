<?php

namespace App\Main\Repositories;

use App\Main\BaseResponse\BaseRepository;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductRepository extends BaseRepository
{
    public function getModel()
    {
        return Product::class;
    }

    public function getAllProducts($perPage)
    {

        return Product::with('categories')->paginate($perPage);
    }

    public function getListProducts($perPage = 10)
    {
        return Product::with(['images' => function ($query) {
            $query->where('is_main', true);
        }])->select('name', 'stock_status', 'price', 'discount_price')
            ->paginate($perPage);
    }

    public function updateProductCategories($id, $categoryIds)
    {
        $product = $this->find($id);
        if ($product) {
            $product->categories()->sync($categoryIds);
            return true;
        }
        return false;
    }

    public function updateProduct($id, $data)
    {
        // dd($this->findOrFail($id));
        // Lấy sản phẩm cần cập nhật
        $product = $this->findOrFail($id);
        // dd(['id' => $id]);
        // $product = Product::findOrFail($id);
        // dd($product);
        // dd($data['image_url']);
        // dd($product->id);
        // dd();

        // \Illuminate\Support\Facades\Log::info('Product image URL:', ['image_url' => $product->image_url]);
        // Kiểm tra và xóa ảnh cũ nếu có dữ liệu mới
        // dd($data['image_url']);
        // dd($product->image_url);
        $oldImageUrl = $product->image_url;

        if (isset($data['image_url']) && $oldImageUrl && file_exists(public_path($oldImageUrl))) {
            unlink(public_path($oldImageUrl));
        }
        // dd($product);
        // Cập nhật hoặc tạo mới dữ liệu sản phẩm
        return $this->updateOrCreate($id, $data);
    }

    public function getFirstTwentyProducts()
    {
        $this->getModel(); // Reset model instance
        return $this->model->with([
            'images' => function ($query) {
                $query->where('is_main', true);
            },
            'reviews',
        ])->take(20)->where('publish', 1)->get();
    }


    public function getProductDetailsBySlug($slug)
    {
        $this->getModel();
        $product = $this->model->with([
            'categories',
            'images',
            'productAdditionalInfo'
        ])->where('slug', $slug)->where('publish', 1)->first();
        // dd($product);
        return $product;
    }

    public function getProductDetailsById($id)
    {
        $this->getModel();
        $product = $this->model->with([
            'categories',
            'images',
            'productAdditionalInfo'
        ])->where('id', $id)->first();
        // dd($product);
        return $product;
    }

    public function getProductDetailsBySlugV2($slug)
    {
        $product = $this->findWhere([['slug', '=',  $slug]]);
        dd($product);
    }
    public function has(string $name)
    {
        $this->has($name);
    }

    public function get(string $name)
    {
        $this->get($name);
    }

    public function set(string $name, string $value)
    {
        $this->set($name, $value);
    }

    public function clear(string $name) {}

    public function getProductsByCategorySlug($categorySlug)
    {


        $this->getModel(); // Reset model instance
        return $this->model->with([
            'images' => function ($query) {
                $query->where('is_main', true);
            },
            'reviews',
        ])->whereHas('categories', function ($query) use ($categorySlug) {
            $query->where('slug', $categorySlug);
        })->where('publish', 1)->get();

    }
}
