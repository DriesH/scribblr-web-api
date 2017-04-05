<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use SoftDeletes;
    
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
