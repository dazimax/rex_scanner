<?php
error_reporting(E_ALL);
//Configurations
$toEmail = 'dazimax@gmail.com';
$logData = array();
$isAvilable = false;
$logData = '<b>Rex IDS Scanner v1.0</b>'.'<br>';
$logData .= '<b>============================================</b>'.'<br>';
$logData .= '<b>SERVER IP : '.$_SERVER['SERVER_ADDR'].'</b>'.'<br>';
$logData .= '<b>'.date('Y-m-d H:i:s A').': scan started</b>'.'<br>';
$logData .= '<b>============================================</b>'.'<br>';
$filepaths = null;
$isAvilable = false;

$path = realpath('public_html');
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $filepath)
{
	if(file_exists($filepath))
	{
		if (date('Y-m-d') === date('Y-m-d', filectime($filepath))) 
		{
			$isAvilable = true;
			$logData .= '<b>Node changed file : '.$filepath.'</b>'.'<br/>';
			$logData .= '<b>Modified datetime : '.date('Y-m-d H:i:A', filectime($filepath)).'</b>'.'<br/>';
			$lines = file($filepath);
    		$filedata = array_slice($lines, 0, 500); //take first 500 lines
    		$logData .= '<b>Changed file content : '.'</b><br/>'.implode('<br/>', $filedata);
    		$logData .= '<br/>';
		}
	}
}
$logData .= '<b>============================================'.'</b><br>';
$logData .= '<b>'.date('Y-m-d H:i:s A').': scan completed'.'</b><br>';
$logData .= '<b>============================================'.'</b><br>';

if($isAvilable)
{
    //send email alert
    $message = $logData;
	$subject = 'Rex IDS Scanner Report - '.date('Y-m-d H:i:A');
	$headers = "From: ".$toEmail. "\r\n";
	$headers .= "Reply-To: ".strip_tags($toEmail)."\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    try{
    	if (mail($toEmail, $subject, $message, $headers)) {
     	   echo 'Rex IDS Scanner report has been sent.';
    	} else {
           echo 'There was a problem sending the email.';
    	}
    }
    catch(Exception $e)
    {
    	echo 'Error : '.$e->getMessage();
    	$logData .= '<b>============================================'.'</b></br>';
    	$logData .= '<b>Error :'.$e->getMessage().'</b>';
    	$logData .= '<b>============================================'.'<br></b>';
    }

    //write log
    $logfile = fopen("rex_scanner.log", "w") or die("Unable to open file!");
    fwrite($logfile, $logData);
    fclose($logfile);   
}
?>