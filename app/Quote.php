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

    public function Child() {
        return $this->belongsTo('App\Child', 'child_id');
    }

    public function Preset() {
        return $this->belongsTo('App\Preset');
    }
}
