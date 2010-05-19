<?php
	$pass = $_POST["pass1"];
	$pass2 = $_POST["pass2"];
	if($pass != $pass2){
		header("Location: index.php?nm=1");
		die();
	}
	include("includes/dbconnect.php");

	$user=mysql_real_escape_string($_POST["uname"]);
	$pass = md5($pass);
	
	$query = "INSERT INTO users (username, password) VALUES ('$user', '$pass')";
	
	
	mysql_query($query);
	include("includes/dbclose.php");
	
	header("Location: index.php");

?>