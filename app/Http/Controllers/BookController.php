<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Post;
use stdClass;
use App\Child;

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
    function generateBook(Request $request)
    {
        $user = Auth::user();
        if ($request->c) {
            $child = Child::where('short_id', $request->c)
            ->whereHas('user', function($query) use($user) {
                $query->where('users.id', $user->id);
            })
            ->first();

            if (!$child) {
                return self::RespondModelNotFound();
            }
            else {
                $memories = self::getAllChildMemories($child);
                $quotes = self::getAllChildQuotes($child);
            }
        }
        else {
            $memories = self::getAllUserMemories($user);
            $quotes = self::getAllUserQuotes($user);
        }



        $not_printed_memories = $memories->where('is_printed', false);
        $not_printed_quotes = $quotes->where('is_printed', false);

        $already_printed_memories = $memories->where('is_printed', true);
        $already_printed_quotes = $quotes->where('is_printed', true);

        $can_create_book = self::checkIfUserCanCreateBook($memories, $quotes);
        $book_is_unique = self::checkForUniqueBookPossibility($not_printed_memories, $not_printed_quotes);

        if ($book_is_unique) {
            $book = self::createUniqueBook($not_printed_quotes, $not_printed_memories);
        }
        elseif ($can_create_book) {
            $book = self::createBookWithAlreadyPrintedPosts($not_printed_quotes, $not_printed_memories, $already_printed_quotes, $already_printed_memories);
        }
        else {
            if (count($memories) > 0 || count($quotes) > 0) {
                $book = self::createBookWithEmptyPages($not_printed_quotes, $not_printed_memories, $already_printed_quotes, $already_printed_memories);
            }
            else {
                return response()->json([
                    self::SUCCESS => false,
                    self::ERROR_TYPE => 'no_posts',
                    self::ERROR_MESSAGE => 'You have no memories to make a book yet.'
                ]);
            }
        }

        $all_left_over = $not_printed_memories->merge($not_printed_quotes)
                                            ->merge($already_printed_memories)
                                            ->merge($already_printed_quotes);

        return response()->json([
            self::SUCCESS => true,
            'left_over' => $book[1],
            'book' => $book[0],
            'is_unique' => $book_is_unique
        ]);
    }

    private function createBookWithEmptyPages($not_printed_quotes, $not_printed_memories, $already_printed_quotes, $already_printed_memories) {
        $not_printed_posts = $not_printed_quotes->merge($not_printed_memories);
        $already_printed_posts = $already_printed_quotes->merge($already_printed_memories);

        $all_posts = $not_printed_posts->merge($already_printed_posts);

        $all_posts = $all_posts->shuffle();

        $remaining_quotes = $all_posts->where('is_memory', false)->count();
        $remaining_memories = $all_posts->where('is_memory', true)->count();

        $empty_fill_object = new stdClass();

        $page_counter = 0;
        $book = [];

        if ($remaining_quotes % 2 != 0) {
            $last_post = $all_posts->reverse()->first();
            if ($last_post->is_memory) {
                $quote_to_push = $all_posts->where('is_memory', false)->first();
                $all_posts = $all_posts->except($quote_to_push->id);
                $all_posts->push($quote_to_push);
            }
        }

        while (count($all_posts) > 0) {
            $current_page_block = [];

            $post_to_add = $all_posts->first();

            if ($post_to_add->is_memory) {
                self::addPageToCurrentBlock($current_page_block, $post_to_add, $all_posts);

                $remaining_memories--;
                $page_counter += 2;
            }
            else {
                if ($remaining_quotes > 1) {
                    //Add first quote
                    self::addPageToCurrentBlock($current_page_block, $post_to_add, $all_posts);
                    $quote2 = $all_posts->where('is_memory', false)->first();
                    self::addPageToCurrentBlock($current_page_block, $quote2, $all_posts);

                    $remaining_quotes -= 2;
                    $page_counter += 2;
                }
                else {
                    self::addPageToCurrentBlock($current_page_block, $post_to_add, $all_posts);
                    array_push($current_page_block, $empty_fill_object);
                    $remaining_quotes--;
                    $page_counter += 2;
                }
            }
            array_push($book, $current_page_block);
        }

        if ($page_counter < self::PAGES_PER_BOOK) {
            $amount_pages_to_fill_up = self::PAGES_PER_BOOK - $page_counter;

            for ($i=0; $i < $amount_pages_to_fill_up/2; $i++) {
                array_push($book, [ $empty_fill_object, $empty_fill_object ]);
            }
        }

        $left_over = $all_posts->sortBy('created_at');

        return [$book, $left_over];
    }

    private function createBookWithAlreadyPrintedPosts($not_printed_quotes, $not_printed_memories, $already_printed_quotes, $already_printed_memories) {
        $not_printed_posts = $not_printed_quotes->merge($not_printed_memories);
        $not_printed_posts_random_order = $not_printed_posts->shuffle();

        $already_printed_posts = $already_printed_quotes->merge($already_printed_memories);
        $already_printed_posts_random_order = $already_printed_posts->shuffle();

        $remaining_not_printed_quotes = count($not_printed_quotes);
        $remaining_not_printed_memories = count($not_printed_memories);

        $remaining_already_printed_quotes = count($already_printed_quotes);
        $remaining_already_printed_memories = count($already_printed_memories);

        $checkedSecondPartForOddEven = false;

        $page_counter = 0;
        $book = [];

        //check if amount of not printed quotes is odd and if there's no already printed quotes to fill up the page_counter
        //if so, jsut remove one not printed quote from the list
        if ($remaining_not_printed_quotes % 2 != 0 && $remaining_already_printed_quotes == 0) {
            $post_to_remove = $not_printed_posts_random_order->where('is_memory', false)->first();
            $not_printed_posts_random_order = $not_printed_posts_random_order->except($post_to_remove->id);
        }

        while ($page_counter < self::PAGES_PER_BOOK) {
            $current_page_block = [];

            //use noi printed posts first
            if (count($not_printed_posts_random_order) > 0) {
                $post_to_add = $not_printed_posts_random_order->first();

                if ($post_to_add->is_memory) {
                    self::addPageToCurrentBlock($current_page_block, $post_to_add, $not_printed_posts_random_order);

                    $remaining_not_printed_memories--;
                    $page_counter += 2;
                }
                else {
                    //Add first quote
                    self::addPageToCurrentBlock($current_page_block, $post_to_add, $not_printed_posts_random_order);

                    if ($remaining_not_printed_quotes > 1) {
                        //meaning that there's still at least 1 other NOT printed quote to go along with this one
                        $quote2 = $not_printed_posts_random_order->where('is_memory', false)->first();
                        self::addPageToCurrentBlock($current_page_block, $quote2, $not_printed_posts_random_order);

                        $remaining_not_printed_quotes -= 2;
                    }
                    else {
                        //meaning we have to use 1 ALREADY printed quote to go with this one
                        //this one MUST exist because of the check we've done before
                        $quote2 = $already_printed_posts_random_order->where('is_memory', false)->first();
                        self::addPageToCurrentBlock($current_page_block, $quote2, $not_printed_posts_random_order);

                        $remaining_not_printed_quotes--;
                        $remaining_already_printed_quotes--;
                    }
                    $page_counter += 2;
                }
            }
            else {
                //meaning we have to start taking ALREADY printed posts

                //do the same check as before so we don't run into the same problem but now for already printed quotes
                // this check must be done NOW because the posibility exists that we manipulate the value before we enter this else statement
                if (!$checkedSecondPartForOddEven && $remaining_already_printed_quotes % 2 != 0) {
                    $post_to_remove = $already_printed_posts_random_order->where('is_memory', false)->first();
                    $already_printed_posts_random_order = $already_printed_posts_random_order->except($post_to_remove->id);
                    $checkedSecondPartForOddEven = true;
                }

                $post_to_add = $already_printed_posts_random_order->first();

                if ($post_to_add->is_memory) {
                    self::addPageToCurrentBlock($current_page_block, $post_to_add, $already_printed_posts_random_order);

                    $remaining_already_printed_memories--;
                    $page_counter += 2;
                }
                else {
                    //Add first quote
                    self::addPageToCurrentBlock($current_page_block, $post_to_add, $already_printed_posts_random_order);
                    //get and add second quote of page_counter (there MUST be a second quote because of the odd/even check earlier)
                    $quote2 = $already_printed_posts_random_order->where('is_memory', false)->first();
                    self::addPageToCurrentBlock($current_page_block, $quote2, $already_printed_posts_random_order);

                    $remaining_already_printed_quotes -= 2;
                    $page_counter += 2;
                }
            }
            array_push($book, $current_page_block);
        }

        $left_over = $not_printed_posts_random_order->merge($already_printed_posts_random_order)->sortBy('created_at');

        return [$book, $left_over];



    }

    private function createUniqueBook($not_printed_quotes, $not_printed_memories) {
        $not_printed_posts = $not_printed_quotes->merge($not_printed_memories);
        $posts_random_order = $not_printed_posts->shuffle();
        $remaining_quotes = count($not_printed_quotes);
        $remaining_memories = count($not_printed_memories);
        $page_counter = 0;
        $book = [];

        if ($remaining_quotes % 2 != 0) {
            $post_to_remove = $posts_random_order->where('is_memory', false)->first();
            $posts_random_order = $posts_random_order->except($post_to_remove->id);
        }

        while ($page_counter < self::PAGES_PER_BOOK) {
            $current_page_block = [];

            $post_to_add = $posts_random_order->first();

            if ($post_to_add->is_memory) {
                self::addPageToCurrentBlock($current_page_block, $post_to_add, $posts_random_order);

                $remaining_memories--;
                $page_counter += 2;
            }
            else {
                //Add first quote
                self::addPageToCurrentBlock($current_page_block, $post_to_add, $posts_random_order);
                //get and add second quote of page_counter (there MUST be a second quote because of the odd/even check earlier)
                $quote2 = $posts_random_order->where('is_memory', false)->first();
                self::addPageToCurrentBlock($current_page_block, $quote2, $posts_random_order);

                $remaining_quotes -= 2;
                $page_counter += 2;
            }
            array_push($book, $current_page_block);
        }

        $left_over = $posts_random_order->sortBy('created_at');
        return [$book, $left_over];
    }

    private function addPageToCurrentBlock(&$current_page_block, $post_to_add, &$posts_random_order) {
        $empty_fill_object = new stdClass();

        array_push($current_page_block, $post_to_add);

        if ($post_to_add->is_memory) {
            array_push($current_page_block, $empty_fill_object);
        }

        $posts_random_order = $posts_random_order->except($post_to_add->id);
        return;
    }

    private function getAllChildMemories($child) {
        return Post::whereHas('child', function($query) use($child) {
            $query->where('children.short_id', $child->id);
        })
        ->where('is_memory', true)
        ->with('child')
        ->get();
    }

    private function getAllChildQuotes($child) {
        return Post::whereHas('child', function($query) use($child) {
            $query->where('children.short_id', $child->short_id);
        })
        ->where('is_memory', false)
        ->with('child')
        ->get();
    }

    private function getAllUserMemories($user) {
        return Post::whereHas('child', function($query) use($user) {
            $query->where('children.user_id', $user->short_id);
        })
        ->where('is_memory', true)
        ->with('child')
        ->get();
    }

    private function getAllUserQuotes($user) {
        return Post::whereHas('child', function($query) use($user) {
            $query->where('children.user_id', $user->id);
        })
        ->where('is_memory', false)
        ->with('child')
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
