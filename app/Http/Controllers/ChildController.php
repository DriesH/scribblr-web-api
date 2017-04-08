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
        $user = Auth::user();
        $children = Child::where('user_id', $user->id)->get();

        if (!$children) {
            return self::RespondModelNotFound();
        }
        return response()->json([
            'success' => true,
            'children' => $children
        ]);
    }

    /*
    | Get a specific child by shortId.
    | @params {$shortId}
    */
    function getChild($childShortId)
    {
        $child = Child::where('short_id', $childShortId)->first();
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
    function allQuotes($childShortId)
    {
        $allChildQuotes = Quote::with(['Children' => function($query) use($childShortId) {
            $query->where('children.shortId', $childShortId);
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
            'gender' => [self::REQUIRED, Rule::in(Child::$genders)],
            'first_name' => self::REQUIRED.'|max:50',
            'last_name' => self::REQUIRED.'|max:50',
            'date_of_birth' => self::REQUIRED.'|date'
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
    function uploadImage(Request $request, $childShortId)
    {
        $validator = Validator::make($request->all(), [
            'image' => self::REQUIRED.'|file|image|size:10485760', //10 MB
        ]);

        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }

        $child = Child::where('short_id', $childShortId)->first();
        if (!$child) {
            return self::RespondModelNotFound();
        }

        $uploadedImage = $child->addMedia($request->image)->toMediaLibrary();

        return response()->json([
            'success' => true,
            'uploadedImage' => $uploadedImage
        ]);

    }

    /*
    | Delete a child by shortId.
    | @params {$shortId}
    */
    function delete($childShortId)
    {
        $childToDelete = Child::where('short_id', $childShortId)->first();
        if (!$childToDelete) {
            return self::RespondModelNotFound();
        }
        $childToDelete->delete();
    }

    /*
    | Update a child by shortId.
    | @params {$shortId}
    */
    function update(Request $request, $childShortId)
    {
        $validator = Validator::make($request->all(), [
            'gender' => [self::REQUIRED, Rule::in(Child::$genders)],
            'first_name' => self::REQUIRED.'|max:50',
            'last_name' => self::REQUIRED.'|max:50',
            'date_of_birth' => self::REQUIRED.'|date'
        ]);

        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }

        $childToUpdate = Child::where('short_id', $childShortId)->first();
        if (!$childToUpdate) {
            return self::RespondModelNotFound();
        }

        $childToUpdate->gender = $request->gender;
        $childToUpdate->first_name = $request->first_name;
        $childToUpdate->last_name = $request->last_name;
        $childToUpdate->date_of_birth = new \DateTime($request->date_of_birth);
        $childToUpdate->save();

        return response()->json([
            'success' => true,
            'child' => $childToUpdate
        ]);

    }
}
