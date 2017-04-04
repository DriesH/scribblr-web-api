<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
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
