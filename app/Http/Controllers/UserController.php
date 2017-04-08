<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use stdClass;
use Auth;

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
        $user_resp->country = $user->country;

        return response()->json([
            $user_resp
        ]);
    }
}
