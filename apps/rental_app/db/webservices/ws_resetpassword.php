<?php
	if(!isset($_SESSION)) { session_start(); }
	require_once "functions.php";

	if (isset($_GET['email'])) { $email=strtolower(htmlspecialchars(urldecode($_GET['email'])));} else {echo -1001; return;}
	if (isset($_GET['uid'])) { $uid=strtolower(htmlspecialchars(urldecode($_GET['uid'])));} else {echo -1002; return;}

	try {
		$sql = "select * from users where useremail=:email and id=:uid";
		$stmt =$dbRWCo->prepare($sql);
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':uid', $uid);
		$stmt->execute();
		if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$userid=$row['id'];	
			$sessionid=$row['sessionid'];	
		} else {
			echo -1003;
			return;
		}
	}	
 	catch(PDOException $e) {
		echo -1004;
		return;
	}

	/*--Calculate temporary password --*/
	$newpassword=random_str(8);
	$options = [
		'cost' => 12,
	];
	$passwordhash=password_hash($newpassword, PASSWORD_BCRYPT, $options);	

	/*--Update Database --*/
	$sql = 'update users set password=:password where useremail=:email and id=:uid ';
	try { 
		$stmt =$dbRWCo->prepare($sql);
		$stmt->bindParam(':email', $email);
		$stmt->bindParam(':uid', $uid);
		$stmt->bindParam(':password', $passwordhash);
		$stmt->execute();
	}	
	catch(PDOException $e) {
		echo $e->getMessage();
		echo $sql;
		echo -1005;
		return;
	}
	$message=$newpwmessage." ".$newpassword;
	if (sendmail($email,$resetpwsubject,$message)==true) {
		echo $newpwsentconfirmation;
	}  else {
		echo -1006;
	}
?>
