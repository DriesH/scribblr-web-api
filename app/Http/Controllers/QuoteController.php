<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Quote;
use App\Child;
use App\Classes\ShortIdGenerator;
use Validator;
use ColorThief\ColorThief;
use Auth;
use Image;

class QuoteController extends Controller
{
    /*
    | Get all quotes for user
    */
    function getAllQuotes(){
        $userId = Auth::user()->id;
        $quotes = Quote::whereHas('children', function($query) use($userId) {
            $query->where('children.user_id', $userId);
        })
        ->get();

        if (!$quotes) {
            return self::RespondModelNotFound();
        }

        return response()->json([
            'success' => true,
            'quotes' => $quotes
        ]);

    }


    /*
    | Create a new quote.
    */
    function new(Request $request, ShortIdGenerator $shortIdGenerator, $childShortId)
    {
        $validator = Validator::make($request->all(), [
            'quote' => self::REQUIRED,
            'font_size' => self::REQUIRED.'|integer',
            'font_type' => self::REQUIRED,
            'image' => 'image'
        ]);

        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }

        //check if child belongs to current user
        $child = Child::where('short_id', $childShortId)->first();
        if (!$child) {
            return self::RespondModelNotFound();
        }

        $quote = new Quote();
        do {
            $quoteShortId = $shortIdGenerator->generateId(8);
        } while ( count( Quote::where('short_id', $quoteShortId)->first()) >= 1 );
        $quote->short_id = $quoteShortId;
        $quote->quote = $request->quote;
        $quote->font_size = $request->font_size;
        $quote->font_type = $request->font_type;
        $quote->child_id = $child->id;
        if($request->image) $quote->addMedia($request->image);
        $quote->save();

        return response()->json([
            'success' => true,
            'quote' => $quote
        ]);

    }

    /*
    | Delete a quote by shortId.
    | @params {$shortId}
    */
    function delete($childShortId, $quoteShortId)
    {
        $quoteToDelete = Quote::where('short_id', $quoteShortId)->first();
        if (!$quoteToDelete) {
            return self::RespondModelNotFound();
        }
        $quoteToDelete->delete();

        return response()->json([
            'success' => true
        ]);
    }

    function getMainColor() {
        $dominantColor = ColorThief::getColor(public_path() . '/test.jpg', 100);

        return view('test', [
            'color' => $dominantColor
        ]);
    }

    function newQuote(Request $request, $childShortId) {
        $validator = Validator::make($request->all(), [
            'link' => 'url',
        ]);

        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }

        $user = Auth::user();
        $child = Child::where('short_id', $childShortId)->whereHas('user', function($query) use($user) {
            $query->where('users.id', $user->id);
        })->first();

        if (!$child) {
            return self::RespondModelNotFound();
        }

        $imageURL = $request->link;
        try {
            $child->addMediaFromUrl($imageURL)->toMediaLibrary('edited_images');
        } catch (Exception $e) {
            return response()->json([
                self::SUCCESS => false,
                self::ERROR_TYPE => 'failed to download/save image'
            ]);
        }


        return response()->json([
            self::SUCCESS => true,
            'media' => $child->getMedia('edited_images')
        ]);
    }
}
