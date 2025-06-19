<?php
include_once("../includes/connection.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$semester = $_SESSION['semester'];
$schoolYear = $_SESSION['schoolYear'];

date_default_timezone_set('Asia/Manila'); // Set correct timezone
class updatelogs
{
    public function loadInfo($connect, $dept, $course, $studentNum, $section, $semester, $schoolYear)
    {
        // Fetch student info using prepared statement
        $stmt = $connect->prepare("SELECT * FROM studentinfo WHERE studentID = ? AND semester = ? AND school_year = ?");
        $stmt->bind_param("sss", $studentNum, $semester, $schoolYear);
        $stmt->execute();
        $student_details_query = $stmt->get_result();

        $str = "";

        if ($student_details_query->num_rows > 0) {
            while ($row = $student_details_query->fetch_assoc()) {
                $student_FirstName = htmlspecialchars($row['firstname']);
                $student_MiddleName = htmlspecialchars($row['middlename']);
                $student_LastName = htmlspecialchars($row['lastname']);
                $student_Number = htmlspecialchars($row['studentID']);
                $student_Dept = htmlspecialchars($row['department']);
                $student_Course = htmlspecialchars($row['course']);
                $student_Section = htmlspecialchars($row['section']);
                $student_Company = htmlspecialchars($row['companyCode']);

                // Adviser query
                $adviserStmt = $connect->prepare("SELECT fullName FROM listadviser WHERE dept = ? AND course = ? AND section = ? AND semester = ? AND schoolYear = ?");
                $adviserStmt->bind_param("sssss", $dept, $course, $section, $semester, $schoolYear);
                $adviserStmt->execute();
                $adviserResult = $adviserStmt->get_result();

                // Company query
                $companyStmt = $connect->prepare("SELECT * FROM company_info WHERE companyCode = ?");
                $companyStmt->bind_param("s", $student_Company);
                $companyStmt->execute();
                $companyResult = $companyStmt->get_result();
                $rowCompany = $companyResult->fetch_assoc();

                $companyName = $rowCompany['companyName'] ?? 'N/A';
                $companyAdd = $rowCompany['companyAddress'] ?? 'N/A';
                $companyNum = $rowCompany['trainerContact'] ?? 'N/A';
                $companyEmail = $rowCompany['trainerEmail'] ?? 'N/A';
                $workType = $rowCompany['workType'] ?? 'N/A';

                // Format adviser names if found
                $adviserNamesStr = "N/A";
                if ($adviserResult->num_rows > 0) {
                    $adviserNames = [];
                    while ($aRow = $adviserResult->fetch_assoc()) {
                        $adviserNames[] = htmlspecialchars($aRow['fullName']);
                    }
                    $adviserNamesStr = implode(", ", $adviserNames);
                }

                // HTML Output
                $str .= "
                <p class='card-text'><b>Name:</b> {$student_FirstName} {$student_MiddleName} {$student_LastName} | {$student_Number}</p>
                <p class='card-text'><b>Department:</b> {$student_Dept}</p>
                <p class='card-text'><b>Section:</b> {$student_Course} - {$student_Section}</p>";

                if ($adviserNamesStr !== "N/A") {
                    $str .= "<p class='card-text'><b>Adviser:</b> {$adviserNamesStr}</p>";
                }

                $str .= "
                <br>
                <div class='container'>
                    <div class='row'>
                        <div class='col-md-7'>
                            <p class='card-text'><b>Company Name:</b> <br>{$companyName}</p><br>
                            <p class='card-text'><b>Address:</b> <br>{$companyAdd}</p><br>
                            <p class='card-text'><b>Contact Number:</b> <br>{$companyNum}</p><br>
                        </div>
                        <div class='col-md-4'>
                            <p class='card-text'><b>Trainer Email:</b> <br>{$companyEmail}</p><br>
                            <p class='card-text'><b>Work Type:</b> <br>{$workType}</p>
                        </div>
                    </div>
                </div><br>";
            }
        } else {
            $str .= "No student data found.";
        }

        echo $str;
    }

    public function loadLogs($connect, $studentNumber, $dateFrom = null, $dateTo = null, $semester, $schoolYear)
    {
        $queryParams = [$studentNumber, $semester, $schoolYear];
        $query = "SELECT * FROM logdata WHERE student_num = ? AND semester = ? AND schoolYear = ?";

        if ($dateFrom && $dateTo) {
            $query .= " AND date BETWEEN ? AND ?";
            $queryParams[] = $dateFrom;
            $queryParams[] = $dateTo;
        }

        $stmt = $connect->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $connect->error);
        }

        $types = str_repeat('s', count($queryParams));
        $stmt->bind_param($types, ...$queryParams);

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $time_in_12hour = date("g:i a", strtotime($row['time_in']));
            $time_out_12hour = "";
            $total = "";

            if (!empty($row['time_out'])) {
                $time_out_12hour = date("g:i a", strtotime($row['time_out']));
                $seconds = strtotime($row['time_out']) - strtotime($row['time_in']);
                $breakSeconds = ($row['break_minutes'] ?? 60) * 60;

                if ($seconds >= 14400) {
                    $seconds -= $breakSeconds;
                }

                $hours = floor($seconds / 3600);
                $minutes = floor(($seconds % 3600) / 60);

                $total = ($seconds < 60) ? "Less than a minute" : "{$hours}hrs {$minutes}mins";
            }

            $breakMins = htmlspecialchars($row['break_minutes'] ?? 60);
            $status = htmlspecialchars($row['is_approved']);

            echo "<tr>
                <td>" . htmlspecialchars($row['date']) . "</td>
                <td>{$time_in_12hour}</td>
                <td>{$time_out_12hour}</td>
                <td>{$breakMins} mins</td>
                <td>{$total}</td>
                <td>{$status}</td>
            </tr>";
        }

        $stmt->close();
    }
}

