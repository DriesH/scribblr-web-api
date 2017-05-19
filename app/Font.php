<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Font extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    public function Quotes() {
        return $this->hasMany('App\Quote', 'font_id');
    }
}
