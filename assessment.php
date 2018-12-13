<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<!-- page that gets assessment details from DB and displays to user -->
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

	//flag that is checked within the body of the page so it knows what to display
	$formFlag = '';
	//sql to check if user has enrolled in modules
	$MODULEsql = "SELECT * FROM Enrolment WHERE StudentUsername = '".$currUN."'";
	$MODULEqry = $DBConn->query($MODULEsql);
	if ($MODULEqry->num_rows == 0){
		//flag to show message to user to enrol in modules
		$formFlag = "NotEnrolled";
	}else{
		//if user has enrolled in modules, sql to get assessment details
		$ASSESSsql = "SELECT * FROM ((Enrolment
		INNER JOIN Assessment ON Enrolment.ModuleID = Assessment.ModuleID)
		INNER JOIN Modules on Enrolment.ModuleID = Modules.ModuleID)
		WHERE Enrolment.StudentUsername = '".$currUN."'";
		$ASSESSqry = $DBConn->query($ASSESSsql);
		//if no assessment data available - flag to show message to user
		if ($ASSESSqry->num_rows == 0){
			$formFlag = "NoData";
		}
	}


	?>
</head>

<body>
	<h1>Student Portal</h1>

	<div class="sidenav">
		<!-- imports sidenav, which also checks this is a valid session -->
		<?php include 'DBsidebar.php'; ?>
	</div>

	<div class="content">
		<h2>Assessment</h2>
		<?php
		if ($formFlag == "NotEnrolled"){
			echo "<p>You are not yet enrolled on any modules. Please go to the Enrolment page to view module descriptions and enrol.</p>";
		}elseif ($formFlag == "NoData"){
			echo "<p>We're sorry, there is no assessment information available currently.</p>";
		}else{
			echo "<p>Here you can find the assessment details of the modules you have enrolled on.</p>";
			echo "<p>For coursework, date refers to the due date of that piece of coursework.</p>";
			//builds a table to display assessment details
			echo "<table>";
			echo "<tr><th>Module</th><th>Assessment Type</th><th>Name</th><th style='width:10%'>Code</th><th style='width:15%'>Date</th></tr>";
			while($ASSESSrecord = $ASSESSqry->fetch_assoc()){
				echo "<tr style='border-bottom: 1px solid #ddd'>";
				echo "<td>".$ASSESSrecord['ModuleName']."</td>";
				echo "<td>".$ASSESSrecord['AssessmentType']."</td>";
				echo "<td>".$ASSESSrecord['AssessmentName']."</td>";
				echo "<td>".$ASSESSrecord['AssessmentID']."</td>";
				echo "<td>".$ASSESSrecord['AssessmentDate']."</td>";
				echo "</tr>";
			}
			echo "</table>";
		}
		?>
	</div>
</body>