<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use stdClass;
use Auth;
use Validator;

class UserController extends Controller
{
    public function getUser(Request $request) {
        $user = Auth::user();
        $user_resp = new stdClass();
        $user_resp->id = $user->id;
        $user_resp->short_id = $user->short_id;
        $user_resp->first_name = $user->first_name;
        $user_resp->last_name = $user->last_name;
        $user_resp->email = $user->email;
        $user_resp->street_name = $user->street_name;
        $user_resp->house_number = $user->house_number;
        $user_resp->city = $user->city;
        $user_resp->postal_code = $user->postal_code;
        $user_resp->has_seen_book_tutorial = $user->has_seen_book_tutorial;
        $user_resp->country = $user->country;

        return response()->json([
            $user_resp
        ]);
    }

    public function editUser(Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name' => self::REQUIRED.'|max:50',
            'last_name' => 'max:50',
            'street_name' => 'max:150',
            'house_number' => 'max:10',
            'city' => 'max:50',
            'postal_code' => 'max:16',
        ]);

        if ($validator->fails()) {
            return self::RespondValidationError($request, $validator);
        }

        $user = Auth::user();
        $user->first_name = $request->first_name;
        if ($request->last_name) {
            $user->last_name = $request->last_name;
        }
        if ($request->street_name) {
            $user->street_name = $request->street_name;
        }
        if ($request->house_number) {
            $user->house_number = $request->house_number;
        }
        if ($request->city) {
            $user->city = $request->city;
        }
        if ($request->postal_code) {
            $user->postal_code = $request->postal_code;
        }
        if ($request->country) {
            $countries = Country::pluck('name');
            if (in_array($request->country, $countries)) {
                $user->country = $request->country;
            }
            else {
                return self::RespondModelNotFound();
            }
        }

        $user->save();

        return response()->json([
            self::SUCCESS => true,
            self::USER => $user,
            self::ACHIEVEMENT => self::checkAchievementProgress(self::COMPLETE_ACCOUNT_INFO)
        ]);
    }

    function checkAuth() {
        $user = Auth::user();
        return response()->json([
            self::SUCCESS => true,
            self::USER => $user
        ]);
    }
}
