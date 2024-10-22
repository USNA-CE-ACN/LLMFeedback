<?php
   session_start();
   header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
   header("Cache-Control: post-check=0, pre-check=0", false);
   header("Pragma: no-cache");
?>


<?php
	include('session.php');
?>

<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
include("config.php");

$assignment = $db->real_escape_string($_GET['assignment']);
$course = $db->real_escape_string($_GET['course']);
$alpha = $db->real_escape_string($_GET['alpha']);

$user = $db->real_escape_string($login_session);
$sql = "SELECT user_id FROM Users WHERE username = '$login_session'";

$result = $db->query($sql);
$count = $result->num_rows;

echo "<h1>Attempts for " . $alpha . "</h1>";

$sql = "SELECT checkpoint_id, name from Checkpoint where assignment_id = '$assignment'";
$result = $db->query($sql);

if ($result->num_rows > 0){
	// Collect all Questions that are part of this Assignment (all Checkpoints)

	while($row = $result->fetch_row()){
		$checkpoint_id = $row[0];
		$checkpoint_name = $row[1];
		
		echo "<h3>" . $checkpoint_name . "</h3>";
		
		$sql = "SELECT question_id, question_text from Question where checkpoint_id = '$checkpoint_id'";
		$question_result = $db->query($sql);
		
		if($question_result->num_rows > 0){
			while($q_row = $question_result->fetch_row()){
			    echo "<h4>" . $q_row[1] . "</h4>";
				$question_id = $q_row[0];
				$question_str = $question_str . $q_row[0] . ",";
				
				$sql = "select attempt_text from Attempt where alpha = '$alpha' AND question_id = '$question_id'";
				$attempt_result = $db->query($sql);
				
				while($a_row = $attempt_result->fetch_row()){
				    echo "<p>" . $a_row[0] . "</p>";
				}
			}
		}
	}
}else{
    echo "No checkpoints to show!";   
}

$db->close();
?>