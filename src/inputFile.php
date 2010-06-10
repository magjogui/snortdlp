<?php include("includes/checklogin.php"); ?>
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
	<?php include("includes/common.php"); ?>
	<?php 
	
	/**
	 * checks if the fileName was past from the post method
	 */
	if (isset($_POST['path']) && !empty($_POST['path'])){
		
		$path = $_POST['path'];
		$config = getConfig();
		$snortFile = $config['snortFile'];
		$substringLength = $config['substringLength'];
		
		/*
		 * gets scoring method
		 */
		if (isset($_POST['scoringMethod']) && !empty($_POST['scoringMethod'])){
			$scoringMethod = $_POST['scoringMethod'];
			
			if(isset($_POST['local'])){
				processFile(1, $path, $path, $scoringMethod, $substringLength, $snortFile);
			} else if (isset($_POST['network'])){
				
				$ip = $_POST['ip'];
				$user = $_POST['user'];
				$pass = $_POST['pass'];
				$path = $_POST['path'];
				$netPath = "//" . $ip . (($path[0] == "/") ? ($path) : ("/" . $path));
				
				$parts = explode("/", $path); //get our path element parts
				$fileName = array_pop($parts);
				$path = implode("/", $parts); //rebuild our path
				
				$path = openShare($ip, $user, $pass, $path) . $fileName;
				processFile(1, $path, $netPath, $scoringMethod, $substringLength, $snortFile);
				closeShare();
			}
		}
			
	}
	
	?>
	<div id="page">
		<div id="content">
		  <div class="post">
				<div class="entry">
				
				<h2 class="title">Process Network File</a></h2>
					<form action="inputFile.php" method="post">
						<table>
						<tr><td><b>IP Address: </b></td<td><input type="text" id="ip" name="ip"/></td>
							<td><b>Network File Path: </b></td<td><input type="text" id="path" name="path"/></td>    
						<tr><td><b>Username: </b></td<td><input type="text" id="user" name="user"/></td>
						    <td><b>Password: </b></td<td><input type="password" id="pass" name="pass"/></td>
						<tr><td><b>Method: </b></td<td>
							<SELECT NAME="scoringMethod">
								<OPTION VALUE=histogram SELECTED>Histogram
								<OPTION VALUE=modifiedhist>Modified histogram
								<OPTION VALUE=multipleRandSamples>Multiple random samples
								<OPTION VALUE=random>Random
							</SELECT></td>
							<input type="hidden" name="network" value="true">
							<tr><td align="left"><input type="submit" id="create" value="Process" /></td></tr>
						</table>						
					</form>
					<br><br>
					<h2 class="title">Process Local File</a></h2>
					<form action="inputFile.php" method="post">
						<table>
						<tr><td><b>File Path: </b><input type="text" id="path" name="path"/></td>
						<td><b>Method: </b>
							<SELECT NAME="scoringMethod">
								<OPTION VALUE=histogram SELECTED>Histogram
								<OPTION VALUE=modifiedhist>Modified histogram
								<OPTION VALUE=multipleRandSamples>Multiple random samples
								<OPTION VALUE=random>Random
							</SELECT></td></tr>
							<input type="hidden" name="local" value="true">
							<tr></tr><td align="left"><input type="submit" id="create" value="Create" /></td></tr>
						</table>						
					</form>
					<br><br><br>
					<h2 class="title">Manage Protected Files</a></h2>
					<br>
					<table>
					<!-- <tr><td><b>Directory</b><td><b>File</b></td></td><td colspan="3" align="center"><b>Action</b></td></tr>--!>
					<tr><td><b>File</b></td><td colspan="3" align="center"><b>Action</b></td></tr>
					<?php 
						include("includes/dbconnect.php");
						
						$query = "SELECT rule_id, file_name, path FROM rules WHERE type = 1";
						$result = mysql_query($query);
						
						while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
							echo "<tr><td width=\"250\">" . $row['path'] ."/". $row['file_name'] . "</td><td><a href=\"display.php?type=file&id=" . $row['rule_id'] . "\">display</a> |</td><td><a href=\"includes/remove.php?type=file&id=" . $row['rule_id'] . "\">delete</a> |</td><td><a href=\"includes/recalculate.php?type=file&id=" . $row['rule_id'] . "\">recalculate</a></td></tr>";
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
