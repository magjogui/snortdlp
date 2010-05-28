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
	<?php 
		
		$id = $_GET['id'];
		
		$type = $_GET['type']
		
	?>
	<div id="page">
		<div id="content">
		  <div class="post">
				<h2 class="title">Details</a></h2>
				<div class="entry">
					<?php 
					
						if ($type == "file") {
							if ($id > 0){
								$id = (int)$id;
								include("includes/dbconnect.php");
								$query = "SELECT rule, regex, path, file_name FROM rules WHERE rule_id = $id";
								$result = mysql_query($query);
								
								if (mysql_num_rows($result) > 0){
									$row = mysql_fetch_array($result);
									
									$rule = $row['rule'];
									$regex = $row['regex'];
									$path = $row['path'];
									$fileName = $row['file_name'];
									
									echo "<table>";
									echo "<tr><td><b>File name: </b></td><td>$fileName</td></tr>";
									echo "<tr><td><b>Directory: </b></td><td>$path</td></tr>";
									echo "<tr><td><b>Regular Expression: </b></td><td>$regex</td></tr>";
									echo "<tr><td><b>Snort Rule: </b></td><td>$rule</td></tr>";
									echo "</table>";
								}						
								include("includes/dbclose.php");
							}
						} else if ($type == "folder") {
							include("includes/dbconnect.php");
							$id = mysql_real_escape_string($id);
							$query = "SELECT rule_id, file_name FROM rules WHERE path = '$id'";
							$result = mysql_query($query);
							if (mysql_num_rows($result) > 0){
								echo "<table>";
								echo "<tr><td><b>File</b></td><td colspan=\"3\" align=\"center\"><b>Action</b></td></tr>";
								while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
												
									echo "<tr><td width=\"200\">" . $row['file_name'] . "</td><td><a href=\"display.php?type=file&id=" . $row['rule_id'] . "\">display</a> |</td><td><a href=\"includes/remove.php?type=file&id=" . $row['rule_id'] . "\">delete</a> |</td><td><a href=#>recalculate</a></td></tr>";
										
								}
								echo "</table>";	
							}
							include("includes/dbclose.php");
						} else if ($type == "free"){
							if ($id > 0){
								include("includes/dbconnect.php");
								$query = "SELECT rule, regex FROM rules WHERE rule_id = $id";
								$result = mysql_query($query);
								$row = mysql_fetch_array($result);
								if (mysql_num_rows($result) > 0){
									$rule = $row['rule'];
									$regex = $row['regex'];
										
									$name = $rule;
									$beg = strpos($name, "Possible detection of: ") + 23;
									$end = strpos($name, "\";", $beg + 2);
									$end = $end - $beg;
									$name = substr($name, $beg, $end);
									
									echo "<table>";
									echo "<tr><td><b>Alert Name: </b></td><td>$name</td></tr>";
									echo "<tr><td><b>Regular Expression: </b></td><td>$regex</td></tr>";
									echo "<tr><td><b>Snort Rule: </b></td><td>$rule</td></tr>";
									echo "</table>";
								}
								include("includes/dbclose.php"); 
						}
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
