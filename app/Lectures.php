<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lectures extends Model
{
    public function user(){
        return $this->belongsTo('App\User');
    }

    public function course(){
        return $this->belongsTo('App\Courses');
    }

    public function JointUsers(){
        return $this->belongsToMany('App\User', 'App\joint_lectures');
    }

    public function comments(){
        return $this->hasMany('App\Comments');
    }
}
