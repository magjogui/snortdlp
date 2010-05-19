<?php
	session_start();
	$loggedin = false;
	if(session_is_registered('user')) {
		$loggedin = true;
	} else {
		header("location: access-denied.php");
		exit();
	}
?>