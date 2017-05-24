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

        $not_printed_memories = $memories->where('is_printed', false);
        $not_printed_quotes = $quotes->where('is_printed', false);

        $already_printed_memories = $memories->where('is_printed', true);
        $already_printed_quotes = $quotes->where('is_printed', true);

        $can_create_book = self::checkIfUserCanCreateBook($memories, $quotes);
        $book_is_unique = self::checkForUniqueBookPossibility($not_printed_memories, $not_printed_quotes);

        if ($book_is_unique) {
            return $book = self::createUniqueBook($not_printed_quotes, $not_printed_memories);
        }
        elseif ($can_create_book) {
            $book = self::createBookWithAlreadyPrintedPosts($not_printed_quotes, $not_printed_memories, $already_printed_quotes, $already_printed_memories);
        }
        else {
            return response()->json([
                self::SUCCESS => false,
                self::ERROR_TYPE => 'not_enough_quotes',
                self::ERROR_MESSAGE => 'You don\'t have enough quotes to make a book yet.'
            ]);
        }

        return response()->json([
            self::SUCCESS => true,
            'book' => $book,
            'is_unique' => $book_is_unique
        ]);
    }

    private function createBookWithAlreadyPrintedPosts($not_printed_quotes, $not_printed_memories, $already_printed_quotes, $already_printed_memories) {
        $not_printed_posts = $not_printed_quotes->merge($not_printed_memories);
        $already_printed_posts = $already_printed_quotes->merge($already_printed_memories);
        
    }

    private function createUniqueBook($not_printed_quotes, $not_printed_memories) {
        $not_printed_posts = $not_printed_quotes->merge($not_printed_memories);
        $posts_random_order = $not_printed_posts->shuffle();
        $remaining_quotes = count($not_printed_quotes);
        $remaining_memories = count($not_printed_memories);
        $page_counter = 0;
        $book = [];

        while ($page_counter < self::PAGES_PER_BOOK) {
            $current_page_block = [];

            if ($page_counter == self::PAGES_PER_BOOK - 2 && $remaining_quotes <= 1) {
                $post_to_add = $posts_random_order->where('is_memory', true)->first();
                self::addPageToCurrentBlock($current_page_block, $post_to_add, $posts_random_order);

                $remaining_memories--;
                $page_counter += 2;
            }
            else {
                $post_to_add = $posts_random_order->first();

                if ($post_to_add->is_memory) {
                    self::addPageToCurrentBlock($current_page_block, $post_to_add, $posts_random_order);

                    $remaining_memories--;
                    $page_counter += 2;
                }
                else {
                    //Add first quote
                    self::addPageToCurrentBlock($current_page_block, $post_to_add, $posts_random_order);
                    //get and add second quote of page_counter
                    $quote2 = $posts_random_order->where('is_memory', false)->first();
                    self::addPageToCurrentBlock($current_page_block, $quote2, $posts_random_order);

                    $remaining_quotes -= 2;
                    $page_counter += 2;
                }
            }
            array_push($book, $current_page_block);
        }
        return [$remaining_memories, $remaining_quotes, $book];
    }

    private function addPageToCurrentBlock(&$current_page_block, $post_to_add, &$posts_random_order) {
        array_push($current_page_block, $post_to_add);
        $posts_random_order = $posts_random_order->except($post_to_add->id);
        return;
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
        $posible_new_pages = (count($not_printed_quotes) * 1) + (count($not_printed_memories) * 2);

        if ($posible_new_pages >=  self::PAGES_PER_BOOK) {
            return true;
        }
        return false;
    }

    private function checkIfUserCanCreateBook($memories, $quotes) {
        $posible_pages = (count($quotes) * 1) + (count($memories) * 2);

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
