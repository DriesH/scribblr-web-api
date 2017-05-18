<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Preset extends Model
{
    use SoftDeletes;

    public function Quotes() {
        return $this->hasMany('App\Quote', 'preset_id');
    }

}
