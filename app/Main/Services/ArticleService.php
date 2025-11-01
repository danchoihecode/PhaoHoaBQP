<?php

namespace App\Main\Services;

use App\Models\Article;
use App\Main\Helpers\Response;
use App\Main\Repositories\ArticleRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use function App\Main\Helpers\responseJsonSuccess;
use function App\Main\Helpers\responseJsonFail;

// CRUD aticle

class ArticleService
{
    protected $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function getAllArticles()
    {
        return $this->articleRepository->all();
    }

    public function getArticleById($id)
    {
       return $this->articleRepository->find($id);
    }

    public function createArticle($data)
    {
        return $this->articleRepository->create($data);
    }

    public function updateArticle($id, $data)
    {
        return $this->articleRepository->updateOrCreate(['id' => $id], $data);
    }
    public function deleteArticle($id)
    {
        return $this->articleRepository->delete($id);  
    }
    
}
