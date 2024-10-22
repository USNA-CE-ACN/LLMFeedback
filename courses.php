<?php
	ini_set('display_errors',1);
	error_reporting(E_ALL);
	echo "<label for=\"course\">Course: </label>\n";
	echo "<select name=\"course\" class=\"course\" required>\n";
	include('config.php');
	$sql = "select course_number from Course";
	$result = $db->query($sql);
	$count = $result->num_rows;
	for($c = 0; $c < $count; $c++){
	       $row = $result->fetch_row();
	       $course = $row[0];
	       echo "<option value=\"" . $course . "\">";
	       echo $course . "</option>\n";
	}
	echo "</select>\n";
?>