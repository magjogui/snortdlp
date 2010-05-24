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
	<?php include("includes/header.php"); ?>
	<div id="page">
		<div id="content">
		  <div class="post">
				<h2 class="title">Manage Protected Files</a></h2>
				<div class="entry">
					<form action="manageFiles.php" method="post"> 
						<table>
						<tr><td><b>Search: </b><input type="text" id="searchName" name="searchName" /></td>
						<td><input type="submit" id="find" value="Search" /></td></tr>
						</table>
					</form>
					<?php $searchTerm = $_POST["searchName"]; 
					if ($searchTerm != null){
						echo "<br>";
						echo "<font color=\"blue\">Search for \"<b>$searchTerm</b>\"</font>";
						echo "<br>";
						echo "<br>";
					}
					?>
					<table>
					<tr><td><b>File</b></td><td colspan="2" align="center"><b>Action</b></td></tr>
					<?php 
						include("includes/dbconnect.php");
						
						if ($searchTerm != null){
							$searchTerm = mysql_real_escape_string($searchTerm);
						} else {
							$searchTerm = "";
						}
						
						$query = "SELECT rule_id, file_name FROM rules WHERE file_name LIKE '%$searchTerm%'"; //WHERE type = 1";
						$result = mysql_query($query);
						
						while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
							echo "<tr><td width=\"250\">" . $row['file_name'] . "</td><td><a href=\"includes/remove.php?type=file&id=" . $row['rule_id'] . "\">delete</a> |</td><td><a href=#>recalculate</a></td></tr>";
					
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
