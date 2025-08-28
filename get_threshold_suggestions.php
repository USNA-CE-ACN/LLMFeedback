<?php
   session_start();
   header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
   header("Cache-Control: post-check=0, pre-check=0", false);
   header("Pragma: no-cache");
?>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function utf8_urldecode($str) {
        return html_entity_decode(preg_replace("/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode($str)), null, 'UTF-8');
}

$question_text = $_REQUEST['question_text'];
$answer_text = $_REQUEST['answer_text'];
	
$prompt = "Task: You are an assistant to a college professor. Give examples of student answers to this question that you would rate 2/10, 4/10, 6/10, and 8/10. Constrain suggested answers to the suggested instructor answer, which should be considered a 10/10 answer to the question. ";
$prompt = $prompt . " Question: " . $question_text;
$prompt = $prompt . " Instructor Answer: " . $answer_text;

require_once 'vendor/autoload.php';
$yourApiKey = file_get_contents("lab_api_key.key");
$client = OpenAI::client($yourApiKey);

$result = $client->chat()->create([
	'model' => 'gpt-4o',
	'messages' => [['role' => 'user', 'content' => $prompt],],]);

$response = $result->choices[0]->message->content;
echo $response;
?>