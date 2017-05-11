<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;
use App\Classes\ShortIdGenerator;
use Image;

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
            'full_name' => self::REQUIRED.'|max:50',
            'date_of_birth' => self::REQUIRED.'|date',
            'avatar' => 'image|max:10485760'
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
        $newChild->full_name = $request->full_name;
        $newChild->date_of_birth = (new \DateTime($request->date_of_birth))->format('Y-m-d');
        $newChild->save();

        if ($request->avatar) {
            self::addChildThumnail($newChild, $request->avatar);
        }

        return response()->json([
            'success' => true,
            'child' => $newChild
        ]);
    }

    private function addChildThumnail($child, $avatar){
        $avatar_url_id = sha1($avatar->getPathName());

        $child->addMedia($avatar)
        ->withCustomProperties(['url_id' => $avatar_url_id])
        ->toMediaLibrary('avatar');

        $child->avatar_url_id = $avatar_url_id;
        $child->save();
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
            'full_name' => self::REQUIRED.'|max:50',
            'date_of_birth' => self::REQUIRED.'|date',
            'avatar' => 'file|image|size:10485760'
        ]);

        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }

        $childToUpdate = Child::where('short_id', $childShortId)->first();
        if (!$childToUpdate) {
            return self::RespondModelNotFound();
        }

        $childToUpdate->gender = $request->gender;
        $childToUpdate->full_name = $request->full_name;
        $childToUpdate->date_of_birth = (new \DateTime($request->date_of_birth))->format('Y-m-d');
        $childToUpdate->save();

        if ($request->avatar) {
            self::addChildThumnail($newChild, $request->avatar);
        }

        return response()->json([
            'success' => true,
            'child' => $childToUpdate
        ]);

    }

    function avatar(Request $request, $childShortId, $avatar_url_id) {
        $child = Child::where('short_id', $childShortId)->first();

        if (!$child) {
            return self::RespondModelNotFound();
        }

        if ($child->avatar_url_id != $avatar_url_id) {
            return response()->json([
                self::SUCCESS => false,
                self::ERROR_TYPE => 'image not found.'
            ]);
        }

        return Image::make($child->getMedia('avatar')[0]->getPath())->response();
    }
}
