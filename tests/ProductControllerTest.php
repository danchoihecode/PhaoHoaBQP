<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\API\ProductController;
use App\Main\Services\ProductService;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class ProductControllerTest extends TestCase
{
    protected $productServiceMock;
    protected $productController;
    protected $requestMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->productServiceMock = $this->createMock(ProductService::class);
        $this->productController = new ProductController($this->productServiceMock);
        $this->requestMock = $this->createMock(Request::class);
    }

    public function testGetProductsByCategorySlugCategoryNotFound()
    {
        Category::shouldReceive('where')
            ->with('slug', 'non-existing-slug')
            ->once()
            ->andReturn(null);

        $response = $this->productController->getProductsByCategorySlug('non-existing-slug');
        $this->assertEquals(json_encode(['error' => 'Category not found']), $response->getContent());
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testGetProductsByCategorySlugCategoryFound()
    {
        $categoryMock = $this->createMock(Category::class);
        $categoryMock->method('products')->willReturn(new Collection([
            (object)['id' => 1, 'name' => 'Product 1'],
            (object)['id' => 2, 'name' => 'Product 2'],
        ]));

        Category::shouldReceive('where')
            ->with('slug', 'existing-slug')
            ->once()
            ->andReturn($categoryMock);

        $response = $this->productController->getProductsByCategorySlug('existing-slug');
        $responseData = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('category', $responseData);
        $this->assertArrayHasKey('products', $responseData);
        $this->assertArrayHasKey('total', $responseData);
        $this->assertCount(2, $responseData['products']);
    }
}
