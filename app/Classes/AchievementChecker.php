<?php

namespace App\Classes;
use App\Achievement;
use App\User;

class AchievementChecker
{
    function attachAndReturnUserAchievement($user, $achievement_id) {
        $achievement = Achievement::find($achievement_id);
        $user->achievements()->attach($achievement);
        return $achievement;
    }

    function checkIfUserHasAchievement($user, $achievement_id) {
        $achievement = Achievement::find($achievement_id);
        if($user->achievements->contains($achievement->id)) {
            return true;
        }
        return false;
    }

    function checkAccountInfo($user, $achievement_id) {
        if (!self::checkIfUserHasAchievement($user, $achievement_id)) {
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
                return self::attachAndReturnUserAchievement($user, $achievement_id);
            }
        }
        return null;
    }

    function checkFirstChild($user, $achievement_id) {
        if (!self::checkIfUserHasAchievement($user, $achievement_id)) {
            if (count($user->with('children')->first()->children) <= 0) {
                return self::attachAndReturnUserAchievement($user, $achievement_id)
            }
        }
        return null;
    }
}
