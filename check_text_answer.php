<?php
   session_start();
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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $question_id = intval($_GET['question_id']);
    $answer_given = $db->real_escape_string($_GET['answer_given']);
    $alpha = $_GET['alpha'];
    $alpha = $db->real_escape_string($alpha);
    
	$sql = "SELECT attempt_id from Attempt where alpha = '$alpha' AND question_id = '$question_id'";
	$result = $db->query($sql);
	
	if($result->num_rows > 0){
		// Update Attempt
		$row = $result->fetch_row();
		$sql = $db->prepare("UPDATE Attempt SET attempt_text = ? WHERE attempt_id = ?");
		$sql->bind_param("ss", $answer_given, $row[0]);
		$sql->execute();
		$sql->close();
	}else{
		// First attempt
		$sql = $db->prepare("INSERT INTO Attempt (alpha,question_id,attempt_text) VALUES (?, ?, ?)");
		$sql->bind_param("sss", $alpha, $question_id, $answer_given);
		$sql->execute();
		$sql->close();
	}
	
	$sql = "SELECT question_type, feedback from Question where question_id = '$question_id'";
	$result = $db->query($sql);
	
	if ($result->num_rows > 0){
		$row = $result->fetch_row();
		$question_type = $row[0];
		$feedback = $row[1];
		// Query to get the correct answer(s) and feedback for the given question ID
		$sql = "SELECT answer_text, answer_text_2 FROM Answer WHERE question_id = '$question_id'";

		$result = $db->query($sql);
	
		if ($result->num_rows > 0) {
			$isCorrect = false;
        
			while ($row = $result->fetch_assoc()){
				if($question_type == "exact"){
					if (strcasecmp(trim($row['answer_text']), trim($answer_given)) == 0) {
						$isCorrect = true;
						break;
					}
				}else if($question_type == "contains"){
					if (str_contains(trim($answer_given), $row['answer_text'])){
						$isCorrect = true;
						break;
					}
				}else if($question_type == "regex"){
					if (preg_match("/" . $row['answer_text'] . "/i", trim($answer_given))){
						$isCorrect = true;
						break;
					}
				}else if($question_type == "ngt"){
					if ((float)$answer_given > (float)$row['answer_text']){
						$isCorrect = true;
						break;
					}
				}else if($question_type == "nge"){
					if ((float)$answer_given >= (float)$row['answer_text']){
						$isCorrect = true;
						break;
					}
				}else if($question_type == "nlt"){
					if ((float)$answer_given < (float)$row['answer_text']){
						$isCorrect = true;
						break;
					}
				}else if($question_type == "nle"){
					if ((float)$answer_given <= (float)$row['answer_text']){
						$isCorrect = true;
						break;
					}
				}else if($question_type == "ne"){
					$center = (float)$row['answer_text'];
					$margin = (float)$row['answer_text_2'];
					$low = $center - $margin;
					$high = $center + $margin;
					$ag = (float)$answer_given;
					if ($ag >= $low && $ag <= $high){
						$isCorrect = true;
						break;
					}
				}else if($question_type == "nne"){
					$center = (float)$row['answer_text'];
					$margin = (float)$row['answer_text_2'];
					$low = $center - $margin;
					$high = $center + $margin;
					$ag = (float)$answer_given;
					if ($ag <= $low || $ag >= $high){
						$isCorrect = true;
						break;
					}
				}else{
					echo "ERROR: Unknown question type!";
				}
			}
        
			if ($isCorrect) {
				echo "Correct!";
			} else {
				echo $feedback;
			}
		} else {
			echo "No answer found for the given question ID.";
		}
	}else{
		echo "ERROR: No question found!";
	}
}

$db->close();
?>