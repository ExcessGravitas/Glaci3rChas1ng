<!DOCTYPE html>
<html lang="en">
<!-- basic CSV reader for adding data to database -->
<head>
	<title>My CSV Reader</title>
	<?php
	include 'DBfunctions.php';
	$DBConn = DBConnection();
	if ($DBConn->connect_error){
		die ("Connection failed".$DBConn->connect_error);
	}
	?>
</head>

<body>
	<h1>My CSV Reader</h1>
	<form action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> method="post">
		File name:
		<br /><input type="text" name="Filename" /><br />
		Table name:
		<br /><input type="text" name="Tablename" /><br />
		<input type="submit" />
	</form>
	<?php
		if ($_SERVER["REQUEST_METHOD"]=="POST"){
			$filename = $_POST['Filename'];
			$tablename = $_POST['Tablename'];
			$openfile = fopen($filename, "r");
			while ($data = fgetcsv($openfile)){
				$INSERTsql = "INSERT INTO ".$tablename." VALUES (";
				foreach ($data as $entry){
					$INSERTsql .= '"'.$entry.'",';
				}
				$INSERTsql = rtrim($INSERTsql, ",").")";
				$INSERTqry = $DBConn->query($INSERTsql);
				echo $INSERTsql;
				echo "<br>";
			}
			fclose($openfile);
		}
	?>
</body>