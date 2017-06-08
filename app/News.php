<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'message'];
    protected $hidden =  ['pivot'];

    public function user()
    {
        return $this->belongsToMany('App\User', 'news__users',
        'news_id', 'user_id');
    }
}
