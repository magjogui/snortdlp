<?php
	$pass = $_POST["pass1"];
	$pass2 = $_POST["pass2"];
	if($pass != $pass2){
		header("location: ../index.php?nm=1");
		die();
	}
	include("dbconnect.php");

	$user=mysql_real_escape_string($_POST["uname"]);
	$pass = md5($pass);
	
	$query = "INSERT INTO users (username, password) VALUES ('$user', '$pass')";
	
	mysql_query($query);
	include("dbclose.php");
	
	session_regenerate_id();
	session_start();
	session_register('user', $user);
	
	header("location: ../index.php");

?>