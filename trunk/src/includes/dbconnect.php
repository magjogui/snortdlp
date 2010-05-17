<?php
	$dbhost = 'localhost:3306';
	$dbuser = 'snortdlp';
	$dbpass = 'tartans';
	
	$conn = mysql_connect($dbhost, $dbuser, $dbpass) or die  ('Error connecting to mysql');
	
	$dbname = 'snortdlp';
	mysql_select_db($dbname);
?>