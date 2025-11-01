<?php

namespace App\Main\Services;

use App\Models\Product;
use App\Main\Helpers\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Main\Repositories\ProductRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class ProductService
{
    protected $response;
    protected $productRepository;


    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts($perPage)
    {
        return $this->productRepository->getAllProducts($perPage);
    }

    public function getProductById($id)
    {
        return $this->productRepository->find($id);
    }
    public function createProduct($data)
    {
        // dd($this->productRepository->create($data));
        $product = $this->productRepository->create($data);
        $this->attachCategories($product, $data['categories']);
        return $product;
    }

    public function getListProducts($perPage = 10) {}

    public function getFirstTwentyProducts()
    {
        $products = $this->productRepository->getFirstTwentyProducts();

        $formattedProducts = $products->map(function ($product) {
            $rating = round($product->reviews->avg('rating'), 2);
            $rating = $rating == 0 ? null : $rating;
            $price_format = number_format($product->price, 0, ',', '.');
            $discount_price_format = number_format($product->discount_price, 0, ',', '.');

            return [
                'name' => $product->name,
                'slug' => $product->slug,
                'main_image_url' => $this->formatImageUrl($product->images->first()->img_url ?? null), // Lấy ảnh chính
                'average_rating' => $rating, // Tính điểm đánh giá trung bình
                'price' => $product->price,
                'price_format' => $price_format,
                'discount_price' => $product->discount_price,
                'discount_price_format' => $discount_price_format,
            ];
        });

        return $formattedProducts;
    }

    public function moveImage($image)
    {
        $timestamp = time();
        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $slugifiedName = Str::slug($originalName);
        $extension = $image->extension();
        $newFilename = $timestamp . '-' . $slugifiedName . '.' . $extension;

        // Move and store the file in the public 'images' directory
        $image->move(public_path('images'), $newFilename);
        $imagePath = 'images/' . $newFilename;
        return $imagePath;
    }

    public function deleteImage($imagePath)
    {
        // dd($imagePath);

        if (File::exists(public_path($imagePath))) {
            File::delete(public_path($imagePath));

            // Kiểm tra sau khi cố gắng xóa để đảm bảo tệp đã được xóa
            if (!File::exists(public_path($imagePath))) {
                return 'File successfully deleted.';
            } else {
                return 'File could not be deleted.';
            }
        } else {
            // Tệp không tồn tại
            return 'File does not exist.';
        }
    }

    // api fe
    public function getProductDetailsBySlug($slug)
    {
        $product = $this->productRepository->getProductDetailsBySlug($slug);
        if (!$product) {
            return null;
        }

        $reviewData = $product->reviews()
            ->selectRaw('AVG(rating) as average_rating, COUNT(*) as total_reviews')
            ->first();
        $rating = round($product->reviews->avg('rating'), 2);
        $rating = $rating == 0 ? null : $rating;
        $price_format = number_format($product->price, 0, ',', '.');
        $discount_price_format = number_format($product->discount_price, 0, ',', '.');
        $result = [
            'id' => $product->id,
            'category_name' => $product->categories->first()->name ?? null,
            'name' => $product->name,
            'youtube_url' => $product->youtube_url,
            'price' => $product->price,
            'price_format' => $price_format,
            'discount_price' => $product->discount_price,
            'discount_price_format' => $discount_price_format,
            'sku' => $product->sku,
            'description' => stripslashes($product->description),
            'images' => $product->images->map(function ($img) {
                return ['img_url' => $this->formatImageUrl($img->img_url), 'is_main' => $img->is_main];
            }),
            'additional_info' => $product->productAdditionalInfo->map(function ($info) {
                return ['content' => $info->content, 'key' => $info->key];
            }),
            // 'rating' => round($reviewData->average_rating , 2),
            'rating' => $rating,
            'total_reviews' => $reviewData->total_reviews ?? 0
        ];

        return $result;
    }


    //api admin
    public function getProductDetailsById($id)
    {
        $product = $this->productRepository->getProductDetailsById($id);
        if (!$product) {
            return null;
        }
        // dd($product);
        $price_format = number_format($product->price, 0, ',', '.');
        $discount_price_format = number_format($product->discount_price, 0, ',', '.');
        $result = [
            // 'category_name' => $product->categories->first()->name ?? null,
            'categories' => $product->categories->map(function ($category) {
                return ['id' => $category->id, 'name' => $category->name];
            }),
            'name' => $product->name,
            'youtube_url' => $product->youtube_url,
            'price' => $product->price,
            'price_format' => $price_format,
            'discount_price' => $product->discount_price,
            'discount_price_format' => $discount_price_format,
            'quantity' => $product->quantity,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'serial_number' => $product->serial_number,
            'stock_status' => $product->stock_status,
            'publish' => $product->publish,
            'description' => stripslashes($product->description),
            'images' => $product->images->map(function ($img) {
                return ['id'=> $img->id, 'img_url' => $this->formatImageUrl($img->img_url), 'is_main' => $img->is_main];
            }),
            'additional_info' => $product->productAdditionalInfo->map(function ($info) {
                return ['id'=> $info->id, 'content' => $info->content, 'key' => $info->key];
            }),

        ];

        return $result;
    }

    public function formatImageUrl($imageUrl)
    {
        // Lấy base URL từ request hiện tại
        if ($imageUrl && !Str::startsWith($imageUrl, 'http')) {
            return url($imageUrl);
        }
        return $imageUrl;
    }

    public function attachCategories(Product $product, array $categories)
    {
        $product->categories()->sync($categories);
    }

    public function updateProduct($id, $data)
    {
        // dd($data);
        return $this->productRepository->updateProduct($id, $data);
    }

    public function updateProductCategories($id, $categoryIds)
    {
        return $this->productRepository->updateProductCategories($id, $categoryIds);
    }


    public function deleteProduct($id)
    {
        return $this->productRepository->delete($id);
    }

    public function getProductsByCategorySlug($categorySlug)
    {

        $products = $this->productRepository->getProductsByCategorySlug($categorySlug);
        // dd($products);



        $formattedProducts = $products->map(function ($product) {
            $rating = round($product->reviews->avg('rating'), 2);
            $rating = $rating == 0 ? null : $rating;
            $price_format = number_format($product->price, 0, ',', '.');
            $discount_price_format = number_format($product->discount_price, 0, ',', '.');

            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'main_image_url' => $this->formatImageUrl($product->images->first()->img_url ?? null),
                'average_rating' => $rating,
                'price' => $product->price,
                'price_format' => $price_format,
                'discount_price' => $product->discount_price,
                'discount_price_format' => $discount_price_format,

            ];
        });
        return $formattedProducts;
    }
}
