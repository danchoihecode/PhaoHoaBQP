<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Main\Services\ArticleService;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;


class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index()
    {   
         return $this->baseAction(function(){
            $data = $this->articleService->getAllArticles();
            return $data;
        }, __("Get article success"), __("Get article error"));
    }

    public function show($id)
    {
        return $this->baseAction(function() use ($id) {
            $data = $this->articleService->getArticleById($id);
            return $data;
        }, __("Get article success"), __("Get article error"));
    }

    public function store(StoreArticleRequest $request)
    {
        try {
            return $this->baseActionTransaction(function() use ($request) {
                $data = $this->articleService->createArticle($request->validated());
                return $data;
            }, __("Create article success"), __("Create article error"));
        } catch (\Exception $e) {
            // Return the error message
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }




    public function update(UpdateArticleRequest $request, $id)
    {
        return $this->baseActionTransaction(function() use ($request, $id) {
            $data = $this->articleService->updateArticle($id, $request->validated());
            return $data;
        }, __("Update article success"), __("Update article error"));
    }

    public function destroy($id)
    {
        return $this->baseActionTransaction(function() use ($id) {
            $data = $this->articleService->deleteArticle($id);
            return $data;
        }, __("Delete article success"), __("Delete article error"));
    }

}
