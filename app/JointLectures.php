<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JointLectures extends Model
{
    public function lecture(){
        return $this->belongsTo('App\Lectures');
    }
    public function user(){
        return $this->belongsTo('App\User');
    }
}
