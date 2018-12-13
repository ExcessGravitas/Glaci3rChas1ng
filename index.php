<?php
//Start the session
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<!-- initial page you navigate to - contains login and account creation forms -->
<head>
    <link rel="stylesheet" type="text/css" href="styleAlt.css">
    <title>Student DBMS</title>

    <?php
	//PHP importing my functions
	include 'DBfunctions.php';

	//Creating DB connection
	$DBConn = DBConnection();
	if($DBConn->connect_error){
		die("Connection failed:".$DBConn->connect_error);
	}

	//Setting form entry variables and error messages as empty...
	//...for login
	$errLogin = $loginUN = $loginPW = ''; 
	//...for account creation
	$errFN = $errSN = $errUN = $errPW = $errPWC = '';
	$validFN = $validSN = $validUN = $validPW = $tempUN = '';
	$finalError = FALSE;

	//Flag, so the page knows whether to run the account creation or login code block
	$formFlag = '';
	if ($_SERVER["REQUEST_METHOD"]=="POST"){
		$formFlag = $_POST['formFlag'];
	}

	//Code block for login once form posts to self
	if ($formFlag == 'login'){
		//function input_valid ensures field is not empty, and sanitizes input string
		$loginUN = input_valid($_POST['loginUN']);
		//hashes the inputted password for comparison with DB
		$loginPW = hash('sha256', $_POST['loginPW']);
		//getting the hashed password from DB
		$LOGINsql = "SELECT Password FROM Students WHERE Username = '".$loginUN."'";
		$LOGINqry = $DBConn->query($LOGINsql);
		$checkPW = $LOGINqry->fetch_assoc();
		//if no rows in query result, username does not exist, generates error
		if ($LOGINqry->num_rows == 0){
			$errLogin = "Username does not exist, please create an account";
		//if passwords match, logs into system
		}elseif ($loginPW == $checkPW['Password']){
			$_SESSION['user'] = $loginUN;
			$DBConn->close();
			echo "<script> location.href='http://mersey.cs.nott.ac.uk/~psxps6/home.php';</script>";
		//if rows exist but passwords don't match, generates error
		}else {
			$errLogin = "Invalid login, please try again";
		}
	}


	//Code block for account creation once form posts to self
	if ($formFlag == 'createAccount'){
			//function input_valid ensures field is not empty, and sanitizes input string
			if(input_valid($_POST['newFN'])){
				$validFN = input_valid($_POST['newFN']);
			}else{
				$errFN = "* Required Field";
			}
			if(input_valid($_POST['newSN'])){
				$validSN = input_valid($_POST['newSN']);
			}else{
				$errSN = "* Required Field";
			}
			if(input_valid($_POST['newUN'])){
				$tempUN = input_valid($_POST['newUN']);
				//now to check if username is available
				$UNsql = "SELECT * FROM Students WHERE Username = '".$tempUN."'";
				$UNcheck = $DBConn->query($UNsql);
				if($UNcheck->num_rows > 0){
					$errUN = "* Username already taken";
				}else{
					$validUN = $tempUN;
				}
			}else{
				$errUN = "* Required Field";
			}
			//ensures password field completed and matches password confirmation, then hashes password
			if(empty($_POST['newPW'])){
				$errPW = "* Required Field";
			}elseif($_POST['newPW'] != $_POST['confirmPW']){
				$errPWC = "* Password does not match";
			}else {
				$validPW = hash('sha256', $_POST['newPW']);
			}
			//Once all entries are valid, adds them to the database
			if(($validFN != '') and ($validSN != '') and ($validUN != '') and ($validPW != '')){
				$newusersql = "INSERT INTO Students (Username,Password,FirstName,Surname) VALUES ('".$validUN."','".$validPW."','".$validFN."','".$validSN."')";
				//Redirects to a page stating the new account has been created successfully and closes conncetion, otherwise generates an error
				if ($DBConn->query($newusersql) === TRUE) {
					$DBConn->close();
					echo "<script> location.href='http://mersey.cs.nott.ac.uk/~psxps6/newuser.html';</script>";
				} else {
					$finalError = TRUE;
				}
			}
	}
	?>

</head>


<body>
	<div class="topbar">
        <h1>Student Portal</h1>
	</div>
	<hr />
    <div class="content">
		<!-- Login form stuff -->
		<p>Welcome to the Student Portal. Use this site to view your modules, enrol in new modules, view your assessment details and update your contact information.</p>
        <h2>Log In</h2>
        <p>Use your Username and Password to log in below</p>
		<?php
			//Generates an error if there is an issue with login
			if ($errLogin != ''){
				echo "<p class='err1'>".$errLogin."</p>";
			}
		?>
        <form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> method="post">
            <p>Username: <input type="text" name="loginUN" /></p>
            <p>Password: <input type="password" name="loginPW" /></p>
			<input style="display:none" type="text" name="formFlag" value="login" />
            <input type="submit" value="Log in" />
        </form>
        <hr />


		<!-- Account creation form stuff -->
        <h2>New here?</h2>
        <p>Click the button to create a new account <button type="button" onclick="document.getElementById('hidden').style.display='block'">Click Here</button></p>
		<?php
			//Generates an error if there is an issue with inserting data into the DB
			if($finalError){
				echo "<p class='err1'> I'm sorry, there was an error creating the account </p>";
			}
		?>
        <br />
        <form id="hidden"
			style=<?php if($formFlag == 'createAccount'){echo "display:block";}else{echo "display:none";}?>
			action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>
			method="post">
                First Name: <br /><input type="text" name="newFN" value="<?php echo $validFN;?>" />
				<span class="err1"><?php echo $errFN; ?></span>
				<br />
                Surname: <br /><input type="text" name="newSN" value="<?php echo $validSN;?>" />
				<span class="err1"><?php echo $errSN; ?></span>
				<br />
                Username: <br /><input type="text" name="newUN" value="<?php echo $tempUN;?>" />
				<span class="err1"><?php echo $errUN; ?></span>
				<br />
                Password: <br /><input type="password" name="newPW" />
				<span class="err1"><?php echo $errPW; ?></span>
				<br />
                Confirm Password: <br /><input type="password" name="confirmPW" />
				<span class="err1"><?php echo $errPWC; ?></span>
				<br />
				<input style="display:none" type="text" name="formFlag" value="createAccount" />
                <input type="submit" value="Create Account" />
        </form>
	</div>
</body>
</html>
