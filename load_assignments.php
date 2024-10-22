<?php
   header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
   header("Cache-Control: post-check=0, pre-check=0", false);
   header("Pragma: no-cache");
?>

<?php
include_once('config.php');

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$course = $db->real_escape_string($_GET['course']);

$sql = "SELECT * from Assignment where course_id = '$course'";
$result = $db->query($sql);

$response = "<h3>Assignments</h3><form name=\"choose_assignment\"><select name=\"assignment\" id=\"assignment\" onchange=\"loadCheckpoints()\">";

if ($result->num_rows > 0){
	while($row = $result->fetch_row()){
		$response = $response . "<option value=\"" . $row[0] . "\">";
		$response = $response . $row[1] . "</option>";
	}
}else{
	$response = $response . "No assignments in database for this course";
}

$response = $response . "</select><input type=\"button\" value=\"View Student Progress\" onclick=\"checkProgress()\" /></form>";
echo $response;

$db->close();
?>