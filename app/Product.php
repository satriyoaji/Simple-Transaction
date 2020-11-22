<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'user_id', 'name', 'stock', 'price'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function transactions() {
        return $this->hasMany('App\Transaction');
    }
}
