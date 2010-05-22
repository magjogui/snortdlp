<?php 
	$loggedin = false;
	session_start();
	if(session_is_registered('user')) {
		$loggedin = true;
	}
?>
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
<?php 
	$nomatch = false;
	$wrongpass = false;
	if($_GET["nm"] == 1){
		$nomatch = true;
	} elseif ($_GET["wp"] == 1){
		$wrongpass = true;
	}
?>
<body>
	<div id="logo">
		<h1><a href="#">PigPen</a></h1>
		<p><em> an open source DLP solution utilizing Snort</em></p>
	</div>
	<hr />
	<!-- end #logo -->
	<?php 
		// header include
		include("includes/header.php");
	?>
	<div id="page">
		<div id="content">
		  <div class="post">
				<h2 class="title">Welcome to PigPen</a></h2>
				<div class="entry">
					<p>This is <strong>PigPen</strong>, an open source data loss prevention solution that utilizes Snort to detect the exfiltration of sensitive data.</p>
					<?php 
						if (!$loggedin){
							include("includes/dbconnect.php");
							$query = "SELECT * FROM users";
							$result = mysql_query($query);
							if(mysql_num_rows($result)>0) {
								if($wrongpass){
									echo "<b><font color=\"red\"><strong>Incorrect username or password.</strong></b></font><br><br>";
								}
								echo "<form action=\"includes/login.php\" method=\"post\">";
								echo "<table>";
								echo "<tr><td><b>Username: </b></td><td><input type=\"text\" id=\"uname\" name=\"uname\" /></td></tr>";
								echo "<tr><td><b>Password: <b></td><td><input type=\"password\" id=\"pass\" name=\"pass\" /></td></tr>";
								echo "<tr><td><input type=\"submit\" id=\"login\" value=\"Login\" /></td></tr>";
								echo "</table>";
								echo "</form>";
							} else {
								if($nomatch){
									echo "<b><font color=\"red\"><strong>Passwords did not match. Please try again.</strong></b></font><br><br>";
								}
								echo "<form action=\"includes/register.php\" method=\"post\">";
								echo "<table>";
								echo "<tr><td><b>Username: </b></td><td><input type=\"text\" id=\"uname\" name=\"uname\" /></td></tr>";
								echo "<tr><td><b>Password: <b></td><td><input type=\"password\" id=\"pass1\" name=\"pass1\" /></td></tr>";
								echo "<tr><td><b>Re-type password: <b></td><td><input type=\"password\" id=\"pass2\" name=\"pass2\" /></td></tr>";
								echo "<tr><td><input type=\"submit\" id=\"register\" value=\"Register\" /></td></tr>";
								echo "</table>";
								echo "</form>";
							}
							include("includes/dbclose.php");
						} else {
							echo "<form method=\"link\" action=\"includes/logout.php\">";
							echo "<input type=\"submit\" value=\"Logout\">";
							echo "</form>";
						}
					?>
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
