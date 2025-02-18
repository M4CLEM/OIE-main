<?php
session_start();
include_once("../../includes/connection.php");

if (isset($_POST['studentID'])) {
    $studentID = $_POST['studentID'];

    // Query to select only the document-related fields for the given studentID
    $query = "SELECT * FROM documents WHERE student_ID = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "s", $studentID);
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
                    $statusClass = 'bg-warning ';
                    $statusText = 'Pending';
                    break;
                case 'approved':
                    $statusClass = 'bg-success ';
                    $statusText = 'Approved';
                    break;
                case 'rejected':
                    $statusClass = 'bg-danger ';
                    $statusText = 'Rejected';
                    break;
                default:
                    $statusClass = 'bg-secondary ';
                    $statusText = 'Unknown';
                    break;
            }
            $htmlContent .= '<tr>';
            $htmlContent .= "<td class='text-center'><div class='form-check'><input name='documentIDs[]' class='form-check-input p-0 checkbox-highlight' type='checkbox' style='transform: scale(1.5);' value='{$row['id']}' id='flexCheck{$row['id']}' " . ($statusText === 'Approved' ? 'disabled' : '') . "></div></td>";
            $htmlContent .= '<td class="small">' . str_replace('_', '/', $row['document']) . '</td>';
            $htmlContent .= '<td class="small"><a href="#" onclick="viewPDF(\'' . $file . '\')">' . $filename . '</a></td>';
            $htmlContent .= '<td class="small">' . $row['date'] . '</td>';
            $htmlContent .= '<td class="small">';
            // Add conditional class to select element
            $htmlContent .= '<select id="status_' . $row['id'] . '" name="status" class="btn btn-sm dropdown-toggle text-white status-dropdown ' . $statusClass . '" data-id="' . $row['id'] . '" onchange="updateStatus(this); toggleCheckbox(this);"">';
            $options = ['Approved', 'Pending', 'Rejected'];
            foreach ($options as $option) {
                $selected = ($statusText == $option) ? ' selected' : '';
                $htmlContent .= '<option value="' . strtolower($option) . '"' . $selected . '>' . $option . '</option>';
            }
            $htmlContent .= '</select>';
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
