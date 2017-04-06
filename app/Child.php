<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;

class Child extends Model implements HasMedia
{
    use SoftDeletes, HasMediaTrait;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'short_id',
        'user_id',
        'gender',
        'first_name',
        'last_name',
        'date_of_birth',
    ];

    public static $genders = [
        'Male',
        'Female',
        'Prefer not to disclose'
    ];


    public function Quotes() {
        $this->hasMany('App\Quote', 'child_id');
    }

    public function User() {
        $this->belongsTo('App\User');
    }
}
