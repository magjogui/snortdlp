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
	<?php include("includes/common.php"); ?>
	<?php 
	
	$substringLength = 7;
	$snortFile = "";
	$path = "";
	$scoringMethod = "histogram";
	$processFolder = false;
	$includeSubfolders = false;
	
	if (isset($_POST['location']) && !empty($_POST['location'])){
		$path = $_POST['location'];
		$processFolder = true;
		
		// Checks to make sure the inputted path is valid and exists
		if($path{strlen($path)-1} != "/"){
			$path = $path . "/";
			if(realpath($path) == false){
				$path_error = true;
			}
		}
	}
	
	if (isset($_POST['scoringMethod']) && !empty($_POST['scoringMethod'])){
		$scoringMethod = $_POST['scoringMethod'];
	}
	
	if (isset($_POST['includeSubfolders']) && !empty($_POST['includeSubfolders'])){
		$includeSubfolders = $_POST['includeSubfolders'];
	}
	
	if ($processFolder){
		
		$config = getConfig();
		$snortFile = $config['snortFile'];
		$substringLength = $config['substringLength'];
		
		if (isset($_POST['local'])){

			processFolder($path, $path, $includeSubfolders, $scoringMethod, $substringLength, $snortFile);

		} else if (isset($_POST['network'])){
			$ip = $_POST['ip'];
			$user = $_POST['user'];
			$pass = $_POST['pass'];
			$folder = $_POST['location'];
			$netPath = "//" . $ip . (($folder[0] == "/") ? ($folder) : ("/" . $folder . "/"));
			
			
			$path = openShare($ip, $user, $pass, $folder);
			processFolder($path, $netPath, $includeSubfolders, $scoringMethod, $substringLength, $snortFile);
			closeShare();
		}
	}
	
	?>
	<div id="page">
		<div id="content">
		  <div class="post">
				<div class="entry">
				<h2 class="title">Process Network Folder</a></h2>
					<form action="folderPath.php" method="post">
						<table>
						<tr><td><b>IP Address: </b></td<td><input type="text" id="ip" name="ip"/></td>
							<td><b>Network Folder: </b></td<td><input type="text" id="location" name="location"/></td>    
						<tr><td><b>Username: </b></td<td><input type="text" id="user" name="user"/></td>
						    <td><b>Password: </b></td<td><input type="password" id="pass" name="pass"/></td>
						<tr><td><b>Method: </b></td<td>
							<SELECT NAME="scoringMethod">
								<OPTION VALUE=histogram SELECTED>Histogram
								<OPTION VALUE=modifiedhist>Modified histogram
								<OPTION VALUE=multipleRandSamples>Multiple random samples
								<OPTION VALUE=random>Random
							</SELECT></td>
						<tr><td><b>Include subfolders: </b></td<td>
							<input type="checkbox" name="includeSubfolders" id="includeSubfolders" value="true"></input></td></tr>
							<input type="hidden" name="network" value="true">
							<tr><td align="left"><input type="submit" id="create" value="Process" /></td></tr>
						</table>						
					</form>
					<br><br>
				<h2 class="title">Process Local Folder</a></h2>
					<form action="folderPath.php" method="post">
						<table>
						<tr><td><b>Local Folder: </b></td><td><input type="text" id="location" name="location"/></td>
						<tr><td><b>Method: </b></td><td>
							<SELECT NAME="scoringMethod">
								<OPTION VALUE=histogram SELECTED>Histogram
								<OPTION VALUE=modifiedhist>Modified histogram
								<OPTION VALUE=multipleRandSamples>Multiple random samples
								<OPTION VALUE=random>Random
							</SELECT></td>
						<tr><td><b>Include subfolders: </b></td><td>
							<input type="checkbox" name="includeSubfolders" id="includeSubfolders" value="true"></input></td>
							<input type="hidden" name="local" value="true"></tr><tr>
							<td align="middle"><input type="submit" id="create" value="Process" /></td></tr>
						</table>
					</form>
					<br><br>
					<h2 class="title">Manage Protected Folders</a></h2>
					<br>
					<table>
					<tr><td><b>Folder</b></td><td colspan="3" align="center"><b>Action</b></td></tr>
					<?php 
						include("includes/dbconnect.php");
						
						$query = "SELECT rule_id, file_name, path FROM rules WHERE type = 2";
						$result = mysql_query($query);
						$paths = array();
						//grab all of our paths from the database
						while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
							//$parts = explode("/", $row['file_name']); //get our path element parts
							//array_pop($parts); //remove the filename from the end
							//$path = implode("/", $parts) . "/";
							$path = $row['path'];
							array_push($paths, $path);
						}
						$paths = array_unique($paths); //uniquify the $paths array
						
						foreach($paths as $path){
							echo "<tr><td width=\"250\">$path</td><td><a href=\"display.php?type=folder&id=" . urlencode($path) . "\">display</a> |</td><td><a href=\"includes/remove.php?type=folder&id=" . urlencode($path) . "\">delete</a></td></tr>";
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
