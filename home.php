<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<!-- home page you land on after login - has a form for user to select their degree which then updates the DB -->
<head>
    <link rel="stylesheet" type="text/css" href="styleDBMS.css">
    <title>Student DBMS</title>

	<?php
	//import functions and connect to DB
	include 'DBfunctions.php';
	$DBConn = DBConnection();
	if ($DBConn->connect_error){
		die("Connection failed".$DBConn->connect_error);
	}
	
	//current user's username
	$currUN = $_SESSION['user'];
	//get user's first name for welcome message
	$USERsql = "SELECT * FROM Students WHERE Username = '".$currUN."'";
	$USERqry = $DBConn->query($USERsql);
	$USERrecord = $USERqry->fetch_assoc();
	$currFN = $USERrecord['FirstName'];

	//set flag so page knows whether to display degree select form for new users
	if ($USERrecord['DegreeID'] == NULL){
		$newUser = TRUE;
	}else{
		$newUser = FALSE;
	}

	//get degree options from DB
	$DEGREEsql = "SELECT * FROM Degrees";
	$DEGREEqry = $DBConn->query($DEGREEsql);

	//add the user's degree choice to the DB once form posted
	if ($_SERVER["REQUEST_METHOD"]=="POST"){
		$degreeChoice = $_POST['Degree'];
		$ADDdegsql = "UPDATE Students SET DegreeID = '".$degreeChoice."' WHERE Username = '".$currUN."'";
		$ADDdegqry = $DBConn->query($ADDdegsql);
		$newUser = FALSE;
	}

	//if not a new user, get current degree for welcome message (set it as empty first)
	$currDeg = '';
	if (!$newUser){
		$CURRDEGsql = "SELECT DegreeName FROM Degrees,Students WHERE Students.Username = '".$currUN."' AND Students.DegreeID = Degrees.DegreeID";
		$CURRDEGqry = $DBConn->query($CURRDEGsql);
		$CURRDEGrecord = $CURRDEGqry->fetch_assoc();
		$currDeg = $CURRDEGrecord['DegreeName'];
	}
	?>
</head>


<body>
    <h1>Student Portal</h1>

    <div class="sidenav">
        <!-- imports sidenav, which also checks this is a valid session -->
		<?php include 'DBsidebar.php';?>
    </div>

	<div class='content'>
		<!-- Welcome message for 'old' users, hidden if $newUsers=TRUE -->
		<div <?php if ($newUser){echo "style='display:none'";} ?> >
			<?php
			echo "<h2>Welcome ".$currFN."</h2>";
			echo "<p>You are studying ".$currDeg.".</p>";
			?>
			<p>If this is not your degree, please contact Student Services.</p>
			<p>Use the menu on the left to navigate the Student Portal.</p>
		</div>

	<!-- Form for 'new' users to register for a degree course, hidden if $newUsers=FALSE -->
		<div <?php if (!$newUser){echo "style='display:none'";} ?> >
			<?php echo "<h2>Welcome ".$currFN."</h2>"; ?>
			<p>Please confirm the degree you will be studying from the list below.</p>
			<p>Once confirmed, you will be able to enrol in modules and view module and assessment details.</p>
			<form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> method="post">
				<select name="Degree">
					<option value=''></option>
					<?php //populating the drop-down list from the DB
					while($DEGREErecord = $DEGREEqry->fetch_assoc()){
						echo "<option value='".$DEGREErecord['DegreeID']."'>".$DEGREErecord['DegreeName']."</option>";
					}
					?>
				</select>
				<br /><br />
				<input type="submit" value="Confirm" />
			</form>
		</div>
	</div>

</body>
</html>
