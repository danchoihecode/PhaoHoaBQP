<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ContactDetail;
use App\Models\SocialMedia;
use Illuminate\Http\Request;

class FooterManagementController extends Controller
{
    public function index()
    {
        // Lấy các bài viết ở cột 1 và cột 2
        $columnOneArticles = Article::where('column', 1)->select('id')->get();
        $columnTwoArticles = Article::where('column', 2)->select('id')->get();



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
            'column_1' => $columnOneArticles,
            'column_2' => $columnTwoArticles,
            'contactDetails' => $contactDetails,
            'socialMedias' => $socialMedias
        ];

        return $this->baseAction(function () use ($result) {

            return $result;
        }, __("Get footer success"), __("Get footer error"));
    }


    public function upsertDetails(Request $request)
    {
        $request->validate([
            'articles' => 'required|array',
            'articles.*.id' => 'required|integer',
            'articles.*.column' => 'nullable|integer',
            'details' => 'required|array',
            'details.*.phone_number' => 'nullable|string|max:20',
            'details.*.operation_hours' => 'nullable|string|max:255',
            'details.*.fee' => 'nullable|string|max:50',
            'details.*.type' => 'nullable|string|max:50',
            'social_media' => 'required|array',
            'social_media.*.platform' => 'nullable|string|max:50',
            'social_media.*.likes' => 'nullable|string|max:255',

        ]);

        return $this->baseActionTransaction(function () use ($request) {



            Article::query()->update(['column' => null]);
            foreach ($request->articles as $article) {
                Article::where('id', $article['id'])->update(['column' => $article['column']]);
            }

            ContactDetail::query()->delete();
            foreach ($request->details as $detail) {
                ContactDetail::create($detail);
            }

            SocialMedia::query()->delete();
            foreach ($request->social_media as $social) {
                SocialMedia::create($social);
            }

            return "no content";
        }, __("Create article success"), __("Create article error"));
    }
}
