<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Post;
use stdClass;
use App\Child;
use App\Book;
use App\Book_Post;
use Validator;
use App\Classes\ShortIdGenerator;

class BookController extends Controller
{
    /*
    | Get all books.
    */
    function getAllBooks()
    {
        $user = Auth::user();
        $books = Book::where('user_id', $user->id)->get();

        return response()->json([
            self::SUCCESS => true,
            'books' => $books
        ]);
    }

    /*
    | Delete a book by shortId.
    | @params {$shortId}
    */
    function delete($shortId)
    {
        $user = Auth::user();
        $book_to_delete = Book::where('user_id', $user->id)
        ->where('short_id', $shortId)
        ->first();

        if (!$book_to_delete) {
            return self::RespondModelNotFound();
        }

        $book_to_delete->delete();

        return response()->json([
            self::SUCCESS => true
        ]);
    }

    /*
    | Create a new book.
    */
    function generateBook(Request $request)
    {
        $user = Auth::user();
        $is_flip_over = $request->is_flip_over;
        $child = null;

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

        if ($is_flip_over) {
            $book = self::createFlipOver($memories, $quotes);

            if ($child) {
                $all_marked_posts = self::createMarkedPostsWithOtherChildren($book[2], $child, $user);
            }
            else {
                $all_marked_posts = $book[2];
            }

            return response()->json([
                self::SUCCESS => true,
                'all_marked_posts' => $book[2],
                'book' => $book[0],
                'is_unique' => $book[1]
            ]);
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



        $left_over = [];
        foreach ($book[1] as $key => $value) {
            $value->is_used_in_book = 0;
            array_push($left_over, $value);
        }

        $all_marked_posts = array_merge($left_over, $book[2]);

        return response()->json([
            self::SUCCESS => true,
            'all_marked_posts' => $all_marked_posts,
            'book' => $book[0],
            'is_unique' => $book_is_unique
        ]);
    }

    private function createMarkedPostsWithOtherChildren($all_current_marked_posts, $child, $user) {
        $other_children_posts = Post::whereHas('child', function($query) use($user, $child) {
            $query->where('user_id', $user->id)->where('id', '<>', $child->id);
        })
        ->with('child')
        ->get();

        $marked_not_used = $other_children_posts->map(function() {

        });
    }

    private function createFlipOver($memories, $quotes) {
        $book = $memories->merge($quotes)->shuffle()->sortBy('is_printed', false)->take(self::PAGES_PER_BOOK);
        $is_unique = ($book->where('is_printed', true)->count() > 0) ? false : true;

        $marked_as_used = $book->map(function($post) {
            $post->is_used_in_book = 1;
            return $post;
        });

        $all_posts = $memories->merge($quotes);
        $not_used = $all_posts->diff($book);

        $marked_as_not_used = $not_used->map(function($post) {
            $post->is_used_in_book = 0;
            return $post;
        });

        $all_marked_posts = $marked_as_not_used->merge($marked_as_used);

        $book_array = [];
        foreach ($book as $post) {
            array_push($book_array, $post);
        }

        $amount_pages_to_fill_up = self::PAGES_PER_BOOK - count($book_array);

        if ($amount_pages_to_fill_up > 0) {
            $empty_fill_object = new StdClass();
            for ($i=0; $i < $amount_pages_to_fill_up; $i++) {
                array_push($book_array, $empty_fill_object);
            }
        }


        return [$book_array, $is_unique, $all_marked_posts];

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
        $all_marked_posts = [];

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
                self::addPageToCurrentBlock($current_page_block, $post_to_add, $all_posts, $all_marked_posts);

                $remaining_memories--;
                $page_counter += 2;
            }
            else {
                if ($remaining_quotes > 1) {
                    //Add first quote
                    self::addPageToCurrentBlock($current_page_block, $post_to_add, $all_posts, $all_marked_posts);
                    $quote2 = $all_posts->where('is_memory', false)->first();
                    self::addPageToCurrentBlock($current_page_block, $quote2, $all_posts, $all_marked_posts);

                    $remaining_quotes -= 2;
                    $page_counter += 2;
                }
                else {
                    self::addPageToCurrentBlock($current_page_block, $post_to_add, $all_posts, $all_marked_posts);
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

        return [$book, $left_over, $all_marked_posts];
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
        $all_marked_posts = [];

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
                    self::addPageToCurrentBlock($current_page_block, $post_to_add, $not_printed_posts_random_order, $all_marked_posts);

                    $remaining_not_printed_memories--;
                    $page_counter += 2;
                }
                else {
                    //Add first quote
                    self::addPageToCurrentBlock($current_page_block, $post_to_add, $not_printed_posts_random_order, $all_marked_posts);

                    if ($remaining_not_printed_quotes > 1) {
                        //meaning that there's still at least 1 other NOT printed quote to go along with this one
                        $quote2 = $not_printed_posts_random_order->where('is_memory', false)->first();
                        self::addPageToCurrentBlock($current_page_block, $quote2, $not_printed_posts_random_order, $all_marked_posts);

                        $remaining_not_printed_quotes -= 2;
                    }
                    else {
                        //meaning we have to use 1 ALREADY printed quote to go with this one
                        //this one MUST exist because of the check we've done before
                        $quote2 = $already_printed_posts_random_order->where('is_memory', false)->first();
                        self::addPageToCurrentBlock($current_page_block, $quote2, $not_printed_posts_random_order, $all_marked_posts);

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
                    self::addPageToCurrentBlock($current_page_block, $post_to_add, $already_printed_posts_random_order, $all_marked_posts);

                    $remaining_already_printed_memories--;
                    $page_counter += 2;
                }
                else {
                    //Add first quote
                    self::addPageToCurrentBlock($current_page_block, $post_to_add, $already_printed_posts_random_order, $all_marked_posts);
                    //get and add second quote of page_counter (there MUST be a second quote because of the odd/even check earlier)
                    $quote2 = $already_printed_posts_random_order->where('is_memory', false)->first();
                    self::addPageToCurrentBlock($current_page_block, $quote2, $already_printed_posts_random_order, $all_marked_posts);

                    $remaining_already_printed_quotes -= 2;
                    $page_counter += 2;
                }
            }
            array_push($book, $current_page_block);
        }

        $left_over = $not_printed_posts_random_order->merge($already_printed_posts_random_order)->sortBy('created_at');

        return [$book, $left_over, $all_marked_posts];



    }

