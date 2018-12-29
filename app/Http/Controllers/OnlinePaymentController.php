<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Components\KNet\OnlinePaymentIntegration;

class OnlinePaymentController extends Controller
{

    public function buy(Request $request){
        $data= [
            'user_id' => 1,
            'amount' => 10,
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
        $udf3 = $request->udf3;
        $udf4 = $request->udf4;
        $udf5 = $request->udf5;
        if ( $presult == "CAPTURED" && $PaymentID != "")
        {
            //do something with the data
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
