<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [];
    protected $dates = ['deleted_at'];

    public function books()
    {
        return $this->belongsToMany('App\Book', 'book__orders',
        'order_id', 'book_id');
    }
}
