<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Achievement;
use App\Achievement_User;
use App\Classes\AchievementChecker;
use stdClass;
use Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    const SUCCESS = 'success';
    const ERRORS = 'errors';
    const REQUIRED = 'required';
    const OLD_INPUT = 'old_input';
    const ERROR_TYPE = 'error_type';
    const ERROR_MESSAGE = 'error_message';
    const ERROR_TYPE_MODEL_NOT_FOUND = 'model_not_found';
    const ERROR_TYPE_VALIDATION = 'validation';
    const ERROR_TYPE_IMAGE_NOT_FOUND = 'image_not_found';
    const ERROR_MESSAGES = [
        'model_not_found' => 'The model was not found.',
        'validation' => 'The given input did not pass the validation rules.',
    ];

    protected function RespondModelNotFound() {
        return response()->json([
            self::SUCCESS => false,
            self::ERROR_TYPE => self::ERROR_TYPE_MODEL_NOT_FOUND,
            self::ERROR_MESSAGE => self::ERROR_MESSAGES[self::ERROR_TYPE_MODEL_NOT_FOUND]
        ], 400);
    }

    protected function RespondValidationError($request, $validator) {
        return response()->json([
            self::SUCCESS => false,
            self::ERROR_TYPE => self::ERROR_TYPE_VALIDATION,
            self::ERROR_MESSAGE => self::ERROR_MESSAGES[self::ERROR_TYPE_VALIDATION],
            self::ERRORS => $validator->errors()->all(),
            self::OLD_INPUT => $request->all()
        ], 400);
    }

    protected function checkAchievementProgress($achievement_id) {
        $achievement_resp = new stdClass();
        $achievement_checker = new AchievementChecker();
        $user = Auth::user();
        switch ($achievement_id) {
            case 1: // make account
                $achievement_resp = $achievement_checker->attachAndReturnUserAchievement($user, $achievement_id);
                break;
            case 2: //confirm email
                $achievement_resp = $achievement_checker->attachAndReturnUserAchievement($user, $achievement_id);
                break;
            case 3: //complete acc info
                $achievement_resp = $achievement_checker->checkAccountInfo($user, $achievement_id);
                break;
            case 4:
                $achievement_resp = $achievement_checker->checkFirstChild($user, $achievement_id);
                break;
            case 5:
            case 6:
            case 7:
                # code...
                break;
            case 8:
            case 9:
            case 10:
                # code...
                break;
            case 11:
                # code...
                break;
            case 12:
            case 13:
                # code...
                break;
            default:
                return null;
        }

        return $achievement_resp;
    }
}
