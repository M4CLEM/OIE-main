<?php
include_once("../../includes/connection.php");
session_start();

$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];
$companyName = $_SESSION['companyName'];

header('Content-Type: application/json');

$studentID = $_GET['studentID'] ?? '';

$response = [];

if (!$studentID) {
    echo json_encode(['error' => 'No studentID provided']);
    exit;
}

// 1. Fetch student main info
$studentQuery = "
    SELECT 
        sm.*, 
        ci.*, 
        si.*
    FROM studentinfo si
    LEFT JOIN student_masterlist sm ON si.studentID = sm.studentID
    LEFT JOIN company_info ci ON si.studentID = ci.studentID
    WHERE si.studentID = ?
        AND si.semester = ?
        AND si.school_year = ?
        AND ci.semester = ?
        AND ci.schoolYear = ?
        AND sm.semester = ?
        AND sm.schoolYear = ?
";

$stmt = $connect->prepare($studentQuery);
$stmt->bind_param("sssssss", $studentID, $activeSemester, $activeSchoolYear, $activeSemester, $activeSchoolYear, $activeSemester, $activeSchoolYear);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$response['student'] = $row ?? [];

if ($row) {
    $response['studentName'] = $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'];
    $response['courseSection'] = $row['course'] . '-' . $row['section'];
    $response['department'] = $row['department'];
    $response['requiredHours'] = $row['hoursRequirement'];
    $response['dateStarted'] = $row['dateStarted'];
    $response['dateEnded'] = $row['dateEnded'];
    $response['trainerEmail'] = $row['trainerEmail'];
    $response['trainerContact'] = $row['trainerContact'];
    $response['workType'] = $row['workType'];
    $response['studentNumber'] = $row['studentID'];
}

// 2. Fetch log data (DTR)
$logQuery = "SELECT * FROM logdata WHERE student_num = ? AND semester = ? AND schoolYear = ? ORDER BY time_in DESC";
$stmt = $connect->prepare($logQuery);
$stmt->bind_param("sss", $studentID, $activeSemester, $activeSchoolYear);
$stmt->execute();
$logResult = $stmt->get_result();

$logs = [];
$totalSeconds = 0;

while ($log = $logResult->fetch_assoc()) {
    // Append to logs
    $logs[] = $log;

    // Only compute total time for "Approved" logs
    if (
        $log['is_approved'] === 'Approved' &&
        !empty($log['time_in']) &&
        !empty($log['time_out'])
    ) {
        $start = new DateTime($log['time_in']);
        $end = new DateTime($log['time_out']);
        $diff = $start->diff($end);
        $seconds = ($diff->d * 24 * 60 * 60) + ($diff->h * 3600) + ($diff->i * 60) + $diff->s;

        // Deduct break minutes if available
        $breakMinutes = isset($log['break_minutes']) ? (int)$log['break_minutes'] : 0;
        $seconds -= ($breakMinutes * 60);

        // Avoid negative time if break_minutes exceeds duration
        if ($seconds < 0) {
            $seconds = 0;
        }

        $totalSeconds += $seconds;
    }
}


$response['logs'] = $logs;

$totalHours = floor($totalSeconds / 3600);
$remainingMinutes = floor(($totalSeconds % 3600) / 60);
$response['totalRendered'] = "{$totalHours} hrs {$remainingMinutes} mins";

// 3. Fetch documents
$docQuery = "SELECT * FROM documents WHERE student_ID = ? AND semester = ? AND schoolYear = ?";
$stmt = $connect->prepare($docQuery);
$stmt->bind_param("sss", $studentID, $activeSemester, $activeSchoolYear);
$stmt->execute();
$documentResult = $stmt->get_result();

$documents = [];
while ($doc = $documentResult->fetch_assoc()) {
    $documents[] = $doc;
}
$response['documents'] = $documents;

echo json_encode($response);
