<?php
   ini_set('display_errors',1);
   error_reporting(E_ALL);
   include("config.php");
   session_start();
   $error='';
   if($_SERVER["REQUEST_METHOD"] == "POST") {
      // username and password sent from form
      $alpha = $db->real_escape_string($_POST['alpha']);

      $sql = "SELECT name FROM Students WHERE alpha = '$alpha'";

      $result = $db->query($sql);      
      $count = $result->num_rows;

      if($count == 1) {
	 $row = $result->fetch_row();
	 echo $row[0];
         $_SESSION['alpha'] = $alpha;
         header('Location: ' . $_SERVER['HTTP_REFERER']);
      } else {
         $error = "Your Alpha is not registered for any courses.  Please try again.";
      }
   }
?>
<html>
<head>
   <title>Login Page</title>
   <style type = "text/css">
      body {
         font-family:Arial, Helvetica, sans-serif;
         font-size:14px;
      }
      label {
         font-weight:bold;
         width:100px;
         font-size:14px;
      }
      .box {
         border:#666666 solid 1px;
      }
   </style>
</head>
<body bgcolor = "#FFFFFF">
   <div align = "center">
      <div style = "width:300px; border: solid 1px #333333; " align = "left">
         <div style = "background-color:#333333; color:#FFFFFF; padding:3px;"><b>Login</b></div>
         <div style = "margin:30px">
            <form action = "" method = "post">
               <label>Alpha :</label><input type = "text" name = "alpha" class = "box"/><br /><br />
               <input type = "submit" value = " Submit "/><br />
            </form>
            <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
         </div>
      </div>
   </div>
</body>
</html>