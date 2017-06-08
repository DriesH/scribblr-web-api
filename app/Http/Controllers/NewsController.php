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

        $marked_news = $read_news->merge($unread_news)->sortByDesc('id')->values()->all();

        return response()->json([
            self::SUCCESS => true,
            'news' => $marked_news
        ]);
    }


    function markAllAsRead(Request $request) {
        $user = Auth::user();
        $news_items_to_mark_read = News::whereDoesntHave('user', function($query) use($user) {
            $query->where('user_id', $user->id);
        })->get();

        foreach ($news_items_to_mark_read as $news_item) {
            $user->news()->attach($news_item);
        }

        return response()->json([
            self::SUCCESS => true
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
