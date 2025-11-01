<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Main\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        return $this->baseAction(function(){
            $data = $this->categoryService->getAllCategories();
            return $data;
        }, __("Get category success"), __("Get category error"));
    }
    
    public function show($id)
    {
        return $this->baseAction(function() use ($id) {
            $data = $this->categoryService->getCategoryById($id);
            return $data;
        }, __("Get category success"), __("Get category error"));
    }

    public function store(StoreCategoryRequest $request)
    {
        return $this->baseActionTransaction(function() use ($request) {
            $data = $this->categoryService->createCategory($request->validated());
            return $data;
        }, __("Create category success"), __("Create category error"));

    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        return $this->baseActionTransaction(function() use ($request, $id) {
            $data = $this->categoryService->updateCategory($id, $request->validated());
            return $data;
        }, __("Update category success"), __("Update category error"));
    }

    public function destroy($id)
    {
        return $this->baseActionTransaction(function() use ($id) {
            $data = $this->categoryService->deleteCategory($id);
            return $data;
        }, __("Delete category success"), __("Delete category error"));
    }
}