    private function createUniqueBook($not_printed_quotes, $not_printed_memories) {
        $not_printed_posts = $not_printed_quotes->merge($not_printed_memories);
        $posts_random_order = $not_printed_posts->shuffle();
        $remaining_quotes = count($not_printed_quotes);
        $remaining_memories = count($not_printed_memories);
        $page_counter = 0;
        $book = [];
        $all_marked_posts = [];

        if ($remaining_quotes % 2 != 0) {
            $post_to_remove = $posts_random_order->where('is_memory', false)->first();
            $posts_random_order = $posts_random_order->except($post_to_remove->id);
        }

        while ($page_counter < self::PAGES_PER_BOOK) {
            $current_page_block = [];

            $post_to_add = $posts_random_order->first();

            if ($post_to_add->is_memory) {
                self::addPageToCurrentBlock($current_page_block, $post_to_add, $posts_random_order, $all_marked_posts);

                $remaining_memories--;
                $page_counter += 2;
            }
            else {
                //Add first quote
                self::addPageToCurrentBlock($current_page_block, $post_to_add, $posts_random_order, $all_marked_posts);
                //get and add second quote of page_counter (there MUST be a second quote because of the odd/even check earlier)
                $quote2 = $posts_random_order->where('is_memory', false)->first();
                self::addPageToCurrentBlock($current_page_block, $quote2, $posts_random_order, $all_marked_posts);

                $remaining_quotes -= 2;
                $page_counter += 2;
            }
            array_push($book, $current_page_block);
        }

        $left_over = $posts_random_order->sortBy('created_at');

        return [$book, $left_over, $all_marked_posts];
    }

    private function addPageToCurrentBlock(&$current_page_block, $post_to_add, &$posts_random_order, &$all_marked_posts) {
        $empty_fill_object = new stdClass();


        array_push($current_page_block, $post_to_add);

        if ($post_to_add->is_memory) {
            array_push($current_page_block, $empty_fill_object);
        }

        //add to array and mark as used right before removing it
        $post_to_add->is_used_in_book = 1;
        array_push($all_marked_posts, $post_to_add);

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
            $query->where('children.user_id', $user->id);
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

    function newBook(Request $request) {
        $isForChild = false;

        if (!$request->is_flip_over) {
            $validator = Validator::make($request->all(), [
                'book' => self::REQUIRED . '|array|size:' . self::PAGES_PER_BOOK/2,
                'book.*' => self::REQUIRED . '|array|size:2',
                'book.*.*' => 'present',
                'title' => self::REQUIRED,
                'cover_preset' => self::REQUIRED
            ]);
        }
        else {
            $validator = Validator::make($request->all(), [
                'book' => self::REQUIRED . '|array|size:' . self::PAGES_PER_BOOK,
                'book.*' => 'present',
                'title' => self::REQUIRED,
                'cover_preset' => self::REQUIRED
            ]);
        }

        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }

        $user = Auth::user();

        if ($request->is_flip_over) {
            return self::CreateOrEditFlipOver($request, $user);
        }
        else {
            return self::CreateOrEditBook($request, $user);
        }

    }

