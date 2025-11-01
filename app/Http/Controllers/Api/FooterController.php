<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ContactDetail;
use App\Models\SocialMedia;
use Illuminate\Http\Request;

class FooterController extends Controller
{
    //
    public function index()
    {
        // Lấy các bài viết ở cột 1 và cột 2
        $columnOneArticles = Article::where('column', 1)->select('title', 'slug')->get();
        $columnTwoArticles = Article::where('column', 2)->select('title', 'slug')->get();

        $modifiedColumnOneArticles = $columnOneArticles->map(function ($article) {
            $article->slug = url("articles/{$article->slug}");
            return $article;
        });
        // dd($modifiedColumnOneArticles);
        $modifiedColumnTwoArticles = $columnTwoArticles->map(function ($article) {
            $article->slug = url("articles/{$article->slug}");
            return $article;
        });

        $contactDetails = ContactDetail::all()->groupBy('type')->map(function ($items) {
            return $items->map(function ($item) {
                return [
                    'phone_number' => $item->phone_number,
                    'operation_hours' => $item->operation_hours,
                    'fee' => $item->fee
                ];
            });
        });

        $socialMedias = SocialMedia::select('platform', 'likes')->get();

        // Đóng gói và trả về kết quả
        $result = [
            'column_1' => $modifiedColumnOneArticles,
            'column_2' => $modifiedColumnTwoArticles,
            'contactDetails' => $contactDetails,
            'socialMedias' => $socialMedias
        ];


        return $this->baseAction(function() use($result) {

            return $result;
        }, __("Get footer success"), __("Get footer error"));
    }
}
