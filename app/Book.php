<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\softDeletes;

class Book extends Model
{

    use softDeletes;
    public function categories()
    {
        return $this->belongsToMany('App\Category');
    }
}
