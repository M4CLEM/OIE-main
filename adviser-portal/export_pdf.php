<?php

require '../vendor/autoload.php';
session_start();
include("../includes/connection.php");

$email = $_SESSION['adviser'];
$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];

$mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);
$mpdf->SetTitle('Student OJT Grade Report');
$mpdf->SetFooter('{PAGENO} of {nbpg}');

// CSS styling
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

// PDF starts with header
$html = $style . '
<div style="text-align:center;">
    <h2 style="margin: 0;">Pamantasan ng Lungsod ng Muntinlupa</h2>
    <p style="margin: 0;">Student OJT Grade Report</p>
    <small><strong>Semester:</strong> ' . htmlspecialchars($activeSemester) . ' | 
           <strong>School Year:</strong> ' . htmlspecialchars($activeSchoolYear) . '</small>
</div>';

if (isset($_SESSION['dept_sec']) && is_array($_SESSION['dept_sec']) && count($_SESSION['dept_sec']) > 0) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['dept_sec']), '?'));
    $query = "SELECT * FROM studentinfo WHERE department= ? AND course= ? AND semester = ? AND school_year = ? AND section IN ($placeholders) ORDER BY course, section, lastName ASC";
    $stmt = $connect->prepare($query);

    $params = array_merge([$_SESSION['dept_adv'], $_SESSION['dept_crs'], $activeSemester, $activeSchoolYear], $_SESSION['dept_sec']);
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        // Group by course-section
        $grouped = [];
        while ($row = $result->fetch_assoc()) {
            $key = $row['course'] . '-' . $row['section'];
            $grouped[$key][] = $row;
        }

        // Process each group
        foreach ($grouped as $courseSection => $students) {
            $html .= '<div class="section-header">' . htmlspecialchars(str_replace('-', ' – ', $courseSection)) . '</div>';
            $html .= '<table>
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
                $fullname = $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'];
                $status = $row['status'];
                $semester = $row['semester'];
                $schoolYear = $row['school_year'];
                $dept = $row['department'];
                $email = $row['email'];

                // Adviser Grade
                $adviserGradeStmt = $connect->prepare("SELECT finalGrade FROM adviser_student_grade WHERE email = ? AND semester = ? AND schoolYear = ?");
                $adviserGradeStmt->bind_param("sss", $email, $activeSemester, $activeSchoolYear);
                $adviserGradeStmt->execute();
                $adviserGradeResult = $adviserGradeStmt->get_result();
                $adviserGrade = $adviserGradeResult->fetch_assoc()['finalGrade'] ?? null;

                // Company Grade
                $companyGradeStmt = $connect->prepare("SELECT finalGrade FROM student_grade WHERE email = ? AND semester = ? AND schoolYear = ?");
                $companyGradeStmt->bind_param("sss", $email, $activeSemester, $activeSchoolYear);
                $companyGradeStmt->execute();
                $companyGradeResult = $companyGradeStmt->get_result();
                $companyGrade = $companyGradeResult->fetch_assoc()['finalGrade'] ?? null;

                // Grading Rubric
                $gradingQuery = $connect->prepare("SELECT adviserWeight, companyWeight FROM grading_rubics WHERE department = ? AND semester = ? AND schoolYear = ?");
                $gradingQuery->bind_param("sss", $dept, $semester, $schoolYear);
                $gradingQuery->execute();
                $gradingResult = $gradingQuery->get_result();
                $gradingInfo = $gradingResult->fetch_assoc();
                $adviserWeight = $gradingInfo['adviserWeight'] ?? 0;
                $companyWeight = $gradingInfo['companyWeight'] ?? 0;

                // Final Grade Calculation
                $finalGrade = (is_numeric($adviserGrade) && is_numeric($companyGrade))
                    ? number_format(($adviserGrade * ($adviserWeight / 100)) + ($companyGrade * ($companyWeight / 100)), 2)
                    : '-';

                $html .= '<tr>
                    <td>' . htmlspecialchars($row['studentID']) . '</td>
                    <td>' . htmlspecialchars($fullname) . '</td>
                    <td>' . htmlspecialchars($dept) . '</td>
                    <td>' . htmlspecialchars($status) . '</td>
                    <td>' . ($adviserGrade !== null ? htmlspecialchars($adviserGrade) : '-') . '</td>
                    <td>' . ($companyGrade !== null ? htmlspecialchars($companyGrade) : '-') . '</td>
                    <td>' . $finalGrade . '</td>
                </tr>';
            }

            $html .= '</tbody></table>';
        }

    } else {
        $html .= '<p style="text-align:center; color:red;">Error executing query: ' . htmlspecialchars($stmt->error) . '</p>';
    }

} else {
    $html .= '<p style="text-align:center;">No sections found for the adviser.</p>';
}

$mpdf->WriteHTML($html);
$mpdf->Output('Student_Grade_Report.pdf', 'D');
?>
