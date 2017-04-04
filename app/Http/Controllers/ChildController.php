<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Child;
use App\Quote;

class ChildController extends Controller
{
    /*
    | Get all children.
    */
    function index()
    {
        // do something...
    }

    /*
    | Get a specific child by shortId.
    | @params {$shortId}
    */
    function getChild($shortId)
    {
        $child = Child::where('short_id', $shortId)->first();
        if (!$child) {
            return self::RespondModelNotFound();
        }

        return response()->json([
            'success' => true,
            'child' => $child
        ]);
    }

    /*
    | Get all quotes from a specific child by shortId.
    | @params {$shortId}
    */
    function allQuotes($shortId)
    {
        $allChildQuotes = Quote::with(['Children' => function($query) use($shortId) {
            $query->where('children.shortId', $shortId);
        }])
        ->get();

        if (!$allChildQuotes) {
            return self::RespondModelNotFound();
        }

        return response()->json([
            'success' => true,
            'quotes' => $allChildQuotes
        ]);
    }

    /*
    | Create a new child.
    */
    function new(Request $request)
    {
        // do something...
    }

    /*
    | Upload an image for your child avatar.
    */
    function uploadImage()
    {
        // do something...
    }

    /*
    | Delete a child by shortId.
    | @params {$shortId}
    */
    function delete($shortId)
    {
        // do something...
    }

    /*
    | Update a child by shortId.
    | @params {$shortId}
    */
    function update($shortId)
    {
        // do something...
    }
}
