<?php

namespace App\Classes;
use App\Achievement;
use App\User;
use App\Post;
use App\Book;
use App\Order;

class AchievementChecker
{
    function attachAndReturnUserAchievement($user, $achievement_scope_name, $amount_to_complete = null) {
        $achievement = Achievement::where('scope_name', $achievement_scope_name)->where('amount_to_complete', $amount_to_complete)->first();
        $user->achievements()->attach($achievement);
        return $achievement;
    }

    function checkIfUserHasAchievement($user, $achievement_scope_name, &$amount_to_complete = null) {
        $achievement = Achievement::where('scope_name', $achievement_scope_name)
                                    ->where('amount_to_complete', $amount_to_complete)
                                    ->first();
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
            if (count($user->children()->get()) == 1) {
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
                if (!self::checkIfUserHasAchievement($user, $achievement_scope_name, $amount_of_scribbles)) {
                return self::attachAndReturnUserAchievement($user, $achievement_scope_name, $amount_of_scribbles);
            }
            break;
        }
        return null;
    }

    function checkAmountScribblesShared($user, $achievement_scope_name) {
        $amount_of_shared_scribbles = Post::whereHas('child', function($query) use($user){
            $query->where('user_id', $user->id);
        })
        ->where('is_shared', true)
        ->get()
        ->count();

        $share_achievements = Achievement::where('scope_name', $achievement_scope_name)->orderBy('amount_to_complete')->get();

        foreach ($share_achievements as $achievement) {
            if ($achievement->amount_to_complete == $amount_of_shared_scribbles) {
                if (!self::checkIfUserHasAchievement($user, $achievement_scope_name, $amount_of_shared_scribbles)) {
                    return self::attachAndReturnUserAchievement($user, $achievement_scope_name, $amount_of_shared_scribbles);
                }
                break;
            }
        }
        return null;
    }

    function checkAmountBooks($user, $achievement_scope_name) {
        if (!self::checkIfUserHasAchievement($user, $achievement_scope_name)) {
            if (Book::where('user_id', $user->id)->get()->count() == 1) {
                return self::attachAndReturnUserAchievement($user, $achievement_scope_name);
            }
        }
        return null;
    }

    function checkAmountBooksOrdered($user, $achievement_scope_name) {
        $amount_of_orders = Order::where('user_id', $user->id)
        ->get()
        ->count();

        $order_achievements = Achievement::where('scope_name', $achievement_scope_name)->orderBy('amount_to_complete')->get();

        foreach ($order_achievements as $achievement) {
            if ($achievement->amount_to_complete == $amount_of_orders) {
                if (!self::checkIfUserHasAchievement($user, $achievement_scope_name, $amount_of_orders)) {
                    return self::attachAndReturnUserAchievement($user, $achievement_scope_name, $amount_of_orders);
                }
                break;
            }
        }
        return null;
    }























}
