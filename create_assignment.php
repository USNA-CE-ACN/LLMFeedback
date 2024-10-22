<?php
   header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
   header("Cache-Control: post-check=0, pre-check=0", false);
   header("Pragma: no-cache");
   include('session.php');
?>

<?php
    include_once('config.php');
    
    // Check connection
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Get form data
    $name = $_GET['name'];
    $course = $_GET['course'];
    
    // Insert into Assignment table
    $sql = $db->prepare("INSERT INTO Assignment (name, course_id) VALUES (?, ?)");
    $sql->bind_param("ss", $name, $course);
    $sql->execute();
    $sql->close();

    $db->close();
?>