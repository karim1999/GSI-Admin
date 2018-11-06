<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JointCourses extends Model
{
    public function course(){
        return $this->belongsTo('App\Courses');
    }
    public function user(){
        return $this->belongsTo('App\User');
    }
}
