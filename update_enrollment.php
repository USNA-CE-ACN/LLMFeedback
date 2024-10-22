<?php
	include('session.php');
?>

<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
include_once('config.php');

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$user = $db->real_escape_string($login_session);
$sql = "SELECT user_id FROM Users WHERE username = '$login_session'";

$result = $db->query($sql);
$count = $result->num_rows;
	
if($count > 0){
	$row = $result->fetch_row();
	$user_id = $row[0];

	// Get form data
	$course = $_REQUEST['course'];
	$students = $_REQUEST['students'];

	$separator = "\r\n";
	$line = strtok($students, $separator);

	while ($line !== false) {
		$words = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
		
		if(count($words) > 0){
			$name = $words[2] . " " . $words[1];
			$alpha = $words[0];
	
			$sql = $db->prepare("INSERT INTO Students (alpha, name, instructor_id, course_id) VALUES (?, ?, ?, ?)");
			$sql->bind_param("ssss", $alpha, $name, $user_id, $course);
			$sql->execute();
			$sql->close();
		}
		
		$line = strtok( $separator );
	}
}

$db->close();
?>