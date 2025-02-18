<?php
include_once("../../includes/connection.php");

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    if (isset($_POST['courseId'])) {
        $courseId = $_POST['courseId'];

        $query = "SELECT * FROM course_list WHERE id = ?";
        $stmt = $connect->prepare($query);
        $stmt->bind_param('i', $courseId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows >  0) {
            $courseData = $result->fetch_assoc();
            echo json_encode($courseData); 
        } else {
            echo json_encode(array('error' => 'No course found with the given ID.'));
        }
    } else {
        echo json_encode(array('error' => 'Course ID is missing.'));
    }
} else {
    echo json_encode(array('error' => 'Invalid request.'));
}
?>