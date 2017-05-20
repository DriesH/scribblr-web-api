<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Preset;

class PresetController extends Controller
{
    function getAllPresets() {
        $presets = Preset::all();

        return response()->json([
            self::SUCCESS => true,
            'presets' => $presets
        ]);
    }
}
