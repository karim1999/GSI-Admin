<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
require_once "e24PaymentPipe.inc.php";

class BuyController extends Controller
{
    public function buy()
    {
        $Pipe = new e24PaymentPipe;
        
        $Pipe->setAction(1);
        $Pipe->setCurrency(414);
        $Pipe->setLanguage("ENG"); //change it to "ARA" for arabic language
        $Pipe->setResponseURL("http://www.gsikw.com/KNet_Tarek/response.php"); // set your respone page URL
        $Pipe->setErrorURL("http://www.gsikw.com/KNet_Tarek/error.php"); //set your error page URL
        $Pipe->setAmt("10"); //set the amount for the transaction
        //$Pipe->setResourcePath("/Applications/MAMP/htdocs/php-toolkit/resource/");
        $Pipe->setResourcePath("/resource/"); //change the path where your resource file is
        $Pipe->setAlias("global"); //set your alias name here
        $Pipe->setTrackId(rand(999999999, 2)); //generate the random number here
        
        $Pipe->setUdf1("Tarek"); //set User defined value
        $Pipe->setUdf2("Magdi"); //set User defined value
        $Pipe->setUdf3("Ahmed"); //set User defined value
        $Pipe->setUdf4("Mohammed"); //set User defined value
        $Pipe->setUdf5("UDF 5"); //set User defined value
        //get results
        if($Pipe->performPaymentInitialization() != $Pipe->SUCCESS){
            echo "Result=" . $Pipe->SUCCESS;
            echo "<br>" . $Pipe->getErrorMsg();
            echo "<br>" . $Pipe->getDebugMsg();
            header("location: http://www.gsikw.com/KNet_Tarek/error.php");
        }
        else{
            $payID = $Pipe->getPaymentId();
            $payURL = $Pipe->getPaymentPage();
            echo $Pipe->getDebugMsg();
            header("location:".$payURL."?PaymentID=".$payID);
        }
        
    }

    public function response()
    {
        $PaymentID = @$_POST['paymentid'];
        $presult = @$_POST['result'];
        $postdate = @$_POST['postdate'];
        $tranid = @$_POST['tranid'];
        $auth = @$_POST['auth'];
        $ref = @$_POST['ref'];
        $trackid = @$_POST['trackid'];
        $udf1 = @$_POST['udf1'];
        $udf2 = @$_POST['udf2'];
        $udf3 = @$_POST['udf3'];
        $udf4 = @$_POST['udf4'];
        $udf5 = @$_POST['udf5'];
        
        if ( $presult == "CAPTURED" )
        {
            $result_url = "https://www.gsikw.com/KNet_Tarek/result.php";
            
           $result_params = "?PaymentID=" . $PaymentID . "&Result=" . $presult . "&PostDate=" . $postdate . "&TranID=" . $tranid . "&Auth=" . $auth . "&Ref=" . $ref . "&TrackID=" . $trackid . "&UDF1=" . $udf1 . "&UDF2=" .$udf2  . "&UDF3=" . $udf3  . "&UDF4=" . $udf4 . "&UDF5=" . $udf5  ;
        
        }
        else
        {
            $result_url = "https://www.gsikw.com/KNet_Tarek/error.php";
            $result_params = "?PaymentID=" . $PaymentID . "&Result=" . $presult . "&PostDate=" . $postdate . "&TranID=" . $tranid . "&Auth=" . $auth . "&Ref=" . $ref . "&TrackID=" . $trackid . "&UDF1=" . $udf1 . "&UDF2=" .$udf2  . "&UDF3=" . $udf3  . "&UDF4=" . $udf4 . "&UDF5=" . $udf5  ;
        
        }
        echo "REDIRECT=".$result_url.$result_params;
        
    }

    public function error()
    {
        echo 'error';
        
    }

    public function result()
    {
        echo 'complete';
        
    }
}
