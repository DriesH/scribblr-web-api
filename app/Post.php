<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;

class Post extends Model implements HasMedia
{
    use SoftDeletes, HasMediaTrait;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'short_id',
        'child_id',
        'quote',
        'story',
        'img_original_url_id',
        'img_baked_url_id',
    ];

    public function Child() {
        return $this->belongsTo('App\Child', 'child_id');
    }

    public function Preset() {
        return $this->belongsTo('App\Preset');
    }

    public function Font() {
        return $this->belongsTo('App\Font');
    }

    public function Book()
    {
        return $this->belongsToMany('App\Book', 'book__posts',
        'post_id', 'book_id');
    }
}
