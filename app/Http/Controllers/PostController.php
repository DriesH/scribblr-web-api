<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Child;
use App\Font;
use App\Classes\ShortIdGenerator;
use Validator;
use ColorThief\ColorThief;
use Auth;
use Image;
use Illuminate\Support\Str;
use App\Book_Post;

class PostController extends Controller
{
    /*
    | Get all quotes for user
    */
    function getAllPosts() {
        $userId = Auth::user()->id;
        $posts = Post::whereHas('child', function($query) use($userId) {
            $query->where('children.user_id', $userId);
        })
        ->with('child')
        ->get();

        if (!$posts) {
            return self::RespondModelNotFound();
        }

        return response()->json([
            self::SUCCESS => true,
            'posts' => $posts
        ]);

    }


    /*
    | Create a new quote.
    */
    function newQuote(Request $request, ShortIdGenerator $shortIdGenerator, $childShortId) {
        $validator = Validator::make($request->all(), [
            'quote' => self::REQUIRED . '|max:100',
            'font_type' => self::REQUIRED,
            'img_original' => self::REQUIRED . '|url',
            'img_baked' => self::REQUIRED . '|image'
        ]);

        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }

        //check if child belongs to current user
        $userId = Auth::user()->id;
        $child = Child::where('short_id', $childShortId)->where('user_id', $userId)->first();
        if (!$child) {
            return self::RespondModelNotFound();
        }

        $quote = new Post();
        do {
            $quoteShortId = $shortIdGenerator->generateId(8);
        } while ( count( Post::where('short_id', $quoteShortId)->first()) >= 1 );
        $quote->short_id = $quoteShortId;
        $quote->quote = $request->quote;
        $quote->child_id = $child->id;
        if($resp = self::addFontType($quote, $request->font_type)) return $resp;
        $quote->is_memory = false;
        $quote->save();

        //image
        if($resp = self::addPostOriginal($quote, $request->img_original)) return $resp;

        self::addQuoteBaked($quote, $request->img_baked);

