<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Post;

class BookController extends Controller
{
    /*
    | Get all books.
    */
    function index()
    {
        // do something...
    }

    /*
    | Delete a book by shortId.
    | @params {$shortId}
    */
    function delete($shortId)
    {
        // do something...
    }

    /*
    | Create a new book.
    */
    function new()
    {
        $user = Auth::user();
        $memories = self::getAllUserMemories($user);
        $quotes = self::getAllUserQuotes($user);

        $not_printed_memories = $memories->where('is_printed', false)->count();
        $not_printed_quotes = $quotes->where('is_printed', false)->count();

        $can_create_book = self::checkIfUserCanCreateBook($memories, $quotes);
        $book_is_unique = self::checkForUniqueBookPossibility($not_printed_memories, $not_printed_quotes);

        if ($book_is_unique) {
            $book = self::createUniqueBook(); //FIXME
        }
        elseif ($can_create_book) {
            $book = self::createBookWithAlreadyPrintedPosts(); //FIXME
        }
        else {
            return 'not enough quotes to make book'; //FIXME
        }

        return response()->json([
            'test' => ''
        ]);
    }

    private function getAllUserMemories($user) {
        return Post::whereHas('child', function($query) use($user) {
            $query->where('children.user_id', $user->id);
        })
        ->where('is_memory', true)
        ->get();
    }

    private function getAllUserQuotes($user) {
        return Post::whereHas('child', function($query) use($user) {
            $query->where('children.user_id', $user->id);
        })
        ->where('is_memory', false)
        ->get();
    }

    private function checkForUniqueBookPossibility($not_printed_memories, $not_printed_quotes) {
        $posible_new_pages = ($not_printed_quotes * 1) + ($not_printed_memories * 2);

        if ($posible_new_pages >=  self::PAGES_PER_BOOK) {
            return true;
        }
        return false;
    }

    private function checkIfUserCanCreateBook($memories, $quotes) {
        $posible_pages = ($not_printed_quotes * 1) + ($not_printed_memories * 2);

        if ($posible_pages >=  self::PAGES_PER_BOOK) {
            return true;
        }
        return false;
    }

    /*
    | Get a specific book by shortId.
    | @params {$shortId}
    */
    function getBook($shortId)
    {
        // do something...
    }

}
