<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Color;

class ColorController extends Controller
{
    function getAllColors() {
        $colors = Color::all();

        return response()->json([
            self::SUCCESS => true,
            'colors' => $colors
        ]);
    }
}
