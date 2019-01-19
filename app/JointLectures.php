<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JointLectures extends Model
{
        protected $appends = ['amount2'];
        protected $with = ['amount2'];

    public function lecture(){
        return $this->belongsTo('App\Lectures');
    }
    
    public function user(){
        return $this->belongsTo('App\User');
    }

    public function payments()
    {
        return $this->hasMany('App\PaymentMethod', 'jointlecture_id');
    }
    public function showamount()
    {
        return $this->hasMany('App\PaymentMethod', 'jointlecture_id');
    }
    
    public function getAmount2Attribute()
    {
        if($this->showamount()->count() > 0){
            return $this->showamount()->sum('amount');
        }
        return 0;
    }
    
}
