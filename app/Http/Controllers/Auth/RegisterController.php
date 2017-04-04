<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use App\Classes\ShortIdGenerator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Jrean\UserVerification\Traits\VerifiesUsers;
use Jrean\UserVerification\Facades\UserVerification;
use Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers, VerifiesUsers;

    /**
    * Where to redirect users after registration.
    *
    * @var string
    */
    protected $redirectTo = '/home';
    protected $shortId;

    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct(ShortIdGenerator $shortId)
    {
        $this->shortId = $shortId;
        $this->middleware('guest', ['except' => ['getVerification', 'getVerificationError']]);
    }
    /**
    * Get a validator for an incoming registration request.
    *
    * @param  array  $data
    * @return \Illuminate\Contracts\Validation\Validator
    */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'fullname' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:8',
        ]);
    }

    /**
    * Create a new user instance after a valid registration.
    *
    * @param  array  $data
    * @return User
    */
    protected function create(array $data)
    {
        //explode fullname in 2 pieces. Split on first space
        $fullnameExploded = explode(' ', $data['fullname'], 2);
        $firstName = $fullnameExploded[0];
        $lastName = (!empty($fullnameExploded[1]))?$fullnameExploded[1]:'';

        do {
            $shortId = $this->shortId->generateId(8);
        } while ( count( User::where('short_id', $shortId)->first()) >= 1 );

        return User::create([
            'short_id' => $shortId,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        event(new Registered($user));

        $token = $this->guard('web')->login($user);

        UserVerification::generate($user);

        UserVerification::send($user, 'My Custom E-mail Subject');

        return response()->json([
            'success' => true,
            'token' => $token
        ]);
    }
}