    function editBook(Request $request, $shortId) {
        $isForChild = false;
        $user = Auth::user();
        $book = Book::where('short_id', $shortId)
        ->where('user_id', $user->id)
        ->first();

        if (!$book) {
            return self::RespondModelNotFound();
        }

        if (!$book->is_flip_over) {
            $validator = Validator::make($request->all(), [
                'book' => self::REQUIRED . '|array|size:' . self::PAGES_PER_BOOK/2,
                'book.*' => self::REQUIRED . '|array|size:2',
                'book.*.*' => 'present',
                'title' => self::REQUIRED,
                'cover_preset' => self::REQUIRED
            ]);
        }
        else {
            $validator = Validator::make($request->all(), [
                'book' => self::REQUIRED . '|array|size:' . self::PAGES_PER_BOOK,
                'book.*' => 'present',
                'title' => self::REQUIRED,
                'cover_preset' => self::REQUIRED
            ]);
        }


        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }

        if (!$book->is_flip_over) {
            return self::CreateOrEditBook($request, $user, $book->id);
        }
        else {
            return self::CreateOrEditFlipOver($request, $user, $book->id);
        }
    }

    private function CreateOrEditFlipOver($request, $user, $book_id = null) {
        $book = null;
        $child = null;
        if ($book_id) {
            $book = Book::where('user_id', $user->id)->find($book_id);

            if (!$book) {
                return self::RespondModelNotFound();
            }
        }

        $shortIdGenerator = new ShortIdGenerator();
        $post_ids_to_attach_to_new_book = [];

        $all_user_posts = Post::whereHas('child', function($query) use($user) {
            $query->where('children.user_id', $user->id);
        })
        ->pluck('id')
        ->toArray();


        foreach ($request->book as $post) {
            if (!$post || empty($post)) {
                array_push($post_ids_to_attach_to_new_book, self::EMPTY_PAGE);
                continue;
            }

            if (!in_array($post['id'], $all_user_posts)) {
                return self::RespondModelNotFound();
            }

            if (in_array($post['id'], $post_ids_to_attach_to_new_book)) {
                return response()->json([
                    self::SUCCESS => false,
                    self::ERROR_TYPE => 'book_contains_duplicates',
                    self::ERROR_MESSAGE => 'Book contains duplicate posts'
                ]);
            }
            array_push($post_ids_to_attach_to_new_book, $post['id']);
        }

        if (!$book) {
            $book = new Book();
            do {
                $shortId = $shortIdGenerator->generateId(8);
            } while ( count( Book::where('short_id', $shortId)->first()) >= 1 );
            $book->short_id = $shortId;
            $book->user_id = $user->id;
        }
        else {
            Book_Post::where('book_id', $book->id)->delete();
        }
        $book->title = $request->title;
        $book->cover_preset = $request->cover_preset;
        $book->is_flip_over = true;
        $book->save();

        for ($i=1; $i <= count($post_ids_to_attach_to_new_book); $i++) {
            $new_page = new Book_Post();
            $new_page->page_nr = $i;
            $new_page->book_id = $book->id;

            if ($post_ids_to_attach_to_new_book[$i-1] != self::EMPTY_PAGE) {
                $new_page->post_id = $post_ids_to_attach_to_new_book[$i-1];
            }

            $new_page->save();
        }

        return response()->json([
            self::SUCCESS => true,
            'book' => $book,
            self::ACHIEVEMENT => self::checkAchievementProgress(self::ADD_BOOK)
        ]);
    }

    private function CreateOrEditBook($request, $user, $book_id = null) {
        $book = null;
        $child = null;
        if ($book_id) {
            $book = Book::where('user_id', $user->id)->find($book_id);

            if (!$book) {
                return self::RespondModelNotFound();
            }
        }

        $shortIdGenerator = new ShortIdGenerator();
        $post_ids_to_attach_to_new_book = [];

        $all_user_posts = Post::whereHas('child', function($query) use($user) {
            $query->where('children.user_id', $user->id);
        })
        ->pluck('id')
        ->toArray();


        foreach ($request->book as $page_block) {
            foreach ($page_block as $post) {
                if (!$post || empty($post)) {
                    array_push($post_ids_to_attach_to_new_book, self::EMPTY_PAGE);
                    continue;
                }

                if (!in_array($post['id'], $all_user_posts)) {
                    return self::RespondModelNotFound();
                }

                if (in_array($post['id'], $post_ids_to_attach_to_new_book)) {
                    return response()->json([
                        self::SUCCESS => false,
                        self::ERROR_TYPE => 'book_contains_duplicates',
                        self::ERROR_MESSAGE => 'Book contains duplicate posts'
                    ]);
                }

                array_push($post_ids_to_attach_to_new_book, $post['id']);
            }
        }

        if (!$book) {
            $book = new Book();
            do {
                $shortId = $shortIdGenerator->generateId(8);
            } while ( count( Book::where('short_id', $shortId)->first()) >= 1 );
            $book->short_id = $shortId;
            $book->user_id = $user->id;
        }
        else {
            Book_Post::where('book_id', $book->id)->delete();
        }
        $book->title = $request->title;
        $book->cover_preset = $request->cover_preset;
        $book->is_flip_over = false;
        $book->save();

        for ($i=1; $i <= count($post_ids_to_attach_to_new_book); $i++) {
            $new_page = new Book_Post();
            $new_page->page_nr = $i;
            $new_page->book_id = $book->id;

            if ($post_ids_to_attach_to_new_book[$i-1] != self::EMPTY_PAGE) {
                $new_page->post_id = $post_ids_to_attach_to_new_book[$i-1];
            }

            $new_page->save();
        }

        return response()->json([
            self::SUCCESS => true,
            'book' => $book,
            self::ACHIEVEMENT => self::checkAchievementProgress(self::ADD_BOOK)
        ]);
    }

    /*
    | Get a specific book by shortId.
    | @params {$shortId}
    */
    function getBook($shortId)
    {
        $user = Auth::user();
        $book = Book::where('user_id', $user->id)
        ->where('short_id', $shortId)
        ->first();

        if (!$book) {
            return self::RespondModelNotFound();
        }

        $all_user_posts = Post::whereHas('child', function($query) use($user) {
            $query->where('user_id', $user->id);
        })
        ->with('child')
        ->get();

        if ($book->is_flip_over) {
            $book_posts = Book_Post::where('book_id', $book->id)->orderBy('page_nr')->with('post')->with('post.child')->get();
            $chunked_book = [];

            foreach ($book_posts as $page) {
                array_push($chunked_book, $page->post);
            }

            $all_marked_posts = $all_user_posts->map(function($post) use($chunked_book){
                if (in_array($post, $chunked_book)) {
                    $post->is_used_in_book = 1;
                    return $post;
                }
                else {
                    $post->is_used_in_book = 0;
                    return $post;
                }
            });
        }
        else {
            $pages = Book_Post::where('book_id', $book->id)->orderBy('page_nr')->with('post')->with('post.child')->get();
            $formatted_pages = [];
            $empty_fill_object = new StdClass();
            foreach ($pages as $page) {
                if ($page->post) {
                    array_push($formatted_pages, $page->post);
                }
                else {
                    array_push($formatted_pages, $empty_fill_object);
                }
            }

            $chunked_book = array_chunk($formatted_pages, 2);

            $all_marked_posts = $all_user_posts->map(function($post) use($formatted_pages){
                if (in_array($post, $formatted_pages)) {
                    $post->is_used_in_book = 1;
                    return $post;
                }
                else {
                    $post->is_used_in_book = 0;
                    return $post;
                }
            });
        }

        return response()->json([
            self::SUCCESS => true,
            'all_marked_posts' => $all_marked_posts,
            'book' => $book,
            'pages' => $chunked_book
        ]);
    }

    function seenTutorial() {
        $user = Auth::user();
        if (!$user->has_seen_book_tutorial) {
            $user->has_seen_book_tutorial = true;
            $user->save();

            return response()->json([
                self::SUCCESS => true
            ]);
        }
        else {
            return response()->json([
                self::SUCCESS => false,
                self::ERROR_TYPE => 'already_seen_tutorial',
                self::ERROR_MESSAGE => 'User has already seen this tutorial'
            ]);
        }
    }

    function check() {
        $user = Auth::user();

        $posts = Post::whereHas('child', function($query) use($user) {
            $query->where('children.user_id', $user->id);
        })
        ->count();

        $can_create_book = ($posts > 0) ? true : false;

        return response()->json([
            self::SUCCESS => true,
            'can_create_book' => $can_create_book
        ]);
    }

}
