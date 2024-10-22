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
function createCheckpoint(){
	var checkpointName = document.getElementById('assignment_name').value;
	var courseId = document.getElementById('course').value;
	
	var xhr = new XMLHttpRequest();
    xhr.open('GET', 'create_assignment.php?course=' + courseId + 'name=' + encodeURIComponent(assignmentName), true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
			response = xhr.responseText;
			loadAssignments();
        }
    };
    
    xhr.send();
}

function loadCheckpoints(){
	document.getElementById("assignments").innerHTML = "";
	var courseId = document.getElementById('course').value;
	
	var xhr = new XMLHttpRequest();
    xhr.open('GET', 'load_assignments.php?course=' + courseId, true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
			response = xhr.responseText;
			document.getElementById("assignments").innerHTML = response;
        }
    };
    
    xhr.send();
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
			<h2>Manage Assignment Checkpoints</h2>
	      </header>
		  <form>
			<select name="assignment" id="assignment" onchange="loadCheckpoints()">
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
		  </form>
		  
		  <div id="assignments" name="assignments">
			
		  </div>
		  
		  <form name="create_assignment_form" id="create_assignment_form">
			Assignment Name: <input type="text" name="assignment_name" id="assignment_name" />
			<input type="button" value="Create Assignment" onclick="createAssignment()">
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