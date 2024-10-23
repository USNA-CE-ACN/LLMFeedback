<?php
	include('session.php');
?>

<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
include_once('config.php');

$checkpoint_id = $db->real_escape_string($_POST['checkpoint_id']);

if(isset($checkpoint_id) && $checkpoint_id != ""){
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
  if($question_type == "llm"){
    $threshold = $question["threshold"];
	$sql = $db->prepare("insert into Question(question_type,question_text,checkpoint_id, standard_priming,feedback,threshold) values (?,?,?,?,?,?)");
	$sql->bind_param("ssiiss",$question_type,$question_text,$checkpoint_id,$priming,$question_feedback,$threshold);
	$sql->execute();
  }else{
	$sql = $db->prepare("insert into Question(question_type,question_text,checkpoint_id, standard_priming,feedback) values (?,?,?,?,?)");
	$sql->bind_param("ssiis",$question_type,$question_text,$checkpoint_id,$priming,$question_feedback);
	$sql->execute();
  }
	
  $question_id = $db->insert_id;
  $sql->close();

  $correct = [];
  
  foreach($question["answer_ids"] as $id){
	  $correct[] = "0";
  }
  
  foreach($question["correct"] as $c){
	  $correct[((int)$c)-1] = "1";
  }

  $all_answers = array_combine($question["answers"], $correct);

  foreach($all_answers as $answer_text => $correct){
    $answer_text = $db->real_escape_string($answer_text);
	if($question_type == "ne" || $question_type == "nne"){
		$margin = $question["margin"];
		$sql = $db->prepare("insert into Answer(question_id,answer_text,answer_text_2,correct) values(?,?,?,?)");
		$sql->bind_param("issi",$question_id,$answer_text,$margin,$correct);
		$sql->execute();
		$sql->close();
	}else{
		$sql = $db->prepare("insert into Answer(question_id,answer_text,correct) values(?,?,?)");
		$sql->bind_param("isi",$question_id,$answer_text,$correct);
		$sql->execute();
		$sql->close();
	}
  }
}

echo "Checkpoint Saved Successfully!";

?>