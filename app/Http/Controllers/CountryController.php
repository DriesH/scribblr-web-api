<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Country;

class CountryController extends Controller
{
    function getAllCountries() {
        $countries = Country::orderBy('name')->pluck('name');

        return response()->json([
            self::SUCCESS => true,
            'countries' => $countries
        ]);
    }
}
