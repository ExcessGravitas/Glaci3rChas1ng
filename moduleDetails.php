<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<!--page for a user to view the details of the modules they are already enrolled on -->
<head>
	<link rel="stylesheet" type="text/css" href="styleDBMS.css">
	<title>Student DBMS</title>

	<?php
	//import functions and connect to DB
	include 'DBfunctions.php';
	$DBConn = DBConnection();
	if ($DBConn->connect_error){
		die ("Connection failed".$DBConn->connect_error);
	}

	//current session user
	$currUN = $_SESSION['user'];

	//sql query to find module details user is enrolled in
	$DETAILSsql = "SELECT * FROM (((Modules
	INNER JOIN Enrolment ON Modules.ModuleID = Enrolment.ModuleID)
	INNER JOIN ModuleDescriptions ON Modules.ModuleID = ModuleDescriptions.ModuleID)
	INNER JOIN Convenors ON Modules.ConvenorID = Convenors.ConvenorID)
	WHERE Enrolment.StudentUsername = '".$currUN."'";
	$DETAILSqry = $DBConn->query($DETAILSsql);
	//a flag - if the user isn't enrolled in any modules yet, will display a message directing them to the enrolment page
	if ($DETAILSqry->num_rows == 0){
		$formFlag = FALSE;
	}else{
		$formFlag = TRUE;
	}
	?>
	<!--javascript to allow more module details to appear in table on button click-->
	<script>
	clicked = false;
	function moredetails(i){
		clicked = !clicked;
		if (clicked){
			show = "block";
		}else{
			show = "none";
		}
		b = "a" + i;
		document.getElementById(b).style.display=show;
	}
	</script>
</head>

<body>
	<h1>Student Portal</h1>

	<div class="sidenav">
		<!-- imports sidenav, which also checks this is a valid session -->
		<?php include 'DBsidebar.php'; ?>
	</div>

	<div class="content">
		<h2>Module Details</h2>
		<?php
		if (!$formFlag){
			echo "<p>You are not yet enrolled on any modules. Please go to the Enrolment page to view module descriptions and enrol.</p>";
		}
		?>
		<div <?php if(!$formFlag){echo "style='display:none'";}?> >
			<?php
				echo "<p>Here you can find details about the modules you have enrolled on.</p>";
				//increment up an integer i as rows are added - allows creation of unique id for each module, so can display further details on button click
				$i = 0;
				 //builds a table to show module details
				while($DETAILSrecord = $DETAILSqry->fetch_assoc()){
					echo "<table>";
					echo "<tr><th style='width:10%'>Code</th><th>Module Name</th><th>Semester</th><th>Credits</th></tr>";
					echo "<tr>";
					echo "<td>".$DETAILSrecord['ModuleID']."</td>";
					echo "<td>".$DETAILSrecord['ModuleName']."</td>";
					echo "<td>".$DETAILSrecord['Semester']."</td>";
					echo "<td>".$DETAILSrecord['Credits']."</td>";
					echo "</tr>";
					echo "</table>";
					//two tables below with display none/block toggled by button
					//uses the integer i preceded by a letter to allow for unique id for the div below which contains further module details
					$b = "a".$i;
					echo "<div id='".$b."' style='display:none'>";
					echo "<table>";
					echo "<tr><th>Description</th></tr>";
					echo "<tr><td>".$DETAILSrecord['Description']."</td></tr>";
					echo "</table>";
					echo "<table>";
					echo "<tr><th style='width:25%'>Convenor</th><th style='width:25%'>Office</th><th>Email</th></tr>";
					echo "<tr>";
					echo "<td>".$DETAILSrecord['FirstName']." ".$DETAILSrecord['Surname']."</td>";
					echo "<td>".$DETAILSrecord['Office']."</td>";
					echo "<td>".$DETAILSrecord['Email']."</td>";
					echo "</tr>";
					echo "</table>";
					echo "</div>";
					//button uses javascript function to toggle the display:none/block of the two tables above within the div
					echo "<button type='button' onclick='moredetails($i)'>More/Less</button>";
					echo "<br /><br />";
					$i += 1;
				}
			?>
		</div>
	</div>
</body>