<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Components\KNet\OnlinePaymentIntegration;
use App\JointLectures;

class OnlinePaymentController extends Controller
{

    public function buy(Request $request){
        $data= [
            'amount' => $request->amount,
            'udf1'=> $request->user_id,
            'udf2'=> $request->lectures_id,
            'udf3'=> 'udf3',
            'udf4'=> 'udf4',
            'udf5'=> 'udf5',
        ];
        return OnlinePaymentIntegration::Buy($data);
    }

    public function response(Request $request){
        $PaymentID = $request->paymentid;
        $presult = $request->result;
        $postdate = $request->postdate;
        $tranid = $request->tranid;
        $auth = $request->auth;
        $ref = $request->ref;
        $trackid = $request->trackid;
        $udf1 = $request->udf1;
        $udf2 = $request->udf2;
        $udf3 = $request->amount;
        $udf4 = $request->udf4;
        $udf5 = $request->udf5;
        if ( $presult == "CAPTURED" && $PaymentID != "")
        {
            $joint = new JointLectures;
            $joint->user_id = $request->udf1;
            $joint->lectures_id = $request->udf2;
            $joint->amount = $request->amount;
    
            $joint->save();
            return redirect()->route('payment.success');
        }else{
            return redirect()->route('payment.error');
        }
    }

    public function success(Request $request){
        echo "Success";
    }

    public function error(Request $request){
        echo "Error";
    }

}
