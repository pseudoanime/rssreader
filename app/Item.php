<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public function Feedurl()
    {
        return $this->belongsTo('App\Feedurl');
    }
}
