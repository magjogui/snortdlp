<?php include("includes/checklogin.php")?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--
Design by Free CSS Templates
http://www.freecsstemplates.org
Released for free under a Creative Commons Attribution 2.5 License

Name       : Milestone
Description: A two-column, fixed-width design for 1024x768 screen resolutions.
Version    : 1.0
Released   : 20100309

-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>PigPen</title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link href="styles/style.css" rel="stylesheet" type="text/css" media="screen" />
</head>
<body>
	<div id="logo">
		<h1><a href="#">PigPen</a></h1>
		<p><em> an open source DLP solution utilizing Snort</em></p>
	</div>
	<hr />
	<!-- end #logo -->
	<?php include("includes/header.php")?>
	<?php include("includes/common.php")?>
	<?php 
	$incomplete = false;
	if (isset($_POST['server']) && !empty($_POST['server'])){
		$server = $_POST['server'];
	}
	
	if (isset($_POST['db']) && !empty($_POST['db'])){
		$db = $_POST['db'];
	}
	
	if (isset($_POST['user']) && !empty($_POST['user'])){
		$user = $_POST['user'];
	}
	
	if (isset($_POST['pass']) && !empty($_POST['pass'])){
		$pass = $_POST['pass'];
	}
	
	if (isset($_POST['table']) && !empty($_POST['table'])){
		$table = $_POST['table'];
	}
	
	if (isset($_POST['port']) && !empty($_POST['port'])){
		$port = $_POST['port'];
	}
	
	if (isset($_POST['column']) && !empty($_POST['column'])){
		$column = $_POST['column'];
	}
	
	if ($server !="" && $db !="" && $user !="" && $pass !="" && $table !="" && $column !=""){
	
	$dbhost = $server . ":" . $port;
	$dbuser = $user;
	$dbpass = $pass;
	
	//flags
	$connError = false;
	$noData = false;
	$incomplete = false;
	
	$conn = mysql_connect($dbhost, $dbuser, $dbpass) or $connError = true;
	if (!$connError){
		$dbname = $db;
		mysql_select_db($dbname);
		
		$column = mysql_real_escape_string($column);
		$table = mysql_real_escape_string($table);
		
		$query = "SELECT $column FROM $table";
		$result = mysql_query($query);
		
		$numRows = mysql_num_rows($result) or $noData = true; //gets the number of rows from the query
		
		if ($numRows > 0){
			if ($numRows < 100000){
				$numRowsUsed = (int)sqrt($numRows); //takes the sqare root to get a smaller samlple
			} else {
				$numRowsUsed = (int)pow($numRows, 1/3); //takes the cube root to get a smaller samlple of extremely large databases
			}
			
			$regex = "/("; //starts the regex value
			
			for($i = 0; $i < $numRowsUsed; $i++){ //loops through the number of number of rows to sample
				$rowNum = (int)rand(($numRows/$numRowsUsed) * $i, ($numRows/$numRowsUsed) * ($i + 1)); //selects a random sample from the numer of rows
				mysql_data_seek($result, $rowNum); //goes to a specific result
				$row = mysql_fetch_assoc($result); // gets the specific result as a $row
				
				$value = $row[$column]; //grabs the random number value
				$value = sanitizeRegex($value); //escapes any reserved regex char
				
				$regex = $regex . $value . "|"; //builds the regex
			}
			
			$regex = substr($regex,0,-1) . ")/i"; //completes the regex
			
			mysql_close($conn); //closes the db connection
			
			$sid = getNextsid();
			$rule = "alert tcp \$HOME_NET any -> \$EXTERNAL_NET any (msg:\"Possible detection of: $table : $column\"; pcre:\"$regex\"; classtype:data-loss; sid:$sid;)";
			
			include("includes/dbconnect.php");
			$query = "INSERT INTO rules (file_name, path, rule, regex, count, sid, type) VALUES ('$table', '$column', '$rule', '$regex', 1, $sid, 4)";
			mysql_query($query);
			include("includes/dbclose.php");
			
			
		} else {
			$noData = true;
		}
	}
	
	} else if(!isset($_POST['server']) && !isset($_POST['server']) && !isset($_POST['table']) && !isset($_POST['db']) && !isset($_POST['port']) && !isset($_POST['user']) && !isset($_POST['pass'])){
		$incomplete = false;
	} else {
		$incomplete = true;
	}
	
	?>
	<div id="page">
		<div id="content">
		  <div class="post">
				<h2 class="title">Process Database Tables</a></h2>
				<div class="entry">
				<?php if($incomplete){
				echo "<font color=\"red\"><b>One of the fields was incomplete!</b></font><br><br>";
				}?>
				<?php if($noData){
				echo "<font color=\"red\"><b>The database table you entered contains no data!</b></font><br><br>";
				}?>
				<?php if($connError){
				echo "<font color=\"red\"><b>There was a database connection error! Please make sure you entered the correct information.</b></font><br><br>";
				}?>
					<form action="database.php" method="post">
						<table>
						<tr><td><b>Server Hostname: </b></td><td><input type="text" id="server" name="server" value="localhost<?php //$_POST['server']?>"/></td></tr>
						<tr><td><b>Port: </b></td><td><input type="text" id="port" name="port" value="3306<?php //$_POST['port']?>"/></td></tr></tr>
						<tr><td><b>Database Name: </b></td><td><input type="text" id="db" name="db" value="testdb<?php //$_POST['db']?>"/></td></tr></tr>
						<tr><td><b>Username: </b></td><td><input type="text" id="user" name="user" value="root<?php //$_POST['user']?>"/></td>
						<tr><td><b>Password: </b></td><td><input type="password" id="pass" name="pass" value="tartans"/></td></tr>
						<tr><td><b>Table: </b></td><td><input type="text" id="table" name="table" value="test<?php //$_POST['table']?>"/></td></tr>
						<tr><td><b>Column: </b></td><td><input type="text" id="column" name="column" value="secret<?php //$_POST['column']?>"/></td></tr>
						<tr><td align="left"><input type="submit" id="create" value="Create" /></td></tr>
						</table>						
					</form>
					<table>
					<br>
					<h2 class="title">Protected Database Tables</a></h2>
					<br>
					<tr><td><b>Table</b></td><td><b>Protected Column</b></td><td colspan="3" align="center"><b>Action</b></td></tr>
					<?php 
						include("includes/dbconnect.php");
						
						$query = "SELECT rule_id, file_name, path FROM rules WHERE type = 4";
						$result = mysql_query($query);
						
						while($row = mysql_fetch_assoc($result)){
							echo "<tr><td>" . $row['file_name'] ."</td><td>". $row['path'] . "</td><td><a href=\"display.php?type=db&id=" . $row['rule_id'] . "\">display</a> |</td><td><a href=\"includes/remove.php?type=db&id=" . $row['rule_id'] . "\">delete</a> |</td><td><a href=#>recalculate</a></td></tr>";
						}					
						include("includes/dbclose.php"); 
						
						
					?>
					</table>
			</div>
		  </div>
		</div><!-- end #content -->
		<div id="sidebar">
			<ul>
				<li>
					<h2>About us:</h2>
					<p>Will Schroeder<br>Tyler Dean</p>
				</li>
			</ul>
		</div>
		<!-- end #sidebar -->
		<div style="clear: both;">&nbsp;</div>
	</div>
	<!-- end #page -->
	<div id="footer">
		<p>All rights reserved. Design by <a href="http://www.freecsstemplates.org/">Free CSS Templates</a>.</p>
	</div>
	<!-- end #footer -->
</body>
</html>
