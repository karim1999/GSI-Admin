<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'jointlecture_id', 'online', 'knet', 'cash'
    ];

    public function joint(){
        return $this->belongsTo('App\JointLectures', 'jointlecture_id');
    }

    public $table = 'payment_method';
}
