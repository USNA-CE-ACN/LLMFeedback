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

$assignment = $db->real_escape_string($_GET['assignment']);

$sql = "SELECT * from Checkpoint where assignment_id = '$assignment'";
$result = $db->query($sql);

$response = "<h3>Checkpoints</h3><ul>";

if ($result->num_rows > 0){
	while($row = $result->fetch_row()){
		$response = $response . "<li><a href=\"create.php?guid=" . $row[3] . "\">";
		$response = $response . $row[1] . "</a></li>";
	}
}else{
	$response = $response . "No checkpoints in database for this assignment.";
}

$response = $response . "</ul>";
echo $response;

$db->close();
?>