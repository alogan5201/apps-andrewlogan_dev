<?php
	if(!isset($_SESSION)) { session_start(); }

	/*-- E R R O R   M A N A G E M E N T --*/
	$showerrors=0;
	ini_set('display_errors', $showerrors);
	ini_set('display_startup_errors', $showerrors);
	error_reporting(E_ALL);


	/*-- E M A I L   S E T T I N G S --*/
	$resetpwsubject="Reset Password";
	$resetpwmessage="In order to reset your password, click on the following link: ";
	$newpwsubject="Your Password";
	$newpwmessage=" Your new temporary password is: ";
	$newpwsentconfirmation="An email has been sent to your address";
 	/*-- G L O B A L   V A R I A B L E S  &  S  E  T   T   I   N   G  S --*/
	global $dbCo;
	global $dbRWCo;



	/*-- H E A D E R --*/
	header('Access-Control-Allow-Origin: *');


?>
