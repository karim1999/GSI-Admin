<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    public function user(){
        return $this->belongsTo('App\User');
    }

    public function jointUsers(){
        return $this->belongsToMany('App\User',' joint_courses')
    }

    public function lecture(){
        return $this->hasMany('App\Lecutres');
    }

    public function comments(){
        return $this->hasMany('App\Comments');
    }
}
