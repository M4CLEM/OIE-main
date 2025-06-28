<?php
    session_start();
    include_once("../../includes/connection.php");

    $activeSemester = $_SESSION['semester'];
    $activeSchoolYear = $_SESSION['schoolYear'];

    if (isset($_GET['department'], $_GET['course'], $_GET['section'])) {
        $department = $_GET['department'];
        $course = $_GET['course'];
        $section = $_GET['section'];

        $stmt = $connect->prepare("SELECT * FROM studentinfo WHERE department = ? AND course = ? AND section = ? AND semester = ? AND school_year = ?");
        $stmt->bind_param("sssss", $department, $course, $section, $activeSemester, $activeSchoolYear);
        $stmt->execute();
    
        $students = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach ($students as $student) {
            //Fetch Deployment Information
            $deploymentInfoStmt = $connect->prepare("SELECT * FROM company_info WHERE studentID = ? AND semester = ? AND schoolYear = ?");
            $deploymentInfoStmt->bind_param("sss", $student['studentID'], $activeSemester, $activeSchoolYear);
            $deploymentInfoStmt->execute();
            $deploymentInfo = $deploymentInfoStmt->get_result()->fetch_assoc();

            // Fetch ALL RECORDS for Student Attendance (DTR) Logs
            $dtrLogStmt = $connect->prepare("SELECT time_in, time_out, break_minutes FROM logdata WHERE student_num = ? AND semester = ? AND schoolYear = ? AND is_approved = 'Approved'");
            $dtrLogStmt->bind_param("sss", $student['studentID'], $activeSemester, $activeSchoolYear);
            $dtrLogStmt->execute();
            $dtrResult = $dtrLogStmt->get_result();

            $totalMinutes = 0;

            while ($log = $dtrResult->fetch_assoc()) {
                $timeIn = strtotime($log['time_in']);
                $timeOut = strtotime($log['time_out']);
                $breakMinutes = (int) $log['break_minutes'];

                if ($timeIn && $timeOut && $timeOut > $timeIn) {
                    $durationMinutes = ($timeOut - $timeIn) / 60; // convert seconds to minutes
                    $netMinutes = max(0, $durationMinutes - $breakMinutes); // subtract break
                    $totalMinutes += $netMinutes;
                }
            }

            // Convert total minutes to HH:MM format
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
            $totalRendered = sprintf("%02d:%02d", $hours, $minutes);
            
            //Fetch Require Hours for the student
            $hoursRequirementStmt = $connect->prepare("SELECT hoursRequirement FROM student_masterlist WHERE studentID = ? AND semester = ? AND schoolYear = ?");
            $hoursRequirementStmt->bind_param("sss", $student['studentID'], $activeSemester, $activeSchoolYear);
            $hoursRequirementStmt->execute();
            $hoursResult = $hoursRequirementStmt->get_result();

            if ($row = $hoursResult->fetch_assoc()) {
                $hourRequirement = $row['hoursRequirement'];
            } else {
                $hourRequirement = 0; // Default or fallback value if no record is found
            }

            // Fetch Grading Rubrics
            $gradingRubicStmt = $connect->prepare("SELECT adviserWeight, companyWeight FROM grading_rubics WHERE department = ? AND semester = ? AND schoolYear = ?");
            $gradingRubicStmt->bind_param("sss", $department, $activeSemester, $activeSchoolYear);
            $gradingRubicStmt->execute();
            $rubicResult = $gradingRubicStmt->get_result();
            $rubicRow = $rubicResult->fetch_assoc();

            $adviserWeight = $rubicRow['adviserWeight'] ?? 50;
            $companyWeight = $rubicRow['companyWeight'] ?? 50;

            // Fetch Adviser Grade
            $adviserGradeStmt = $connect->prepare("SELECT finalGrade FROM adviser_student_grade WHERE email = ? AND semester = ? AND schoolYear = ?");
            $adviserGradeStmt->bind_param("sss", $student['email'], $activeSemester, $activeSchoolYear);
            $adviserGradeStmt->execute();
            $adviserResult = $adviserGradeStmt->get_result();
            $adviserRow = $adviserResult->fetch_assoc();
            $finalGradeAdviser = $adviserRow['finalGrade'] ?? 0;

            // Fetch Company Grade
            $companyGradeStmt = $connect->prepare("SELECT finalGrade FROM student_grade WHERE email = ? AND semester = ? AND schoolYear = ?");
            $companyGradeStmt->bind_param("sss", $student['email'], $activeSemester, $activeSchoolYear);
            $companyGradeStmt->execute();
            $companyResult = $companyGradeStmt->get_result();
            $companyRow = $companyResult->fetch_assoc();
            $finalGradeCompany = $companyRow['finalGrade'] ?? 0;

            // Compute final grade using weights
            $finalizedGrade = ($finalGradeAdviser * ($adviserWeight / 100)) + ($finalGradeCompany * ($companyWeight / 100));
            $finalizedGrade = round($finalizedGrade, 2);

            echo "<tr>
                <td>
                    <p>{$student['studentID']}</p>
                </td>

                <td>
                    <p>{$student['firstname']} {$student['middlename']} {$student['lastname']}</p>
                </td>

                <td>
                    <P>{$deploymentInfo['companyName']}</p>
                </td>

                <td>
                    <P>{$deploymentInfo['jobrole']}</p>
                </td>

                <td>
                    <P>{$totalRendered} Hours/{$hourRequirement} Hours</p>
                </td>
                
                <td>
                    <p>{$finalizedGrade}</p>
                </td>
            </tr>";
        }
    } else {
        echo "Required parameters are missing.";
    }
?>
