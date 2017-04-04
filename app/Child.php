<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    protected $fillable = [
        'short_id',
        'user_id',
        'gender',
        'first_name',
        'last_name',
        'date_of_birth',
    ];

    protected $genders = [
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
