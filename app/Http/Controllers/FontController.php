<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Font;

class FontController extends Controller
{
    function getAllFonts() {
        $fonts = Font::all();

        return response()->json([
            self::SUCCESS => true,
            'fonts' => $fonts
        ]);
    }
}
