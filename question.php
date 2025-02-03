<?php
   header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
   header("Cache-Control: post-check=0, pre-check=0", false);
   header("Pragma: no-cache");
?>

<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Lab Checkpoint Question</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="assets/css/main.css" />
    <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
	
<script>
var doneTypingInterval = 1000;
var typingTimer;

function checkAfterTime(questionId){
	clearTimeout(typingTimer);
	typingTimer = setTimeout(checkText, doneTypingInterval, questionId);
}

  function checkLLM(questionId){
    var answerGiven = document.getElementById('answer' + questionId).value;
    var alpha = document.getElementById('alpha').value;
    var xhr = new XMLHttpRequest();
	var params = "question_id=" + questionId + "&alpha=" + alpha + "&answer_given=" + encodeURIComponent(answerGiven);
    xhr.open('POST', 'check_llm_answer.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    document.cookie = "alpha=" + alpha;

    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
			response = xhr.responseText;

			var splits = response.split("Feedback")
			var summary = splits[0].trim();
			var feedback = splits[1].trim().substring(1);
			
			if(summary.includes("GOOD")){
				document.getElementById('answer' + questionId).style.background = "#2bf060";
				document.getElementById('feedback' + questionId).innerHTML = "Good answer!";
			}else{
				document.getElementById('answer' + questionId).style.background = "#ff776e";
				document.getElementById('feedback' + questionId).innerHTML = feedback;
			}
        }
    };
    
    xhr.send(params);
}

function checkText(questionId){
	var answerGiven = document.getElementById('answer' + questionId).value;
	var alpha = document.getElementById('alpha').value;
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'check_text_answer.php?question_id=' + questionId + '&alpha=' + alpha + '&answer_given=' + encodeURIComponent(answerGiven), true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    document.cookie = "alpha=" + alpha;
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
			response = xhr.responseText;
			if(response.includes("Correct")){
				document.getElementById('answer' + questionId).style.background = "#2bf060";
				document.getElementById('feedback' + questionId).innerHTML = "";
			}else{
			    document.getElementById('answer' + questionId).style.background = "#ff776e";
			    document.getElementById('feedback' + questionId).innerHTML = xhr.responseText;
			}
        }
    };
    
    xhr.send();
}

function checkMC(questionId){
	var answerGiven = document.querySelector('input[name="answer' + questionId + '"]:checked').value;
	var alpha = document.getElementById('alpha').value;
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'check_mc_answer.php?question_id=' + questionId + '&alpha=' + alpha + '&answer_given=' + encodeURIComponent(answerGiven), true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	
    document.cookie = "alpha=" + alpha;
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
			response = xhr.responseText;
			if(response.includes("Correct")){
				document.getElementById('question_' + questionId).style.background = "#2bf060";
				document.getElementById('feedback' + questionId).innerHTML = "";
			}else{
			    document.getElementById('question_' + questionId).style.background = "#ff776e";
			    document.getElementById('feedback' + questionId).innerHTML = xhr.responseText;
			}
        }
    };
    
    xhr.send();
}
</script>

</head>
<body class="is-preload">
  <div id="wrapper">
    <header id="header" class="alt">
      <h1>Answer Lab Checkpoint Question</h1>
    </header>
    <form id="answerForm">
      <div id ="main">
	<section id="intro" class="main">
	  <div class="spotlight">
	    <div class="content">
	      <header class="major">
		<h2>Checkpoint Information and Description</h2>
	      </header>
<?php
    ini_set('display_errors',1);
    error_reporting(E_ALL);
    include("config.php");
	
    $guid = $db->real_escape_string($_GET["guid"]);
    $sql = "SELECT * FROM Checkpoint WHERE guid = '$guid'";

    $result = $db->query($sql);
    $count = $result->num_rows;
	
    $name = "Not Found";
    $checkpoint_id = 0;
	
	if($count == 1) {
		$row = $result->fetch_row();
		$name = $row[1];
		$checkpoint_id = $row[0];
	}
?>
	 <label for="name">Name: </label>
	 <input type="text" name="description" class="name" readonly value=	  
<?php
	echo "\"" . $name . "\"";
?>
	 required>
	 <label for="alpha">Alpha: </label>
	 <input type="text" name="alpha" id="alpha" value="<?php if(isset($_COOKIE['alpha'])){ echo $_COOKIE['alpha']; } ?>" required>
	 </div>
	</div>
   </section>
   </div>
<?php
	if($checkpoint_id > 0){
		$sql = "SELECT * from Question where checkpoint_id = '$checkpoint_id'";
		$result = $db->query($sql);
		$count = $result->num_rows;
		
		for($i = 0; $i < $count; $i++){
			$row = $result->fetch_row();
			$q_num = $row[0];
			$q_type = $row[1];
			$q_text = $row[2];
			$q_priming = $row[4];
			
			echo '<div id ="main">';
			echo '<section id = "question_' . $q_num . '" class="main">';
			echo '<div class="content">';
			echo '<h3>' . nl2br(stripslashes($q_text)) . '</h3>';
			
			if($q_type == "llm"){
			    echo '<textarea id="answer' . $q_num . '" name="answer_given" rows="5" cols="100"></textarea>';
				echo '<input type="button" id="check' . $q_num . '" value="Check Answer" onclick="checkLLM(' . $q_num . ')">';
			    echo '<div id="attempts' . $q_num . '">Attempts: </div>';
			}else if($q_type == "mc"){
				$sqla = "SELECT answer_text from Answer where question_id = '$q_num'";
				$resulta = $db->query($sqla);
				$counta = $resulta->num_rows;
				
				echo '<table>';
				for($j = 0; $j < $counta; $j++){
					$rowa = $resulta->fetch_row();
					echo '<tr><td style="padding: 0"><input type="radio" id="answer' . $q_num . '" name="answer' . $q_num . '"';
					echo 'onclick="checkMC(' . $q_num . ')" value="' . $rowa[0] . '"></td>';
					echo '<td style="padding-left: 10px">' . $rowa[0] . '</td></tr>';
				}
				echo '</table>';
			}else if($q_type == "ms"){
				
			}else{
				echo '<input type="text" id="answer' . $q_num . '" name="answer_given" onkeyup="checkAfterTime(' . $q_num . ')">';
			}
			echo '<div id="feedback' . $q_num . '"></div>';			
			echo '</div>';
			echo '</section>';
			echo '</div>';
		}
	}
?>
    </div>
	  
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.scrollex.min.js"></script>
    <script src="assets/js/jquery.scrolly.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="script.js"></script>
    </form>
  </div>
</body>
</html>
