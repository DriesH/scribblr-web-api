<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;
use App\Classes\ShortIdGenerator;

//Models
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
    function new(Request $request, ShortIdGenerator $shortIdGenerator)
    {
        $validator = Validator::make($request->all(), [
            'gender' => ['required', Rule::in(Child::$genders)],
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'date_of_birth' => 'required|date'
        ]);

        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }

        $newChild = new Child();
        do {
            $shortId = $shortIdGenerator->generateId(8);
        } while ( count( Child::where('short_id', $shortId)->first()) >= 1 );
        $newChild->user_id = Auth::user()->id; // FIXME: get current user, works with jwt???
        $newChild->gender = $request->gender;
        $newChild->first_name = $request->first_name;
        $newChild->last_name = $request->last_name;
        $newChild->date_of_birth = new \DateTime($request->date_of_birth);
        $newChild->save();

        return response()->json([
            'success' => true,
            'child' => $newChild
        ]);
    }

    /*
    | Upload an image for your child avatar.
    */
    function uploadImage(Request $request)
    {
        //file upload
    }

    /*
    | Delete a child by shortId.
    | @params {$shortId}
    */
    function delete($shortId)
    {
        $childToDelete = Child::where('short_id')->first();
        if (!$childToDelete) {
            return self::RespondModelNotFound();
        }
        $childToDelete->delete();
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
