<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Quote;

class ShareController extends Controller
{
    function shareQuote($childShortId, $quoteShortId) {
        $userId = Auth::user()->id;
        $quote = Quote::whereHas('child', function($query) use($userId) {
            $query->where('children.user_id', $userId);
        })
        ->where('short_id', $quoteShortId)
        ->first();

        if (!$quote) {
            return self::RespondModelNotFound();
        }

        $quote->shared = true;
        $quote->save();

        return response()->json([
            self::SUCCESS => true,
            'quote' => $quote
        ]);
    }

    function getSharedQuote($childShortId, $quoteShortId, $img_baked_url_id) {
        $quote = Quote::where('short_id', $quoteShortId)
                ->where('img_baked_url_id', $img_baked_url_id)
                ->where('is_shared', true)
                ->first(['quote', 'story', 'img_baked_url_id']);

        if (!$quote) {
            return response()->json([
                self::SUCCESS => false,
                self::ERROR_TYPE => self::ERROR_TYPE_IMAGE_NOT_FOUND
            ]);
        }

        return response()->json([
            self::SUCCESS => true,
            'quote' => $quote
        ]);
    }
}
