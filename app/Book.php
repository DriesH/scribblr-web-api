<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $fillable = [];
    protected $dates = ['deleted_at'];


    public function posts()
    {
        return $this->belongsToMany('App\Post', 'book__posts',
        'book_id', 'post_id');
    }


}
