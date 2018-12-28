<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lectures extends Model
{
    protected $with= ['user'];
    
    public function user(){
        return $this->belongsTo('App\User');
    }

    public function course(){
        return $this->belongsTo('App\Courses');
    }

    public function jointUsers(){
        return $this->belongsToMany('App\User', 'joint_lectures')->withPivot(['amount']);
    }

    public function comments(){
        return $this->hasMany('App\Comments');
    }

    
}
