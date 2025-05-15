<?php
require '../vendor/autoload.php';
session_start();
include("../includes/connection.php");

$email = $_SESSION['adviser'];
$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];

$html = '<h2 style="text-align:center;">Student Grade Report</h2>';
$html .= '<p style="text-align:center;"><strong>Semester:</strong> ' . htmlspecialchars($activeSemester) . ' | <strong>School Year:</strong> ' . htmlspecialchars($activeSchoolYear) . '</p>';
$html .= '<table border="1" cellspacing="0" cellpadding="5" width="100%">
    <thead>
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Department</th>
            <th>Course-Section</th>
            <th>Status</th>
            <th>Adviser Grade</th>
            <th>Company Grade</th>
            <th>Final Grade</th>
        </tr>
    </thead>
    <tbody>';

if (isset($_SESSION['dept_sec']) && is_array($_SESSION['dept_sec']) && count($_SESSION['dept_sec']) > 0) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['dept_sec']), '?'));
    $query = "SELECT * FROM studentinfo WHERE department= ? AND course= ? AND semester = ? AND school_year = ? AND section IN ($placeholders) ORDER BY section ASC, lastName ASC";
    $stmt = $connect->prepare($query);

    $params = array_merge([$_SESSION['dept_adv'], $_SESSION['dept_crs'], $activeSemester, $activeSchoolYear], $_SESSION['dept_sec']);
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $fullname = $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'];
            $course_section = $row['course'] . '-' . $row['section'];
            $status = $row['status'];
            $semester = $row['semester'];
            $schoolYear = $row['school_year'];
            

            $adviserGradeStmt = $connect->prepare("SELECT * FROM adviser_student_grade WHERE email = ? AND semester = ? AND schoolYear = ?");
            $adviserGradeStmt->bind_param("sss", $row['email'], $activeSemester, $activeSchoolYear);
            $adviserGradeStmt->execute();
            $adviserGradeResult = $adviserGradeStmt->get_result();
            $adviserGrade = 0;
            if ($rowAdviserGrade = $adviserGradeResult->fetch_assoc()) {
                $adviserGrade = $rowAdviserGrade['finalGrade'];
            }

            $companyGradeStmt = $connect->prepare("SELECT * FROM student_grade WHERE email = ? AND semester = ? AND schoolYear = ?");
            $companyGradeStmt->bind_param("sss", $row['email'], $activeSemester, $activeSchoolYear);
            $companyGradeStmt->execute();
            $companyGradeResult = $companyGradeStmt->get_result();
            $companyGrade = 0;
            if ($rowCompanyGrade = $companyGradeResult->fetch_assoc()) {
                $companyGrade = $rowCompanyGrade['finalGrade'];
            }

            $gradingQuery = $connect->prepare("SELECT * FROM grading_rubics WHERE department = ? AND semester = ? AND schoolYear = ?");
            $gradingQuery->bind_param("sss", $row['department'], $semester, $schoolYear);
            $gradingQuery->execute();
            $gradingResult = $gradingQuery->get_result();
            $gradingInfo = $gradingResult->fetch_assoc();

            $adviserWeight = $gradingInfo['adviserWeight'] ?? 0;
            $companyWeight = $gradingInfo['companyWeight'] ?? 0;
            $finalGrade = ($adviserGrade * ($adviserWeight / 100)) + ($companyGrade * ($companyWeight / 100));

            $html .= '<tr>
                <td>' . htmlspecialchars($row['studentID']) . '</td>
                <td>' . htmlspecialchars($fullname) . '</td>
                <td>' . htmlspecialchars($row['department']) . '</td>
                <td>' . htmlspecialchars($course_section) . '</td>
                <td>' . htmlspecialchars($status) . '</td>
                <td align="center">' . htmlspecialchars($adviserGrade) . '</td>
                <td align="center">' . htmlspecialchars($companyGrade) . '</td>
                <td align="center">' . htmlspecialchars(number_format($finalGrade, 2)) . '</td>
            </tr>';
        }
    } else {
        $html .= '<tr><td colspan="8">Error executing query: ' . htmlspecialchars($stmt->error) . '</td></tr>';
    }
} else {
    $html .= '<tr><td colspan="8">No sections found for the adviser.</td></tr>';
}

$html .= '</tbody></table>';

$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output('Student_Grade_Report.pdf', 'D');
?>