        return response()->json([
            self::SUCCESS => true,
            'quote' => $quote,
            self::ACHIEVEMENT => self::checkAchievementProgress(self::ADD_SCRIBBLE)
        ]);

    }

    function newStory(Request $request, ShortIdGenerator $shortIdGenerator, $childShortId) {
        $validator = Validator::make($request->all(), [
            'story' => self::REQUIRED . '|max:1000',
            'img_baked' => self::REQUIRED .'|url',
        ]);

        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }

        //check if child belongs to current user
        $userId = Auth::user()->id;
        $child = Child::where('short_id', $childShortId)->where('user_id', $userId)->first();
        if (!$child) {
            return self::RespondModelNotFound();
        }

        $memory = new Post();
        do {
            $memoryShortId = $shortIdGenerator->generateId(8);
        } while ( count( Post::where('short_id', $memoryShortId)->first()) >= 1 );
        $memory->lqip = self::getSmallSizeImage($img_baked);
        $memory->short_id = $memoryShortId;
        $memory->child_id = $child->id;
        $memory->story = $request->story;
        $memory->is_memory = true;
        $memory->save();

        //image
        if($resp = self::addMemoryBaked($memory, $request->img_original)) return $resp;

        return response()->json([
            self::SUCCESS => true,
            'memory' => $memory,
            self::ACHIEVEMENT => self::checkAchievementProgress(self::ADD_SCRIBBLE)
        ]);

    }

    private function addFontType($quote, $font_type) {
        $font = Font::where('name', $font_type)->first();

        if (!$font) {
            return self::RespondModelNotFound();
        }

        $quote->font_id = $font->id;
        return;
    }

    private function addPostOriginal($post, $img_original){
        if (!@getimagesize($img_original)) {
            return response()->json([
                self::SUCCESS => false,
                self::ERROR_TYPE => self::ERROR_TYPE_IMAGE_NOT_FOUND
            ]);
        }

        $img_original_url_id = hash_hmac('sha256', Str::random(40), config('app.key'));

        $post->clearMediaCollection('original');

        $post->addMediaFromUrl($img_original)
        ->withCustomProperties(['url_id' => $img_original_url_id])
        ->toMediaLibrary('original');

        $post->img_original_url_id = $img_original_url_id;

        $post->save();
        return;
    }

    private function addMemoryBaked($post, $img_baked){
        if (!@getimagesize($img_baked)) {
            return response()->json([
                self::SUCCESS => false,
                self::ERROR_TYPE => self::ERROR_TYPE_IMAGE_NOT_FOUND
            ]);
        }

        $quote->lqip = self::getSmallSizeImage($img_baked);
        $img_baked_url_id = hash_hmac('sha256', Str::random(40), config('app.key'));

        $post->clearMediaCollection('baked');

        $post->addMediaFromUrl($img_baked)
        ->withCustomProperties(['url_id' => $img_baked_url_id])
        ->toMediaLibrary('baked');

        $post->img_baked_url_id = $img_baked_url_id;

        $post->save();
        return;
    }

    private function addQuoteBaked($quote, $img_baked){
        $quote->lqip = self::getSmallSizeImage($img_baked);

        $img_baked_url_id = hash_hmac('sha256', Str::random(40), config('app.key'));

        $post->clearMediaCollection('baked');

        $quote->addMedia($img_baked)
        ->withCustomProperties(['url_id' => $img_baked_url_id])
        ->toMediaLibrary('baked');

        $quote->img_baked_url_id = $img_baked_url_id;
        $quote->save();
        return;
    }

    private function getSmallSizeImage($image) {
        return Image::make($image)
        ->resize(5, null, function ($constraint) {
            $constraint->aspectRatio();
        })
        ->encode('data-url')
        ->encoded;
        return;
    }


    function editQuote(Request $request, $childShortId, $quoteShortId) {
        $userId = Auth::user()->id;
        $quote = Post::whereHas('child', function($query) use($userId) {
            $query->where('children.user_id', $userId);
        })
        ->where('short_id', $quoteShortId)
        ->first();

        if (!$quote) {
            return self::RespondModelNotFound();
        }

        $validator = Validator::make($request->all(), [
            'quote' => self::REQUIRED . '|max:100',
            'font_type' => self::REQUIRED,
            'img_original' => self::REQUIRED . '|url',
            'img_baked' => self::REQUIRED . '|image'
        ]);

        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }

        $quote->quote = $request->quote;
        if($resp = self::addFontType($quote, $request->font_type)) return $resp;
        $quote->save();

        //image

        if($resp = self::addPostOriginal($quote, $request->img_original)) return $resp;

        self::addQuoteBaked($quote, $request->img_baked);

        return response()->json([
            self::SUCCESS => true,
            'quote' => $quote
        ]);
    }

    function editStory(Request $request, $childShortId, $memoryShortId) {
        $userId = Auth::user()->id;
        $memory = Post::whereHas('child', function($query) use($userId) {
            $query->where('children.user_id', $userId);
        })
        ->where('short_id', $memoryShortId)
        ->first();

        if (!$memory) {
            return self::RespondModelNotFound();
        }

        $validator = Validator::make($request->all(), [
            'story' => self::REQUIRED . '|max:1000',
            'img_baked' => 'url',
        ]);

        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }

        $memory->story = $request->story;
        $memory->save();

        //image
        if ($request->img_baked) {
            if($resp = self::addMemoryOriginal($memory, $request->img_original)) return $resp;
        }

        return response()->json([
            self::SUCCESS => true,
            'memory' => $memory,
            self::ACHIEVEMENT => self::checkAchievementProgress(self::ADD_SCRIBBLE)
        ]);

    }

    /*
    | Delete a quote by shortId.
    | @params {$shortId}
    */
    function delete($childShortId, $postShortId) {
        $userId = Auth::user()->id;
        $postToDelete = Post::whereHas('child', function($query) use($userId) {
            $query->where('children.user_id', $userId);
        })
        ->where('short_id', $postShortId)
        ->first();

        if (!$postToDelete) {
            return self::RespondModelNotFound();
        }

        $book_posts_to_turn_null = Book_Post::where('post_id', $postToDelete->id)->get();

        foreach ($book_posts_to_turn_null as $post) {
            $post->post_id = null;
        }

        $postToDelete->deletePreservingMedia();

        return response()->json([
            self::SUCCESS => true
        ]);
    }

    function getMainColor($image_url) {
        $dominantColor_rgb = ColorThief::getColor($image_url, 100);

        $dominantColor_hex = sprintf("#%02x%02x%02x", $dominantColor_rgb[0], $dominantColor_rgb[1], $dominantColor_rgb[2]);

        return $dominantColor_hex;
    }

    function getPostOriginalImage(Request $request, $childShortId, $postShortId, $img_original_url_id) {
        $post = Post::where('short_id', $postShortId)->where('img_original_url_id', $img_original_url_id)->first();

        if (!$post || !$post->img_original_url_id) {
            return response()->json([
                self::SUCCESS => false,
                self::ERROR_TYPE => self::ERROR_TYPE_IMAGE_NOT_FOUND
            ]);
        }

        return Image::make($post->getMedia('original')[0]
        ->getPath())
        ->response()
        ->header('Cache-Control', 'private, max-age=864000');
    }

    function getPostBakedImage(Request $request, $childShortId, $quoteShortId, $img_baked_url_id) {
        $post = Post::where('short_id', $quoteShortId)->where('img_baked_url_id', $img_baked_url_id)->first();

        if (!$post) {
            return response()->json([
                self::SUCCESS => false,
                self::ERROR_TYPE => self::ERROR_TYPE_IMAGE_NOT_FOUND
            ]);
        }

        return Image::make($post->getMedia('baked')[0]
        ->getPath())
        ->response()
        ->header('Cache-Control', 'private, max-age=864000');
    }
}
