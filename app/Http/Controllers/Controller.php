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

    /*      achievements    */
    const ACHIEVEMENT = 'achievement';
    const REGISTER_ACCOUNT = 'register_account';
    const CONFIRM_EMAIL = 'confirm_email';
    const COMPLETE_ACCOUNT_INFO = 'complete_account_info';
    const ADD_CHILD = 'add_child';
    const ADD_SCRIBBLE = 'add_scribble';
    const SHARE_SCRIBBLE = 'share_scribble';
    const ADD_BOOK = 'add_book';
    const ORDER_BOOK = 'order_book';

    /*      responses       */
    const SUCCESS = 'success';
    const USER = 'user';
    const ERRORS = 'errors';
    const REQUIRED = 'required';
    const OLD_INPUT = 'old_input';
    const ERROR_TYPE = 'error_type';
    const ERROR_MESSAGE = 'error_message';
    const ERROR_TYPE_NOT_AUTHENTICATED = 'not_authenticated';
    const ERROR_TYPE_MODEL_NOT_FOUND = 'model_not_found';
    const ERROR_TYPE_VALIDATION = 'validation';
    const ERROR_TYPE_IMAGE_NOT_FOUND = 'image_not_found';
    const ERROR_TYPE_RELATION_ALREADY_EXISTS = 'relation_already_exists';
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
            self::ERRORS => $validator->errors()->messages(),
            self::OLD_INPUT => $request->all()
        ], 400);
    }

    protected function checkAchievementProgress($achievement_scope_name) {
        $achievement_resp = new stdClass();
        $achievement_checker = new AchievementChecker();
        $user = Auth::user();
        switch ($achievement_scope_name) {
            case self::REGISTER_ACCOUNT: // make account
                $achievement_resp = $achievement_checker->attachAndReturnUserAchievement($user, $achievement_scope_name);
                break;
            case self::CONFIRM_EMAIL: //confirm email
                        //FIXME: check if email is confirmed
                break;
            case self::COMPLETE_ACCOUNT_INFO: //complete acc info
                $achievement_resp = $achievement_checker->checkAccountInfo($user, $achievement_scope_name);
                break;
            case self::ADD_CHILD:
                $achievement_resp = $achievement_checker->checkFirstChild($user, $achievement_scope_name);
                break;
            case self::ADD_SCRIBBLE:
                $achievement_resp = $achievement_checker->checkAmountScribbles($user, $achievement_scope_name);
                break;
            case self::SHARE_SCRIBBLE:

                break;
            case self::ADD_BOOK:
                # code...
                break;
            case self::ORDER_BOOK:
                # code...
                break;
            default:
                return null;
        }

        return $achievement_resp;
    }
}
