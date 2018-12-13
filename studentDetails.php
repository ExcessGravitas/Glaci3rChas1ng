<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<!-- page for displaying and updating a user's student details -->
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

	//add contact details to database
	if ($_SERVER["REQUEST_METHOD"]=="POST"){
		$AddressL1 = input_valid($_POST['AddressL1']);
		$Postcode = input_valid($_POST['Postcode']);
		$ContactNo = input_valid($_POST['ContactNo']);
		if ($AddressL1){
			$ADDRsql = "UPDATE Students SET AddressLine1 = '".$AddressL1."' WHERE Username = '".$currUN."'";
			$ADDRqry = $DBConn->query($ADDRsql);
		}
		if ($Postcode){
			$PCsql = "UPDATE Students SET Postcode = '".$Postcode."' WHERE Username = '".$currUN."'";
			$PCqry = $DBConn->query($PCsql);
		}
		if ($ContactNo){
			$CNsql = "UPDATE Students SET ContactNo = '".$ContactNo."' WHERE Username = '".$currUN."'";
			$CNqry = $DBConn->query($CNsql);
		}
	}

	//import student data from DB
	$STUDENTsql = "SELECT * FROM Students WHERE Username = '".$currUN."'";
	$STUDENTqry = $DBConn->query($STUDENTsql);
	$STUDENTrecord = $STUDENTqry->fetch_assoc();
	?>
</head>


<body>
    <h1>Student Portal</h1>

    <div class="sidenav">
		<!-- imports sidenav, which also checks this is a valid session -->
		<?php include 'DBsidebar.php'; ?>
    </div>

    <div class="content">
		<h2>Student Details</h2>
		<!--table of user's details that are already in DB -->
		<table>
			<tr>
				<th>First Name</th>
				<th>Surname</th>
			</tr>
			<tr>
				<td><?php echo $STUDENTrecord['FirstName']; ?></td>
				<td><?php echo $STUDENTrecord['Surname']; ?></td>
			</tr>
			<tr>
				<th>Address</th>
				<th>Contact Number</th>
			</tr>
			<tr>
				<td>
				<?php
				if (empty($STUDENTrecord['AddressLine1']) and empty($STUDENTrecord['Postcode'])){
					echo "<i>No data</i>";
				}else{
					echo $STUDENTrecord['AddressLine1'];
				} ?>
				</td>
				<td>
				<?php
				if (empty($STUDENTrecord['ContactNo'])){
					echo "<i>No data</i>";
				}else{
					echo $STUDENTrecord['ContactNo'];
				} ?>
				</td>
			</tr>
			<tr>
				<td>
				<?php echo $STUDENTrecord['Postcode']; ?>
				</td>
			</tr>
		</table>
		<hr />
		<h3>Update Details</h3>
		<p>Use the form below to update your contact details.</p>
		<p>Please note it is <b>your</b> responsibility to ensure accurate and up-to-date contact information.</p>
		<!--form to update details held in database (php in header) -->
		<form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> method ="post">
			Address Line 1:
			<br /><input type="text" name="AddressL1" /><br />
			Postcode:
			<br /><input type="text" name="Postcode" /><br /><br />
			Contact Number:
			<br /><input type="text" name="ContactNo" /><br />
			<input type="submit" value="Update" />
		</form>
    </div>    
    </body>
</html>
