<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Quote;
use App\Child;
use App\Classes\ShortIdGenerator;

class QuoteController extends Controller
{
    /*
    | Create a new quote.
    */
    function new(Request $request, ShortIdGenerator $shortIdGenerator, $childShortId)
    {
        $validator = Validator::make($request->all(), [
            'quote' => self::REQUIRED,
            'font_size' => self::REQUIRED.'|integer',
            'font' => self::REQUIRED,
            'image' => 'image'
        ]);

        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }

        $child = Child::where('short_id', $childShortId)->first();
        if (!$child) {
            return self::RespondModelNotFound();
        }

        $quote = new Quote();
        do {
            $quoteShortId = $shortIdGenerator->generateId(8);
        } while ( count( Quote::where('short_id', $shortId)->first()) >= 1 );
        $quote->quote = $request->quote;
        $quote->font_size = $request->font_size;
        $quote->font = $request->font;
        if($request->image) $quote->addMedia($request->image);
        $quote->save();

        $child->attach($quote);
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
}
