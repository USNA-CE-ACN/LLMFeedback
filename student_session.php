<?php
	session_start();

	if(!isset($_SESSION['alpha'])){
		header("location: student_login.php");
		die();
	}
	$alpha = $_SESSION['alpha'];
?>