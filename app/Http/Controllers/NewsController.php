<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use App\News;

class NewsController extends Controller
{

    function getAllNews() {
        $user = Auth::user();

        $all_news = News::all();
        $read_news = $user->with('news')->first()->news;

        $read_news->map(function ($read_news) {
            $read_news->read = true;
            return $read_news;
        });

        $unread_news = $all_news->diff($read_news);
        $unread_news->map(function ($unread_news) {
            $unread_news->read = false;
            return $unread_news;
        });

        $marked_news = $read_news->merge($unread_news)->sortByDesc('id')->values()->all(); //FIXME ------------------------------

        return response()->json([
            self::SUCCESS => true,
            'news' => $marked_news
        ]);
    }


    function markAsRead($news_id) {
        $user = Auth::user();
        $news_to_mark_read = News::find($news_id);

        if (!$news_to_mark_read) {
            return self::RespondModelNotFound();
        }

        //check if already read
        $already_read = $user->whereHas('news', function($query) use($news_to_mark_read) {
            $query->where('news.id', $news_to_mark_read->id);
        })
        ->first();

        if ($already_read) {
            return response()->json([
                self::SUCCESS => false,
                self::ERROR_TYPE => self::ERROR_TYPE_RELATION_ALREADY_EXISTS
            ], 400);
        }

        $user->news()->attach($news_to_mark_read);
        return response()->json([
            self::SUCCESS => true,
            'read_news' => $news_to_mark_read
        ]);
    }

    function getUnreadCount() {
        $all_news_count = News::all()->count();
        $user_read_news_count = Auth::user()->news()->count();

        $unread_count = $all_news_count - $user_read_news_count;
        return response()->json([
            self::SUCCESS => true,
            'unread_count' => $unread_count
        ]);
    }
}
