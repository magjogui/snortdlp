<?php

	$pass = $_POST["pass"];
	
	include("dbconnect.php");
	
	$user=mysql_real_escape_string($_POST["uname"]);
	$pass = md5($pass);
	
	$query = "SELECT user_id FROM users WHERE username = '$user' AND password = '$pass'";
	
	$result = mysql_query($query);
	
	if(mysql_num_rows($result)==1){
		session_regenerate_id();
		session_start();
		session_register('user', $user);

	} else {
		header("location: ../index.php?wp=1");
		include("dbclose.php");
		die();
	}
	
	include("dbclose.php");
	
	header("Location: ../index.php");

?>