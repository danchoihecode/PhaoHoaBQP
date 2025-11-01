<?php

namespace App\Main\Repositories;

use App\Main\BaseResponse\BaseRepository;
use App\Models\Article;

class ArticleRepository extends BaseRepository
{
    public function getModel()
    {
        return Article::class;
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

    public function clear(string $name)
    {
    }
}