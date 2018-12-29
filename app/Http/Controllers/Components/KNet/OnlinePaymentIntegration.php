<?php

namespace App\Http\Controllers\Components\KNet;

use e24PaymentPipe;

class OnlinePaymentIntegration
{

    public static function Buy($data){
        $language = 'ENG'; //change it to "ARA" for arabic language
        $currency = 414;
        $response = url('payment/response'); // set your response page URL
        $error = url('payment/error'); //set your error page URL

        $file = app_path("Http/Controllers/Components/KNet/e24PaymentPipe.inc.php");
        require_once $file;
        $Pipe = new e24PaymentPipe;
        $Pipe->setAction(1);
        $Pipe->setCurrency($currency);
        $Pipe->setLanguage($language); //change it to "ARA" for arabic language
        $Pipe->setResponseURL($response); // set your respone page URL
        $Pipe->setErrorURL($error); //set your error page URL
        $Pipe->setAmt($data['amount']); //set the amount for the transaction
//        $Pipe->setResourcePath("/home/gsikwcom/public_html/KNet_Tarek/resource/"); //change the path where your resource file is
        $Pipe->setResourcePath(public_path('/online_payment/resource/')); //change the path where your resource file is
        $Pipe->setAlias("global"); //set your alias name here
        $Pipe->setTrackId(rand(999999999, 2)); //generate the random number here

        $Pipe->setUdf1($data['udf1']); //set User defined value
        $Pipe->setUdf2($data['udf2']); //set User defined value
        $Pipe->setUdf3($data['udf3']); //set User defined value
        $Pipe->setUdf4($data['udf4']); //set User defined value
        $Pipe->setUdf5($data['udf5']); //set User defined value
//get results
        if($Pipe->performPaymentInitialization() != $Pipe->SUCCESS){
            echo "Result=" . $Pipe->SUCCESS;
            echo "<br>" . $Pipe->getErrorMsg();
            echo "<br>" . $Pipe->getDebugMsg();
            return redirect($error);
        }
        else{
            $payID = $Pipe->getPaymentId();
            $payURL = $Pipe->getPaymentPage();
            echo $Pipe->getDebugMsg();
            return redirect()->away($payURL . "?PaymentID=" . $payID);
        }
    }

}
