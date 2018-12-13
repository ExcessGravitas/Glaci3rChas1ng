<?php
	//runs a check - if session isn't valid, redirects to login page - prevents bypassing of login page
	if (empty($_SESSION['user'])){
		echo "<script> location.href='http://mersey.cs.nott.ac.uk/~psxps6/';</script>";
	}

	//sidebar navigation
	echo '<br /><br />
	<a href="http://mersey.cs.nott.ac.uk/~psxps6/home.php">Home</a><br />
    <a href="http://mersey.cs.nott.ac.uk/~psxps6/studentDetails.php">Student Details</a><br />
    <a href="http://mersey.cs.nott.ac.uk/~psxps6/moduleDetails.php">Module Details</a><br />
    <a href="http://mersey.cs.nott.ac.uk/~psxps6/enrolment.php">Enrolment</a><br />
    <a href="http://mersey.cs.nott.ac.uk/~psxps6/assessment.php">Assessment</a><br />
	<br /><br />
	<hr />
	<br />
	<form action="http://mersey.cs.nott.ac.uk/~psxps6/logout.php" method="post">
	<input class=logout type="submit" value="Log out" />
	</form>';
?>