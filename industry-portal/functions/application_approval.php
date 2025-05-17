<?php
    include_once("../../includes/connection.php");
    session_start();

    $activeSemester = $_SESSION['semester'];
    $activeSchoolYear = $_SESSION['schoolYear'];
    $companyName = $_SESSION['companyName'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['studentID'])) {
        $studentID = $_POST['studentID'] ?? null;
        $companyCode = $_POST['companyCode'] ?? null;
        $applicationID = $_POST['applicationID'] ?? null;
        $jobrole = $_POST['jobrole'] ?? null;

        $companyInfoQuery = "SELECT * FROM companylist WHERE No = ?";
        $companyInfoStmt = $connect->prepare($companyInfoQuery);
        $companyInfoStmt->bind_param("s", $companyCode);
        $companyInfoStmt->execute();
        $companyInfoResult = $companyInfoStmt->get_result();

        if($row = $companyInfoResult->fetch_assoc()) {
            $companyAddress = $row['companyaddress'];
            $workType = $row['workType'];
        }

        $stmt = $connect->prepare("
            SELECT 
                s.studentID, s.firstname, s.middlename, s.lastname, s.address, s.age, s.gender,
                s.contactNo, s.course, s.image, s.department, s.objective, s.skills, s.seminars,
                s.email,
                m.section
            FROM studentinfo s
            LEFT JOIN student_masterlist m 
                ON s.studentID = m.studentID AND m.semester = ? AND m.schoolYear = ?
            WHERE s.studentID = ?
        ");

        $stmt->bind_param("sss", $activeSemester, $activeSchoolYear, $studentID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $studentID = $row['studentID'];
            $email = $row['email'] ?? null;
            $firstName = $row['firstname'];
            $middleName = $row['middlename'];
            $lastName = $row['lastname'];
            $address = $row['address'];
            $age = $row['age'];
            $gender = $row['gender'];
            $contactNo = $row['contactNo'];
            $course = $row['course'];
            $image = $row['image'];
            $department = $row['department'];
            $objective = $row['objective'];
            $skills = $row['skills'];
            $seminars = $row['seminars'];
            $section = $row['section'] ?? 'N/A'; // in case there's no matching row
        }

        $stmt->close();

        $approvalQuery = "UPDATE applications SET status = 'Approved' WHERE studentID = ? AND companyName = ? AND semester = ? AND schoolYear = ?";
        $approvalStmt = $connect->prepare($approvalQuery);
        $approvalStmt->bind_param("ssss", $studentID, $companyName, $activeSemester, $activeSchoolYear);
        
        if($approvalStmt->execute()) {
            // Delete other applications of the same student for the same semester and school year, except the approved one
            $deleteQuery = "DELETE FROM applications WHERE studentID = ? AND semester = ? AND schoolYear = ? AND id != ?";
            $deleteStmt = $connect->prepare($deleteQuery);
            $deleteStmt->bind_param("sssi", $studentID, $activeSemester, $activeSchoolYear, $applicationID);
            $deleteStmt->execute();
            $deleteStmt->close();

            $checkQuery = "SELECT * FROM studentinfo WHERE studentID = ? AND semester = ? AND school_year = ?";
            $checkStmt = $connect->prepare($checkQuery);
            $checkStmt->bind_param("sss", $studentID, $activeSemester, $activeSchoolYear);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $recordExist = $checkResult->num_rows > 0;
            $checkStmt->close();
            
            if(!$recordExist) {
                $deploymentStatus = 'Undeployed'; 

                $newRecordQuery = "INSERT INTO studentinfo (studentID, firstname, middlename, lastname, address, age, gender, contactNo, section, course, department, email, status, image, school_year, semester, objective, skills, seminars) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $newRecordStmt = $connect->prepare($newRecordQuery);
                $newRecordStmt->bind_param("sssssisssssssssssss", $studentID, $firstName, $middleName, $lastName, $address, $age, $gender, $contactNo, $section, $course, $department, $email, $deploymentStatus, $image, $activeSchoolYear, $activeSemester, $objective, $skills, $seminars);
                $newRecordStmt->execute();
                $newRecordStmt->close();
            }

            $deploymentStat = 'Pending'; //Status
            $applicationStat = 'Approved'; //Remarks

            $companyInfoRecord = "INSERT INTO company_info (companyName, companyAddress, workType, jobrole, remarks, status, studentID, student_email, department, course, section, semester, schoolYear) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $companyInfoStmt = $connect->prepare($companyInfoRecord);
            $companyInfoStmt->bind_param("ssssssissssss", $companyName, $companyAddress, $workType, $jobrole, $applicationStat, $deploymentStat, $studentID, $email, $department, $course, $section, $activeSemester, $activeSchoolYear);
            $companyInfoStmt->execute();
            $companyInfoStmt->close();

            header("Location: ../applicants.php");
            exit;
        }

        $approvalStmt->close();
    }
?>