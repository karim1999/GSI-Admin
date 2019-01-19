<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    protected $with= ['user'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function course(){
        return $this->belongsTo('App\Courses');
    }

    public function lectures(){
        return $this->belongsTo('App\Lectures');
    }
}
