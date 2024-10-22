<?php

// Get form data
$course = $_POST['course'];
$description = $_POST['description'];
$question_types = $_POST['question_type'];
$question_texts = $_POST['question_text'];
$validations = $_POST['validation'];
$options_array = $_POST['options'];

// Insert into Assignment table
$sql = $db->prepare("INSERT INTO Assignment (name, course_id) VALUES (?, ?)");
$sql->bind_param("ss", $description, $course);
$sql->execute();
$assignment_id = $db->insert_id;
$sql->close();

// Insert into Question table
for ($i = 0; $i < count($question_types); $i++) {
    $question_type = $question_types[$i];
    $question_text = $question_texts[$i];
    $validation = $validations[$i];
    $options = $options_array[$i];
    
    // Generate question number (e.g., question 1, 2, 3,...)
    $question_number = $i + 1;

    $sql = $db->prepare("INSERT INTO Question (question_number, assignment_id, response_type, validation, body, standard_priming, feedback) VALUES (?, ?, ?, ?, ?, 1, ?)");
    $feedback = ""; // Feedback can be generated based on requirements or left empty
    $sql->bind_param("iissss", $question_number, $assignment_id, $question_type, $validation, $question_text, $feedback);
    $sql->execute();
    $sql->close();
}

$db->close();

?>