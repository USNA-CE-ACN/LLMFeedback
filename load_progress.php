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

$user = $db->real_escape_string($login_session);
$sql = "SELECT user_id FROM Users WHERE username = '$login_session'";

$result = $db->query($sql);
$count = $result->num_rows;

$response = "<h3>Progress</h3>";

$alphas = array();
$names = array();

if($count > 0){
	$row = $result->fetch_row();
	$user_id = $row[0];

	$sql = "SELECT alpha, name from Students where instructor_id = '$user_id' AND course_id = '$course'";
	$result = $db->query($sql);
	$count = $result->num_rows;
	
	if($count > 0){
		while($row = $result->fetch_row()){
			$alphas[] = $row[0];
			$names[] = $row[1];
		}
	}
}

$sql = "SELECT checkpoint_id, name from Checkpoint where assignment_id = '$assignment'";
$result = $db->query($sql);

$checkpoint_num_questions = array();
$checkpoint_num = 0;
$question_ids = array();
$question_str = "";

if ($result->num_rows > 0){
	// Collect all Questions that are part of this Assignment (all Checkpoints)
	
	$response = $response . "<table><tr><th>Alpha</td><th>Name</th>";
	while($row = $result->fetch_row()){
		$checkpoint_id = $row[0];
		$checkpoint_name = $row[1];
		
		$response = $response . "<th>" . $checkpoint_name . "</th>";
		
		$sql = "SELECT question_id from Question where checkpoint_id = '$checkpoint_id'";
		$question_result = $db->query($sql);
		
		$checkpoint_num_questions[] = 0;
		
		if($question_result->num_rows > 0){
			while($q_row = $question_result->fetch_row()){
				$question_ids[] = $q_row[0];
				$question_str = $question_str . $q_row[0] . ",";
				$checkpoint_num_questions[$checkpoint_num]++;
			}
		}
		
		$checkpoint_num = $checkpoint_num + 1;
	}
	
	$response = $response . "</th>";
	
	for($c = 0; $c < count($alphas); $c++){
		$response = $response . "<tr><td><a href=\"view_attempts.php?assignment=" . $assignment . "&course=" . $course . "&alpha=" . $alphas[$c] . "\">" . $alphas[$c] . "</a></td><td>" . $names[$c] . "</td>";
		
		$checkpoint_num = 0;
		$checkpoint_q_remain = $checkpoint_num_questions[$checkpoint_num];
		$checkpoint_q_total = $checkpoint_q_remain;
		$checkpoint_done = 0;
		
		for($d = 0; $d < count($question_ids); $d++){
			$sql = "select attempt_id from Attempt where alpha = '$alphas[$c]' AND question_id = '$question_ids[$d]'";
			$result = $db->query($sql);
			
			if($result->num_rows > 0){
			    $checkpoint_done++;
			}
		    
			$checkpoint_q_remain--;
			
			if($checkpoint_q_remain == 0){
			    $pct = ($checkpoint_done / $checkpoint_q_total) * 100;
			    $response = $response . "<td><progress id=\"checkpoint" . $checkpoint_num . "\" value=\"" . $pct . "\" max=\"100\"> " . $pct . "% </progress></td>";
			    
			    $checkpoint_num++;
			    if($checkpoint_num < count($checkpoint_num_questions)){
		            $checkpoint_q_remain = $checkpoint_num_questions[$checkpoint_num];
		            $checkpoint_q_total = $checkpoint_q_remain;
    		        $checkpoint_done = 0;
			    }
    		}
		}
		
		$response = $response . "</tr>";
	}
		
	$response = $response . "</table>";
}else{
	$response = $response . "No checkpoints to show progress for.";
}

echo $response;

$db->close();
?>