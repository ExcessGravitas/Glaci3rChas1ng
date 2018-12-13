<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<!-- page with a form for users to view a module's details and enrol, which updates DB -->
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
	//get user's record from DB
	$USERsql = "SELECT * FROM Students WHERE Username = '".$currUN."'";
	$USERqry = $DBConn->query($USERsql);
	$USERrecord = $USERqry->fetch_assoc();

	//flag to show Enrolment form - if user hasn't confirmed degree, form will be hidden
	if (empty($USERrecord['DegreeID'])){
		$formFlag = FALSE;
	}else {
		$formFlag = TRUE;
	}

	//gets the modules from DB that match DegreeID
	$MODULEsql = "SELECT * FROM Modules WHERE DegreeID = '".$USERrecord['DegreeID']."'";
	$MODULEqry = $DBConn->query($MODULEsql);

	//sql query to get module details once form has been submitted with "View Details" selected
	if (($_SERVER['REQUEST_METHOD']=="POST") and ($_POST['request']=="ViewDetails")){
		$DETAILSsql = "SELECT * FROM ((Modules
		INNER JOIN ModuleDescriptions ON Modules.ModuleID = ModuleDescriptions.ModuleID)
		INNER JOIN Convenors ON Modules.ConvenorID = Convenors.ConvenorID)
		WHERE Modules.ModuleID = '".$_POST['Module']."'";
		$DETAILSqry = $DBConn->query($DETAILSsql);
		$DETAILSrecord = $DETAILSqry->fetch_assoc();
		//additional sql query to get assessment details for the selected Module
		$ASSESSsql = "SELECT * FROM Assessment WHERE ModuleID = '".$_POST['Module']."'";
		$ASSESSqry = $DBConn->query($ASSESSsql);
	}
	
	//sql to enrol user on module - inserts their Username and the chosen ModuleID into Enrolment table
	if (($_SERVER['REQUEST_METHOD']=="POST") and ($_POST['request']=="Enrol")){
		//first check the user hasn't already enrolled on the module
		$CHECKsql = "SELECT * FROM Enrolment WHERE StudentUsername = '".$currUN."' AND ModuleID = '".$_POST['Module']."'";
		$CHECKqry = $DBConn->query($CHECKsql);
		if ($CHECKqry->num_rows > 0){
			$enrolmsg = "You are already enrolled on that module.";
		//if not already enrolled, inserts data into table
		}else {
			$ENROLsql = "INSERT INTO Enrolment VALUES ('".$currUN."','".$_POST['Module']."')";
			if ($DBConn->query($ENROLsql) === TRUE){
				$enrolmsg = "You have been successfully enrolled in module ".$_POST['Module'];
			}else{
				$enrolmsg = "I'm sorry, there was an error and we could not enrol you in that module";
			}
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
		<h2>Enrolment</h2>
		<?php
		//Welcome text changes depending on whether user has confirmed their degree
		if (!$formFlag){
			echo "<p>Please confirm the degree you are studying on the home page before enrolling in modules.</p>";
		}else{
			echo "<p>Use the form below to view module details and enrol.</p>";
		}
		?>
		<!--Form that has a drop down selection of modules available for the user's degree (hidden if degree not confirmed)-->
		<form <?php if (!$formFlag){echo "style='display:none'";} ?>
		action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> method="post">
			<select name="Module">
				<option value =''></option>
				<?php //populating the drop down list from the DB with module options
				while($MODULErecord = $MODULEqry->fetch_assoc()){
					echo "<option value='".$MODULErecord['ModuleID']."'>".$MODULErecord['ModuleName']."</option>";
				}
				?>
			</select>
			<br />
			<input type="radio" name="request" value="ViewDetails" />View Details<br />
			<input type="radio" name="request" value="Enrol" />Enrol
			<br />
			<input type="submit" value="Submit" />
			<br /><br />
		</form>

		<!--Tables to display selected module's details - only appears once a module has been selected and form submitted-->
		<div <?php if($_POST['request']!="ViewDetails"){echo "style='display:none'";}?> >
			<table>
				<tr>
					<th>Module Code</th>
					<th>Module Name</th>
					<th>Semester</th>
					<th>Credits</th>
				</tr>
				<tr>
					<td><?php echo $DETAILSrecord['ModuleID'];?></td>
					<td><?php echo $DETAILSrecord['ModuleName'];?></td>
					<td><?php echo $DETAILSrecord['Semester'];?></td>
					<td><?php echo $DETAILSrecord['Credits'];?></td>
				</tr>
			</table>
			<table>
				<tr>
					<th>Module Description</th>
				</tr>
				<tr>
					<td><?php echo $DETAILSrecord['Description'];?></td>
				</tr>
			</table>
			<table>
				<tr>
					<th>Convenor</th>
					<th>Office</th>
					<th>Email</th>
				</tr>
				<tr>
					<td><?php echo $DETAILSrecord['FirstName']." ".$DETAILSrecord['Surname'];?></td>
					<td><?php echo $DETAILSrecord['Office'];?></td>
					<td><?php echo $DETAILSrecord['Email'];?></td>
				</tr>
			</table>
			<table>
				<?php //finally, some PHP to build a final table with the types of assessment
				if ($ASSESSqry->num_rows == 0){
					echo "<tr><td>No assessment information available currently.</td></tr>";
				}else{
					echo "<tr><th style='width:33%'>Assessment Type</th><th>Assessment Name</th></tr>";
					while($ASSESSrecord = $ASSESSqry->fetch_assoc()){
						echo "<tr>";
						echo "<td style='width:20%'>".$ASSESSrecord['AssessmentType']."</td>";
						echo "<td>".$ASSESSrecord['AssessmentName']."</td>";
						echo "</tr>";
					}
				}
				?>
			</table>
		</div>
		<?php
		//displays message - varies depending on whether enrolment was successful (see PHP in head)
		if (($_SERVER['REQUEST_METHOD']=="POST") and ($_POST['request']=="Enrol")){
			echo "<p>".$enrolmsg."</p>";
		}
		?>
	</div>
</body>