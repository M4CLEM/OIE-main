<?php
session_start();
include_once("../../includes/connection.php");

$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];

if (isset($_POST['studentID'])) {
    $studentID = $_POST['studentID'];

    // Query to select only the document-related fields for the given studentID
    $query = "SELECT * FROM documents WHERE student_ID = ? AND semester = ? AND schoolYear = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "sss", $studentID, $activeSemester, $activeSchoolYear);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if there are documents associated with the studentID
    if (mysqli_num_rows($result) > 0) {
        // Initialize an empty string to store the HTML content
        $htmlContent = '';

        while ($row = mysqli_fetch_assoc($result)) {
            $file = $row['file_name'];
            $filename = basename($file);
            $status = strtolower($row['status']);
            $statusClass = '';
            $statusText = '';
        
            switch ($status) {
                case 'pending':
                    $statusClass = 'text-warning ';
                    $statusText = 'Pending';
                    break;
                case 'approved':
                    $statusClass = 'text-success ';
                    $statusText = 'Approved';
                    break;
                case 'rejected':
                    $statusClass = 'text-danger ';
                    $statusText = 'Rejected';
                    break;
                default:
                    $statusClass = 'text-secondary ';
                    $statusText = 'Unknown';
                    break;
            }
            $htmlContent .= '<tr>';
            $htmlContent .= '<td class="small">' . str_replace('_', '/', $row['document']) . '</td>';
            $htmlContent .= '<td class="small"><a href="#" onclick="viewPDF(\'../' . $file . '\')">' . $filename . '</a></td>';
            $htmlContent .= '<td class="small">' . $row['date'] . '</td>';
            $htmlContent .= '<td class="small text-center">';
            // Add conditional class to select element
            $htmlContent .= '<span id="status_' . $row['id'] . '" name="status" class="font-weight-bold text-uppercase ' . $statusClass . '" data-id="' . $row['id'] . '">' . $status . '</span>';

            $htmlContent .= '</td>';
            $htmlContent .= '</tr>';
        }
        // Echo the constructed HTML content
        echo $htmlContent;
    } else {
        // If no documents found for the studentID, echo a message indicating so
        echo '<tr><td colspan="4" class="text-center small">No documents found for this student.</td></tr>';
    }
}
$connect->close();
