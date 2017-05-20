<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Quote;
use App\Child;
use App\Preset;
use App\Font;
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
    function getAllQuotes() {
        $userId = Auth::user()->id;
        $quotes = Quote::whereHas('child', function($query) use($userId) {
            $query->where('children.user_id', $userId);
        })
        ->get();

        if (!$quotes) {
            return self::RespondModelNotFound();
        }

        return response()->json([
            self::SUCCESS => true,
            'quotes' => $quotes
        ]);

    }


    /*
    | Create a new quote.
    */
    function new(Request $request, ShortIdGenerator $shortIdGenerator, $childShortId) {
        $validator = Validator::make($request->all(), [
            'quote' => self::REQUIRED . '|max:300',
            'story' => 'max:1000',
            'font_type' => self::REQUIRED,
            'img_original' => 'required_without:preset|url',
            'img_baked' => self::REQUIRED . '|image',
            'preset' => 'required_without:img_original|integer'
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

        $quote = new Quote();
        do {
            $quoteShortId = $shortIdGenerator->generateId(8);
        } while ( count( Quote::where('short_id', $quoteShortId)->first()) >= 1 );
        $quote->short_id = $quoteShortId;
        $quote->quote = $request->quote;
        if ($request->story) {
            $quote->story = $request->story;
        }
        $quote->child_id = $child->id;
        if($resp = self::addFontType($quote, $request->font_type)) return $resp;
        $quote->save();

        //images
        if ($request->img_original) {
            if($resp = self::addQuoteOriginal($quote, $request->img_original)) return $resp;
        }
        elseif ($request->preset) {
            if($resp = self::addQuotePreset($quote, $request->preset)) return $resp;
        }

        self::addQuoteBaked($quote, $request->img_baked);

        return response()->json([
            self::SUCCESS => true,
            'quote' => $quote,
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

    private function addQuoteOriginal($quote, $img_original){
        if (!@getimagesize($img_original)) {
            return response()->json([
                self::SUCCESS => false,
                self::ERROR_TYPE => self::ERROR_TYPE_IMAGE_NOT_FOUND
            ]);
        }

        $img_original_url_id = sha1($img_original);

        $quote->clearMediaCollection('avatar');

        $quote->addMediaFromUrl($img_original)
        ->withCustomProperties(['url_id' => $img_original_url_id])
        ->toMediaLibrary('original');

        $quote->preset_id = null;
        $quote->img_original_url_id = $img_original_url_id;

        $quote->save();
        return;
    }

    private function addQuoteBaked($quote, $img_baked){
        $quote->lqip = self::getSmallSizeImage($img_baked);

        $img_baked_url_id = sha1($img_baked->getPathName());

        $quote->addMedia($img_baked)
        ->withCustomProperties(['url_id' => $img_baked_url_id])
        ->toMediaLibrary('baked');

        $quote->img_baked_url_id = $img_baked_url_id;
        $quote->save();
        return;
    }

    private function addQuotePreset($quote, $preset_id){
        $preset_exists = Preset::find($preset_id);

        if (!$preset_exists) {
            self::RespondModelNotFound();
        }

        $quote->img_original_url_id = null;
        $quote->preset_id = $preset_id;
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


    function editQuote(Request $request, ShortIdGenerator $shortIdGenerator, $childShortId, $quoteShortId) {
        $userId = Auth::user()->id;
        $quote = Quote::whereHas('child', function($query) use($userId) {
            $query->where('children.user_id', $userId);
        })
        ->where('short_id', $quoteShortId)
        ->first();

        if (!$quote) {
            return self::RespondModelNotFound();
        }

        $validator = Validator::make($request->all(), [
            'quote' => self::REQUIRED . '|max:300',
            'story' => 'max:1000',
            'font_type' => self::REQUIRED,
            'img_original' => 'required_without:preset|url',
            'img_baked' => self::REQUIRED . '|image',
            'preset' => 'required_without:img_original|integer'
        ]);

        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }

        $quote->quote = $request->quote;
        if ($request->story) {
            $quote->story = $request->story;
        }
        if($resp = self::addFontType($quote, $request->font_type)) return $resp;
        $quote->save();

        //images
        if ($request->img_original) {
            if($resp = self::addQuoteOriginal($quote, $request->img_original)) return $resp;
        }
        elseif ($request->preset) {
            if($resp = self::addQuotePreset($quote, $request->preset)) return $resp;
        }

        self::addQuoteBaked($quote, $request->img_baked);

        return response()->json([
            self::SUCCESS => true,
            'quote' => $quote
        ]);
    }

    /*
    | Delete a quote by shortId.
    | @params {$shortId}
    */
    function delete($childShortId, $quoteShortId) {
        $userId = Auth::user()->id;
        $quoteToDelete = Quote::whereHas('child', function($query) use($userId) {
            $query->where('children.user_id', $userId);
        })
        ->where('short_id', $quoteShortId)
        ->first();

        if (!$quoteToDelete) {
            return self::RespondModelNotFound();
        }
        $quoteToDelete->delete();

        return response()->json([
            self::SUCCESS => true
        ]);
    }

    function getMainColor($image_url) {
        $dominantColor_rgb = ColorThief::getColor($image_url, 100);

        $dominantColor_hex = sprintf("#%02x%02x%02x", $dominantColor_rgb[0], $dominantColor_rgb[1], $dominantColor_rgb[2]);

        return $dominantColor_hex;
    }

    function getQuoteOriginalImage(Request $request, $childShortId, $quoteShortId, $img_original_url_id) {
        $quote = Quote::where('short_id', $quoteShortId)->where('img_original_url_id', $img_original_url_id)->first();

        if (!$quote) {
            return response()->json([
                self::SUCCESS => false,
                self::ERROR_TYPE => self::ERROR_TYPE_IMAGE_NOT_FOUND
            ]);
        }

        return Image::make($quote->getMedia('original')[0]->getPath())->response();
    }

    function getQuoteBakedImage(Request $request, $childShortId, $quoteShortId, $img_baked_url_id) {
        $quote = Quote::where('short_id', $quoteShortId)->where('img_baked_url_id', $img_baked_url_id)->first();

        if (!$quote) {
            return response()->json([
                self::SUCCESS => false,
                self::ERROR_TYPE => self::ERROR_TYPE_IMAGE_NOT_FOUND
            ]);
        }

        return Image::make($quote->getMedia('baked')[0]->getPath())->response();
    }
}
