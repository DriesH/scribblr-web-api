<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;
use App\Classes\ShortIdGenerator;
use Image;
use Spatie\Image\Image as SpatieImage;


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
            self::SUCCESS => true,
            'children' => $children->toArray()
        ]);
    }

    /*
    | Get a specific child by shortId.
    | @params {$shortId}
    */
    function getChild($childShortId)
    {
        $userId = Auth::user()->id;
        $child = Child::where('short_id', $childShortId)->where('user_id', $userId)->first();
        if (!$child) {
            return self::RespondModelNotFound();
        }

        return response()->json([
            self::SUCCESS => true,
            'child' => $child
        ]);
    }

    /*
    | Get all quotes from a specific child by shortId.
    | @params {$shortId}
    */
    function allQuotes($childShortId)
    {
        $userId = Auth::user()->id;
        $allChildQuotes = Child::where('short_id', $childShortId)->where('user_id', $userId)->with('Quotes')->first();
        if (!$allChildQuotes) {
            return self::RespondModelNotFound();
        }

        return response()->json([
            self::SUCCESS => true,
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
            self::SUCCESS => true,
            'child' => $newChild,
            self::ACHIEVEMENT => self::checkAchievementProgress(self::ADD_CHILD)
        ]);
    }

    private function addChildThumnail($child, $avatar){
        $avatar_url_id = hash_hmac('sha256', Str::random(40), config('app.key'));

        SpatieImage::load($avatar->getPathName())
        ->width(75)
        ->save('avatar.' . $avatar->getClientOriginalExtension());

        $child->addMedia('avatar.' . $avatar->getClientOriginalExtension())
        ->withCustomProperties(['url_id' => $avatar_url_id])
        ->toMediaLibrary('avatar');

        $child->avatar_url_id = $avatar_url_id;
        $child->save();
    }

    /*
    | Delete a child by shortId.
    | @params {$shortId}
    */
    function delete($childShortId) {
        $userId = Auth::user()->id;
        $childToDelete = Child::where('short_id', $childShortId)->where('user_id', $userId)->first();
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
        $userId = Auth::user()->id;
        $childToUpdate = Child::where('short_id', $childShortId)->where('user_id', $userId)->first();

        if (!$childToUpdate) {
            return self::RespondModelNotFound();
        }

        $validator = Validator::make($request->all(), [
            'gender' => [self::REQUIRED, Rule::in(Child::$genders)],
            'full_name' => self::REQUIRED.'|max:50',
            'date_of_birth' => self::REQUIRED.'|date',
            'avatar' => 'file|image|size:10485760'
        ]);

        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }

        $childToUpdate->gender = $request->gender;
        $childToUpdate->full_name = $request->full_name;
        $childToUpdate->date_of_birth = (new \DateTime($request->date_of_birth))->format('Y-m-d');
        $childToUpdate->save();

        if ($request->avatar) {
            $childToUpdate->clearMediaCollection('avatar');
            self::addChildThumnail($newChild, $request->avatar);
        }

        return response()->json([
            self::SUCCESS => true,
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
                self::ERROR_TYPE => 'Image not found.'
            ], 400);
        }

        return Image::make($child->getMedia('avatar')[0]->getPath())->response();
    }
}
