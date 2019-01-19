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

    public function payments()
    {
        return $this->hasMany('App\PaymentMethod', 'jointlecture_id');
    }
    
    // public function getAmountAttribute()
    // {
    //     if($this->showamount()->count() > 0){
    //         return $this->showamount()->sum('amount');
    //     }
    //     return 0;
    // }
    // protected $appends = ['amount'];
    
}
