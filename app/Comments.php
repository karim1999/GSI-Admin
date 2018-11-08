<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    public function user(){
        return $this->hasMany('App\User');
    }

    public function course(){
        return $this->belongsTo('App\Courses');
    }

    public function Lecture(){
        return $this->belongsTo('App\Lectures');
    }
}
