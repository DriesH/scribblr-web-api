<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Book;
use Auth;

class StatsController extends Controller
{
    function getAllStats(Request $request) {
        $user = Auth::user();

        $all_memories = Post::wherehas('child', function($query) use($user){
            $query->where('children.user_id', $user->id);
        })
        ->get();

        $memory_count = $all_memories->count();

        $shared_count = $all_memories->where('is_shared', true)->count();

        $printed_memories_count = $all_memories->where('is_printed', true)->count();

        $book_count = Book::where('user_id', $user->id)->count();

        return response()->json([
            self::SUCCESS => true,
            'memory_count' => $memory_count,
            'book_count' => $book_count,
            'shared_count' => $shared_count,
            'printed_memories_count' => $printed_memories_count
        ]);
    }
}
