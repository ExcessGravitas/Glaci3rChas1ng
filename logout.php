<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<!-- simple logout page - unsets and destroys the session, and displays a link back to login page -->
<head>
    <link rel="stylesheet" type="text/css" href="styleAlt.css">
    <title>Student DBMS</title>
	<?php
	session_unset();
	session_destroy();
	?>
</head>


<body>
    <div class="topbar">
        <h1>Student Portal</h1>
    </div>
	<hr />
    <div class="content">
		<h2>Logout Successful</h2>
		<p>Click <a href="http://mersey.cs.nott.ac.uk/~psxps6/">here</a> to return to the login page</p>
    </div>
</body>
</html>
