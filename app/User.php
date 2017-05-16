<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Tymon\JWTAuth\Contracts\JWTSubject as AuthenticatableUserContract;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;

class User extends Model implements
AuthenticatableContract,
AuthorizableContract,
CanResetPasswordContract,
AuthenticatableUserContract,
HasMedia
{
    use Authenticatable, Authorizable, CanResetPassword, SoftDeletes, HasMediaTrait;

    protected $dates = ['deleted_at'];
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'first_name',
        'last_name',
        'short_id',
        'email',
        'street_name',
        'house_number',
        'city',
        'postal_code',
        'country',
        'password',
    ];

    /**
    * The attributes that should be hidden for arrays.
    *
    * @var array
    */
    protected $hidden = [
        'password', 'remember_token',
    ];



    /**
    * @return mixed
    */
    public function getJWTIdentifier()
    {
        return $this->getKey();  // Eloquent model method
    }

    /**
    * @return array
    */
    public function getJWTCustomClaims()
    {
        return [
            'user' => [
                'id' => $this->id,
            ]
        ];
    }

    public function Children() {
        return $this->hasMany('App\Child', 'user_id');
    }

    public function achievements()
    {
        return $this->belongsToMany('App\Achievements', 'achievements__users',
        'user_id', 'achievement_id');
    }
}