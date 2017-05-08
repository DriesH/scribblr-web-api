<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;
use App\Classes\ShortIdGenerator;

//Models
use App\Child;
use App\Quote;
use Auth;

class ChildController extends Controller
{
    /*
    | Get all children.
    */
    function getAllChildren()
    {
        $user = Auth::user();
        $children = Child::where('user_id', $user->id)->get();

        if (!$children) {
            return self::RespondModelNotFound();
        }
        return response()->json([
            'success' => true,
            'children' => $children->toArray()
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
        $allChildQuotes = Child::where('short_id', $childShortId)->with('Quotes')->first();
        if (!$allChildQuotes) {
            return self::RespondModelNotFound();
        }

        return response()->json([
            'success' => true,
            'quotes' => $allChildQuotes->Quotes->toArray()
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
            'last_name' => 'max:50',
            'date_of_birth' => self::REQUIRED.'|date',
            'thumbnail' => 'file|image|size:10485760'
        ]);

        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }

        $newChild = new Child();
        do {
            $shortId = $shortIdGenerator->generateId(8);
        } while ( count( Child::where('short_id', $shortId)->first()) >= 1 );
        $newChild->short_id = $shortId;
        $newChild->user_id = Auth::user()->id; // FIXME: get current user, works with jwt???
        $newChild->gender = $request->gender;
        $newChild->first_name = $request->first_name;
        $newChild->last_name = $request->last_name;
        $newChild->date_of_birth = (new \DateTime($request->date_of_birth))->format('Y-m-d');

        if ($request->thumbnail) {
            addChildThumnail($newChild, $request);
        }

        $newChild->save();


        return response()->json([
            'success' => true,
            'child' => $newChild
        ]);
    }

    private function addChildThumnail($child, $request){
        $thumnail_url_id = uniqid(true);

        $child->addMedia($request->thumbnail)
        ->withCustomProperties(['url_id' => $thumnail_url_id])
        ->toMediaLibrary('thumbnail');

        $newChild->thumbnail_url_id = $thumnail_url_id;
        $newChild->save();
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
            'date_of_birth' => self::REQUIRED.'|date',
            'thumbnail' => 'file|image|size:10485760'
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
        $childToUpdate->date_of_birth = (new \DateTime($request->date_of_birth))->format('Y-m-d');
        if ($request->thumbnail) {
            addChildThumnail($newChild, $request);
        }
        $childToUpdate->save();

        return response()->json([
            'success' => true,
            'child' => $childToUpdate
        ]);

    }

    function thumbnail(Request $request, $childShortId, $thumbnail_url_id) {
        $child = Child::where('short_id', $childShortId)->first();

        if (!$child) {
            return self::RespondModelNotFound();
        }

        if ($child->thumbnail_url_id != $thumbnail_url_id) {
            return response()->json([
                self::SUCCESS => fase,
                self::ERROR_TYPE => 'image not found.'
            ]);
        }

        return Image::make($child->getMedia('thumbnail')[0]->getPath())->response();
    }
}
