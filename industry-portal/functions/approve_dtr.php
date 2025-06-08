<?php
    include_once("../../includes/connection.php");
    session_start();
    $activeSemester = $_SESSION['semester'];
    $activeSchoolYear = $_SESSION['schoolYear'];
    header('Content-Type: application/json');

    $response = ['success' => false, 'message' => 'Something went wrong.'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $logID = $_POST['logID'] ?? '';

        if (empty($logID)) {
            $response['message'] = "Missing log ID.";
        } else {
            $stmt = $connect->prepare("UPDATE logdata SET is_approved = 'Approved' WHERE id = ? AND semester = ? AND schoolYear = ?");
            $stmt->bind_param("iss", $logID, $activeSemester, $activeSchoolYear);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = "Log approved successfully.";

                // Get student number from the approved log
                $studNumQuery = "SELECT student_num FROM logdata WHERE id = ? AND semester = ? AND schoolYear = ?";
                $studNumStmt = $connect->prepare($studNumQuery);
                $studNumStmt->bind_param("iss", $logID, $activeSemester, $activeSchoolYear);
                $studentNum = null;

                if ($studNumStmt->execute()) {
                    $studNumStmt->bind_result($studentNum);
                    $studNumStmt->fetch();
                    $studNumStmt->close();

                    if (!empty($studentNum)) {
                        // Get required hours from student_masterlist
                        $reqQuery = "SELECT hoursRequirement FROM student_masterlist WHERE studentID = ?";
                        $reqStmt = $connect->prepare($reqQuery);
                        $reqStmt->bind_param("s", $studentNum);
                        $requiredHours = 0;

                        if ($reqStmt->execute()) {
                            $reqStmt->bind_result($requiredHours);
                            $reqStmt->fetch();
                            $reqStmt->close();

                            if ($requiredHours > 0) {
                                // Calculate total rendered hours from time_in and time_out, minus break_minutes, avoiding negatives
                                $hoursQuery = "
                                    SELECT COALESCE(SUM(
                                        GREATEST(TIMESTAMPDIFF(MINUTE, time_in, time_out) - break_minutes, 0)
                                    ), 0) / 60 AS total_hours
                                    FROM logdata
                                    WHERE student_num = ?
                                        AND is_approved = 'Approved'
                                        AND semester = ? AND schoolYear = ?
                                    ";
                                $hoursStmt = $connect->prepare($hoursQuery);
                                $hoursStmt->bind_param("sss", $studentNum, $activeSemester, $activeSchoolYear);

                                if ($hoursStmt->execute()) {
                                    $hoursStmt->bind_result($totalRendered);
                                    if ($hoursStmt->fetch()) {
                                        $totalRendered = round($totalRendered, 2);
                                        if ($totalRendered >= $requiredHours) {
                                            $response['message'] .= " Student has completed all required hours.";
                                            // Optional: mark status as 'Completed' here
                                            $updateDeploymentStatus = $connect->prepare("UPDATE studentinfo SET status = 'Completed' WHERE studentID = ? AND semester = ? AND school_year = ?");
                                            $updateDeploymentStatus->bind_param("sss", $studentNum, $activeSemester, $activeSchoolYear);
                                            $updateDeploymentStatus->execute();

                                            $updateEndDate = $connect->prepare("UPDATE company_info SET dateEnded = CURDATE() WHERE studentID = ? AND semester = ? AND schoolYear = ?");
                                            $updateEndDate->bind_param("sss", $studentNum, $activeSemester, $activeSchoolYear);
                                            $updateEndDate->execute();
                                        } else {
                                            $remaining = round($requiredHours - $totalRendered, 2);
                                            $response['message'] .= " Student still needs $remaining more hour(s).";
                                        }
                                    }
                                    $hoursStmt->close();
                                }
                            } else {
                                $response['message'] .= " Required hours not set in student_masterlist.";
                            }
                        }
                    }
                }
            } else {
                $response['message'] = "Failed to update log.";
            }
        }
    } else {
        $response['message'] = "Invalid request method.";
    }

    echo json_encode($response);