if (isset($_POST['logState'], $_POST['studentNum'], $_POST['log_course'], $_POST['log_section'], $_POST['log_company'])) {

    include_once("../../includes/connection.php");

    $logState = $_POST['logState'];
    $studentNum = $_POST['studentNum'];
    $logDept = $_POST['log_dept'];
    $logCourse = $_POST['log_course'];
    $logSection = $_POST['log_section'];
    $logCompany = $_POST['log_company'];

    if ($logState === 'In') {
        $status = "Out";
        $approval = "Pending";
        $breakMinutes = $_SESSION['student_breaks'][$studentNum] ?? 60;

        $sql = "INSERT INTO logdata (
                date, time_in, status, student_num, log_dept, log_course, log_section,
                log_company, semester, schoolYear, break_minutes, is_approved
            ) VALUES (
                CURDATE(), CURRENT_TIMESTAMP, '$status', '$studentNum', '$logDept', '$logCourse',
                '$logSection', '$logCompany', '$semester', '$schoolYear', '$breakMinutes', '$approval'
            )";

        if (mysqli_query($connect, $sql)) {
            echo "Logged In Successfully!";
        } else {
            echo "ERROR: Could not execute $sql. " . mysqli_error($connect);
        }
    } elseif ($logState === 'Out') {
        $status = "In";

        // Step 1: Fetch the most recent open log (time_out IS NULL)
        $select = "SELECT id, time_in FROM logdata 
               WHERE student_num = '$studentNum' AND time_out IS NULL 
               ORDER BY time_in DESC LIMIT 1";

        $res = mysqli_query($connect, $select);
        if ($row = mysqli_fetch_assoc($res)) {
            $logId = $row['id'];
            $timeIn = strtotime($row['time_in']);
            $now = time();

            // Calculate duration between time_in and now
            $duration = $now - $timeIn;

            // Maximum session length (10 hours)
            $maxDuration = 10 * 3600; // 36000 seconds

            // If the session is too long, cap it
            if ($duration > $maxDuration) {
                $timeOutFinal = $timeIn + $maxDuration;
            } else {
                $timeOutFinal = $now;
            }

            // Format timestamp for SQL update
            $timeOutFormatted = date("Y-m-d H:i:s", $timeOutFinal);

            $update = "UPDATE logdata 
                   SET time_out = '$timeOutFormatted', status = '$status' 
                   WHERE id = $logId";

            if (mysqli_query($connect, $update)) {
                echo "Logged Out.";
            } else {
                echo "ERROR: Could not execute update. " . mysqli_error($connect);
            }
        } else {
            echo "No open log entry found for Time Out.";
        }
    }
}
?>