<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;

class Quote extends Model implements HasMedia
{
    use SoftDeletes, HasMediaTrait;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'short_id',
        'child_id',
        'quote',
        'font_size',
        'font',
    ];

    public function Children() {
        $this->belongsTo('App\Child');
    }
}
