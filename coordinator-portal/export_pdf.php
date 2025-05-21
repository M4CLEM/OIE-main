<?php

require '../vendor/autoload.php';
session_start();
include("../includes/connection.php");

$department = $_SESSION['department'];
$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];

$selectedCourse = trim($_POST['selected_course'] ?? 'All');
$selectedSection = trim($_POST['selected_section'] ?? 'All');

$mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);
$mpdf->SetTitle('OJT Grade Report');
$mpdf->SetFooter('{PAGENO} of {nbpg}');

$style = '
<style>
    body { font-family: Arial, sans-serif; font-size: 12px; }
    table { border-collapse: collapse; width: 100%; margin-top: 10px; }
    th, td { border: 1px solid #000; padding: 5px; text-align: center; }
    th { background-color: #f2f2f2; }
    .section-header {
        margin-top: 30px;
        margin-bottom: 10px;
        text-align: center;
        font-size: 14px;
        font-weight: bold;
        border-bottom: 1px solid #000;
        padding-bottom: 5px;
    }
</style>';

$query = "SELECT * FROM studentinfo WHERE department = ? AND semester = ? AND school_year = ?";
$params = [$department, $activeSemester, $activeSchoolYear];
$types = "sss";

if ($selectedCourse !== 'All') {
    $query .= " AND course = ?";
    $params[] = $selectedCourse;
    $types .= "s";
}
if ($selectedSection !== 'All') {
    $query .= " AND section = ?";
    $params[] = $selectedSection;
    $types .= "s";
}

$query .= " ORDER BY course ASC, section ASC, lastName ASC";
$stmt = $connect->prepare($query);
$stmt->bind_param($types, ...$params);

if (!$stmt->execute()) {
    $mpdf->WriteHTML($style . "<p>Error: " . htmlspecialchars($stmt->error) . "</p>");
    $mpdf->Output('Student_Grade_Report.pdf', 'D');
    exit;
}

$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $mpdf->WriteHTML($style . "<p>No student records found for the selected filters.</p>");
    $mpdf->Output('Student_Grade_Report.pdf', 'D');
    exit;
}

// Preload grades
$adviserGrades = [];
$advStmt = $connect->prepare("SELECT email, finalGrade FROM adviser_student_grade WHERE semester = ? AND schoolYear = ?");
$advStmt->bind_param("ss", $activeSemester, $activeSchoolYear);
$advStmt->execute();
$advResult = $advStmt->get_result();
while ($r = $advResult->fetch_assoc()) {
    $adviserGrades[$r['email']] = $r['finalGrade'];
}

$companyGrades = [];
$compStmt = $connect->prepare("SELECT email, finalGrade FROM student_grade WHERE semester = ? AND schoolYear = ?");
$compStmt->bind_param("ss", $activeSemester, $activeSchoolYear);
$compStmt->execute();
$compResult = $compStmt->get_result();
while ($r = $compResult->fetch_assoc()) {
    $companyGrades[$r['email']] = $r['finalGrade'];
}

// Rubric cache
$rubricCache = [];

// Group students by course-section
$grouped = [];
while ($row = $result->fetch_assoc()) {
    $key = $row['course'] . '-' . $row['section'];
    $grouped[$key][] = $row;
}

// Header once at the top
$mainHeader = '
<div style="text-align:center;">
    <h2 style="margin: 0;">Pamantasan ng Lungsod ng Muntinlupa</h2>
    <p style="margin: 0;">Student OJT Grade Report</p>
    <small><strong>Department:</strong> ' . htmlspecialchars($department) . ' | 
           <strong>Semester:</strong> ' . htmlspecialchars($activeSemester) . ' | 
           <strong>School Year:</strong> ' . htmlspecialchars($activeSchoolYear) . '</small>
</div>';

$content = $style . $mainHeader;

// Loop through groups without forced page breaks
foreach ($grouped as $courseSection => $students) {
    $sectionHeader = '<div class="section-header">' . htmlspecialchars(str_replace('-', ' – ', $courseSection)) . '</div>';

    $table = '<table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Status</th>
                <th>Adviser Grade</th>
                <th>Company Grade</th>
                <th>Final Grade</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($students as $row) {
        $email = $row['email'];
        $fullname = $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'];
        $status = $row['status'];
        $semester = $row['semester'];
        $schoolYear = $row['school_year'];
        $dept = $row['department'];
        $rubricKey = "{$dept}-{$semester}-{$schoolYear}";

        $adviserGrade = $adviserGrades[$email] ?? null;
        $companyGrade = $companyGrades[$email] ?? null;

        if (!isset($rubricCache[$rubricKey])) {
            $rstmt = $connect->prepare("SELECT adviserWeight, companyWeight FROM grading_rubics WHERE department = ? AND semester = ? AND schoolYear = ?");
            $rstmt->bind_param("sss", $dept, $semester, $schoolYear);
            $rstmt->execute();
            $rres = $rstmt->get_result();
            $rubricCache[$rubricKey] = $rres->fetch_assoc() ?: ['adviserWeight' => 0, 'companyWeight' => 0];
        }

        $aw = $rubricCache[$rubricKey]['adviserWeight'];
        $cw = $rubricCache[$rubricKey]['companyWeight'];

        $finalGrade = (is_numeric($adviserGrade) && is_numeric($companyGrade))
            ? number_format(($adviserGrade * ($aw / 100)) + ($companyGrade * ($cw / 100)), 2)
            : '-';

        $table .= '<tr>
            <td>' . htmlspecialchars($row['studentID']) . '</td>
            <td>' . htmlspecialchars($fullname) . '</td>
            <td>' . htmlspecialchars($dept) . '</td>
            <td>' . htmlspecialchars($status) . '</td>
            <td>' . ($adviserGrade !== null ? htmlspecialchars($adviserGrade) : '-') . '</td>
            <td>' . ($companyGrade !== null ? htmlspecialchars($companyGrade) : '-') . '</td>
            <td>' . $finalGrade . '</td>
        </tr>';
    }

    $table .= '</tbody></table>';
    $content .= $sectionHeader . $table;
}

// Output once at the end
$mpdf->WriteHTML($content);
$mpdf->Output('Student_Grade_Report.pdf', 'D');
?>
