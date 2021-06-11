<?php
	if(!isset($_SESSION)) { session_start(); }
	include "config.php";

	/*-- A J A X  F U N C T I O N S --*/
	function is_ajax() {
		return true;
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}

	/*-- E M A I L   F U N C T I O N S --*/
	function sendmail($to,$subject,$message) {
		$message = wordwrap($message, 70, "\r\n");
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-Type: text/html;charset=UTF-8;';
		$result=mail( $to, $subject, $message,implode("\r\n", $headers) );
		if ($result==false) {return false;} else {return true;}
	}

	/*-- U T I L S --*/
	function url() {
		$url = $_SERVER['REQUEST_URI']; //returns the current URL
		$parts = explode('/',$url);
		$dir = $_SERVER['SERVER_NAME'];
		for ($i = 0; $i < count($parts) - 1; $i++) {
 			$dir .= $parts[$i] . "/";
		}
		return "http://".$dir;
	}

	function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
    	$pieces = [];
   		$max = mb_strlen($keyspace, '8bit') - 1;
    	for ($i = 0; $i < $length; ++$i) {
        	$pieces []= $keyspace[random_int(0, $max)];
		}
    	return strtolower(implode('', $pieces));
	}


	/*-- S Q L L I T E   D A T A B A S E --*/
	$filepath= dirname(__FILE__)."/dbfile/4654vcvx01htpxs56wx678.db";
  	try {
	  	$dbCo = new PDO('sqlite:'.$filepath);
	  	$dbCo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e) {
		echo $e->getMessage();
		return;
	}

	/*-- S Q L L I T E  R W  D A T A B A S E --*/
	$filepath= dirname(__FILE__)."/dbfile/userlogicrw.db";
  	try {
	  	$dbRWCo = new PDO('sqlite:'.$filepath);
	  	$dbRWCo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	}
	catch(PDOException $e) {
		echo $e->getMessage();
		return;
	}

	
	


?>
