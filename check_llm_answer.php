<?php
   session_start();
   header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
   header("Cache-Control: post-check=0, pre-check=0", false);
   header("Pragma: no-cache");
?>

<?php
include_once('config.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function utf8_urldecode($str) {
        return html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode($str)), null, 'UTF-8');
}

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question_id = intval($_POST['question_id']);
    $answer_given = $db->real_escape_string($_POST['answer_given']);
    $alpha = $_POST['alpha'];
    $alpha = $db->real_escape_string($alpha);

    $sql = "SELECT question_text, standard_priming, feedback, threshold from Question where question_id = '$question_id'";
    $result = $db->query($sql);

    if ($result->num_rows > 0){
       	$row = $result->fetch_row();
	$question_text = $row[0];
	$standard_priming = $row[1];
	$feedback = $row[2];
	$threshold = $row[3];
	
	$prompt = "Task: You are a teaching assistant for a freshman-level course.  A student is going to answer this question.  Rate their answer out of 10 based on the answer that I am expecting and provide feedback to the student on their answer compared to the expected answer.  Do not give the correct answer or directly suggest improvements to the student's answer.  Format the answer as Rating: X/10 Feedback: ...";
		
	/*
	if($standard_priming == 0){
		$sql = "SELECT priming_text from Priming WHERE question_id = '$question_id'";
		$result = $db->query($sql);
		
		if($result->num_rows > 0){
			$row = $result->fetch_row();
			$prompt = $row[0];
		}
	}
	*/
		
	$prompt = $prompt . " Question: " . $question_text;
		
	// Query to get the correct answer(s) and feedback for the given question ID
	$sql = "SELECT answer_text FROM Answer WHERE question_id = '$question_id'";
	$result = $db->query($sql);
	$num_answer = 1;

	if ($result->num_rows > 0) {
		$isCorrect = false;
        
		while ($row = $result->fetch_assoc()) {
			$prompt = $prompt . " Correct Answer " . $num_answer . ": " . $row['answer_text'];
			$num_answer++;
		}
			
		$prompt = $prompt . " Student Answer: " . $answer_given;

		$sql = $db->prepare("INSERT INTO Attempt (alpha,question_id,attempt_text) VALUES (?, ?, ?)");
		$sql->bind_param("sss", $alpha, $question_id, $answer_given);
		$sql->execute();
		$sql->close();

		require_once 'vendor/autoload.php';
		$yourApiKey = file_get_contents("lab_api_key.key");
		$client = OpenAI::client($yourApiKey);

		$result = $client->chat()->create([
			'model' => 'gpt-4o',
			'messages' => [['role' => 'user', 'content' => $prompt],],]);

 		$response = $result->choices[0]->message->content;
		preg_match('/\d+/', $response, $matches);
		$rating = $matches[0];
		$feedback = strstr($response,"Feedback");

		if((int)$rating >= (int)$threshold){
			echo "GOOD " . $feedback;
		}else{
			echo "BAD " . $feedback;
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