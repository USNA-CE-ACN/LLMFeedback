<!DOCTYPE html>
<html lang="en">
<head>
<title>Test</title>
</head>

<body>
<?php
   require_once 'vendor/autoload.php';

   $question = $_POST['question'];
   $expected_answer = $_POST['eanswer'];
   $answer = $_POST['answer'];
   $yourApiKey = "sk-proj-dIFqKtnnIQd8gNujkmAeT3BlbkFJ8MsSIu75BjROrVsmzFPt";
   echo "Key: " . $yourApiKey . "<br/>";
   $client = OpenAI::client($yourApiKey);

   echo "HERE";

   $prompt = "A student will answer a question.  You will rate their answer out of 10 based on the answer that I am expecting and provide feedback to the student on their answer compared to the expected answer.  Question: " . $question . " Expected answer: " . $expected_answer . " Phrase your response as if you are talking to a sophomore non-engineering student. Do not give the correct answer or directly suggest improvements to the student's answer. Ask them to try again if the answer if not above an 8/10. Student answer: " . $answer;
	
  $result = $client->chat()->create([
      'model' => 'gpt-3.5-turbo',
      'messages' => [['role' => 'user', 'content' => $prompt],],]);

  echo $result->choices[0]->message->content;
?>
</body>
</html>
