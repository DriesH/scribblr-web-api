<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Achievement;
use App\Achievement_User;
use Auth;


class AchievementController extends Controller
{
    function all() {
        $user = Auth::user();

        $all_achievements = Achievement::all();
        $completed_achievements = Auth::user()->with('achievements')->first()->achievements;

        $completed_achievements->map(function ($completed_achievements) {
            $completed_achievements->completed = true;
            return $completed_achievements;
        });

        $not_completed = $all_achievements->diff($completed_achievements);
        $not_completed->map(function ($not_completed) {
            $not_completed->completed = false;
            return $not_completed;
        });

        $marked_achievements = $completed_achievements->merge($not_completed)->groupBy('category');

        return $marked_achievements;

        return $all_achievements;


    }
}
