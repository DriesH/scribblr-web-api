<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Achievement extends Model
{
    use SoftDeletes;


    public function users()
    {
        return $this->belongsToMany('App\Users', 'achievements__users',
        'achievement_id', 'user_id');
    }
}
