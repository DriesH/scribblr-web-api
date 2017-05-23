<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Preset extends Model
{
    use SoftDeletes;

    public $timestamps = false;
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function Posts() {
        return $this->hasMany('App\Post', 'preset_id');
    }

}
