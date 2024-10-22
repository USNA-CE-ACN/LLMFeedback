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
    $assignment = $_GET['assignment'];
    $id = dechex(mt_rand(0, 0xFFFFFFFF));
    
    // Insert into Assignment table
    $sql = $db->prepare("INSERT INTO Checkpoint (name, assignment_id, guid) VALUES (?, ?, ?)");
    $sql->bind_param("sss", $name, $assignment, $id);
    $sql->execute();
    $sql->close();

    $db->close();
?>