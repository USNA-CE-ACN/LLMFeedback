<?php
	include('session.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
         <link rel="stylesheet" href="assets/css/main.css" />
    	 <noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>	
</head>

<script>
function createAssignment(){
	var assignmentName = document.getElementById('assignment_name').value;
	var courseId = document.getElementById('course').value;
	
	var xhr = new XMLHttpRequest();
    xhr.open('GET', 'create_assignment.php?course=' + courseId + '&name=' + encodeURIComponent(assignmentName), true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
			loadAssignments();
        }
    };
    
    xhr.send();
}

function createCheckpoint(){
	var checkpointName = document.getElementById('checkpoint_name').value;
	var assignmentId = document.getElementById('assignment').value;
	
	var xhr = new XMLHttpRequest();
    xhr.open('GET', 'create_checkpoint.php?assignment=' + assignmentId + '&name=' + encodeURIComponent(checkpointName), true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
			loadCheckpoints();
        }
    };
    
    xhr.send();
}

function loadAssignments(){
	document.getElementById("assignments").innerHTML = "";
	var courseId = document.getElementById('course').value;
	
	var xhr = new XMLHttpRequest();
    xhr.open('GET', 'load_assignments.php?course=' + courseId, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
			response = xhr.responseText;
			document.getElementById("assignments").innerHTML = response;
			loadCheckpoints();
        }
    };
    
    xhr.send();
}

function loadCheckpoints(){
	document.getElementById("checkpoints").innerHTML = "";
	var assignmentId = document.getElementById('assignment').value;
	
	var xhr = new XMLHttpRequest();
    xhr.open('GET', 'load_checkpoints.php?assignment=' + assignmentId, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
			response = xhr.responseText;
			document.getElementById("checkpoints").innerHTML = response;
        }
    };
    
    xhr.send();
}

function checkProgress(){
	document.getElementById("progress").innerHTML = "";
	var assignmentId = document.getElementById('assignment').value;
	var courseId = document.getElementById('course').value;
	
	var xhr = new XMLHttpRequest();
    xhr.open('GET', 'load_progress.php?assignment=' + assignmentId + '&course=' + courseId, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
			response = xhr.responseText;
			document.getElementById("progress").innerHTML = response;
        }
    };
    
    xhr.send();
}

function showEnroll(){
	document.getElementById("enroll").style.display = "inline";
}

function saveEnrollment(){
	var courseId = document.getElementById('course').value;
	var students = document.getElementById('students').value;
	var url = 'course=' + courseId + '&students=' + encodeURIComponent(students);
	
	var xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_enrollment.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
			document.getElementById("students").value = "";
			document.getElementById("enroll").style.display = "none";
        }
    };
    
    xhr.send(url);
	document.getElementById("students").value = "Processing...";
}
</script>

<body class="is-preload">
<div id="wrapper">
  <header id="header" class="alt">
    <h1>Admin Panel</h1>
  </header>

  <div id ="main">
	<section id="intro" class="main">
	  <div class="spotlight">
	    <div class="content">
	      <header class="major">
			<h2>Manage Assignments and Checkpoints</h2>
	      </header>
		  <h3>Courses</h3>
		  <form>
			<select name="course" id="course" onchange="loadAssignments()">
<?php
	ini_set('display_errors',1);
    error_reporting(E_ALL);
    include("config.php");
	
	$user = $db->real_escape_string($login_session);
	$sql = "SELECT user_id FROM Users WHERE username = '$login_session'";

    $result = $db->query($sql);
    $count = $result->num_rows;
	
	if($count > 0){
		$row = $result->fetch_row();
		$user_id = $row[0];
		
		$sql = "SELECT course_id from CourseAdmins where user_id = '$user_id'";
		$result = $db->query($sql);
		$id_string = "";
		
		for($c = 0; $c < $result->num_rows; $c++){
			$row = $result->fetch_row();
			if($c == 0){
				$id_string = $id_string . $row[0];
			}else{
				$id_string = $id_string . "," . $row[0];
			}
		}
		
		$sql = "SELECT * from Course where course_id in ($id_string)";
		$result = $db->query($sql);
		
		for($c = 0; $c < $result->num_rows; $c++){
			$row = $result->fetch_row();
			echo '<option value="' . $row[0] . '">' . $row[2] . ': ' . $row[1] . '</option>';
		}
	}
?>
			</select>
			<input type="button" value="Edit Enrollment" onclick="showEnroll()" />
		  </form>
		  
		  <div id="enroll" name="enroll" style="display:none">
			<form name="enrollment" id="enrollment">
<textarea id="students" name="students" rows="10" cols="100">Please delete all of the text from this box and paste the MIDS entry for your section(s) starting with the first student's Alpha and ending with the last student's company number.  Press "Save Enrollment" below when you're done.
Example:
200000	DOE	 JOHN	40 COMPUTER ENGINEERING
200004	DOE	 JANE	40 ELECTRICAL ENGINEERING
... </textarea>
				<input type="button" value="Save Enrollment" onclick="saveEnrollment()" />
			</form>
		  </div>
		  
		  <div id="assignments" name="assignments">
			
		  </div>
		  
		  <div id="progress" name="progress">
		  
		  </div>
		  
		  <div id="checkpoints" name="checkpoints">
		  
		  </div>
		  
		  <form name="create_assignment_form" id="create_assignment_form">
			Assignment Name: <input type="text" name="assignment_name" id="assignment_name" />
			<input type="button" value="Create Assignment" onclick="createAssignment()">
		  </form>
		  
		  <form name="create_checkpoint_form" id="create_assignment_form">
			Checkpoint Name: <input type="text" name="checkpoint_name" id="checkpoint_name" />
			<input type="button" value="Create Checkpoint" onclick="createCheckpoint()">
		  </form>
	    </div>
	  </div>
	</section>
   </div>
</div>


	<script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.scrollex.min.js"></script>
    <script src="assets/js/jquery.scrolly.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>
	
<script>
window.onload = function() {
  loadAssignments();
};
</script>
</body>
</html>