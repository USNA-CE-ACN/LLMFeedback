<?php
	include('session.php');
?>

<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
include_once('config.php');

$checkpoint_id = $db->real_escape_string($_POST['checkpoint_id']);

if(isset($url) && $url != ""){
  // Delete old questions for this checkpoint if they already exist
  $sql = "delete from Question where checkpoint_id='$checkpoint_id'";
  $db->query($sql);
}

foreach($_POST["questions"] as &$question){
  $question_text = $db->real_escape_string($question["question"]);
  $question_type = $db->real_escape_string($question["type"]);
  $question_feedback = $db->real_escape_string($question["questionFeedback"]);

  // TODO: Add/Handle priming checkbox
  $priming = 0;
  $sql = $db->prepare("insert into Question(question_type,question_text,checkpoint_id, standard_priming,feedback) values (?,?,?,?,?)");
  $sql->bind_param("ssiis",$question_type,$question_text,$checkpoint_id,$priming,$question_feedback);
  $sql->execute();
  $question_id = $db->insert_id;
  $sql->close();

  if($question_type == "llm"){
    // TODO: Handle different priming info
  }

  foreach($question["answers"] as &$answer){
    $answer_text = $db->real_escape_string($answer);
    $sql = $db->prepare("insert into Answer(question_id,answer_text) values(?,?)");
    $sql->bind_param("is",$question_id,$answer_text);
    $sql->execute();
    $sql->close();
  }
}

echo "Checkpoint Saved Successfully!";

?>