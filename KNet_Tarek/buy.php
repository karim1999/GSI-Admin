<!--
This merchant demo is published by Knet as a demonstration of the process
of Online Knet Payment Gateway Transactions. Note however that this is not
a fully running demo and there are parts that the merchant has to build him self.
Also, this demo is not tested for security or stability, and Knet does not intend to recommend
this for production purposes. Merchants should build their own web pages based on their needs. 
This demo is just a guide as to what the whole process will look like.
/*Developed by saqib 18-08-2009*/
-->
<?php
ob_start();
ini_set("display_errors", "1");
error_reporting(E_ALL);

require_once "e24PaymentPipe.inc.php";
$Pipe = new e24PaymentPipe;

$Pipe->setAction(1);
$Pipe->setCurrency(414);
$Pipe->setLanguage("ENG"); //change it to "ARA" for arabic language
$Pipe->setResponseURL("http://www.gsikw.com/KNet_Tarek/response.php"); // set your respone page URL
$Pipe->setErrorURL("http://www.gsikw.com/KNet_Tarek/error.php"); //set your error page URL
$Pipe->setAmt("10"); //set the amount for the transaction
//$Pipe->setResourcePath("/Applications/MAMP/htdocs/php-toolkit/resource/");
$Pipe->setResourcePath("/home/gsikwcom/public_html/KNet_Tarek/resource/"); //change the path where your resource file is
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
?>