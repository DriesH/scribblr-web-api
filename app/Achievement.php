<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Achievement extends Model
{
    use SoftDeletes;

    protected $hidden =  ['pivot'];


    public function user()
    {
        return $this->belongsToMany('App\Users', 'achievements__users',
        'achievement_id', 'user_id');
    }
}
