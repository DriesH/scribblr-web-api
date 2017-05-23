<?php

namespace App\Classes;
use App\Achievement;
use App\User;
use App\Post;

class AchievementChecker
{
    function attachAndReturnUserAchievement($user, $achievement_scope_name, $amount_to_complete = null) {
        $achievement = Achievement::where('scope_name', $achievement_scope_name)->where('amount_to_complete', $amount_to_complete)->first();
        $user->achievements()->attach($achievement);
        return $achievement;
    }

    function checkIfUserHasAchievement($user, $achievement_scope_name) {
        $achievement = Achievement::where('scope_name', $achievement_scope_name)->first();
        if($user->achievements->contains($achievement->id)) {
            return true;
        }
        return false;
    }

    function checkAccountInfo($user, $achievement_scope_name) {
        if (!self::checkIfUserHasAchievement($user, $achievement_scope_name)) {
            $filtered_user = User::where('id', $user->id)
            ->first([
                'first_name',
                'last_name',
                'email',
                'street_name',
                'house_number',
                'city',
                'postal_code',
                'country',
            ])
            ->toArray();

            if(!in_array(null, $filtered_user)) {
                return self::attachAndReturnUserAchievement($user, $achievement_scope_name);
            }
        }
        return null;
    }

    function checkFirstChild($user, $achievement_scope_name) {
        if (!self::checkIfUserHasAchievement($user, $achievement_scope_name)) {
            if (count($user->with('children')->first()->children) == 1) {
                return self::attachAndReturnUserAchievement($user, $achievement_scope_name);
            }
        }
        return null;
    }

    function checkAmountScribbles($user, $achievement_scope_name) {
        $amount_of_scribbles = Post::whereHas('child', function($query) use($user){
            $query->where('user_id', $user->id);
        })
        ->get()
        ->count();

        $scribble_achievements = Achievement::where('scope_name', $achievement_scope_name)->orderBy('amount_to_complete')->get();

        foreach ($scribble_achievements as $achievement) {
            if ($achievement->amount_to_complete == $amount_of_scribbles) {
                return self::attachAndReturnUserAchievement($user, $achievement_scope_name, $amount_of_scribbles);
            }
        }
        return null;
    }
}
