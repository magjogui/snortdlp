<?php
	//Start session
	session_start();
	//Check whether the session variable
	//SESS_MEMBER_ID is present or not
	$loggedin = false;
	if(session_is_registered('user')) {
		$loggedin = true;
	} else {
		header("location: access-denied.php");
		exit();
	}
?>