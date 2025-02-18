<?php


class updatelogs
{

    public function loadInfo($connect, $dept, $course, $studentNum, $section)
    {

        $student_details_query = mysqli_query($connect, "SELECT * FROM studentinfo WHERE studentID='$studentNum'");
        $str = " ";

        if (mysqli_num_rows($student_details_query) > 0) {
            while ($row = mysqli_fetch_array($student_details_query)) {

                $student_FirstName = $row['firstname'];
                $student_MiddleName = $row['middlename'];
                $student_LastName = $row['lastname'];
                $student_Number = $row['studentID'];
                $student_Dept = $row['department'];
                $student_Course = $row['course'];
                $student_Section = $row['section'];
                $student_Company = $row['companyCode'];

                $adviser_details_query = mysqli_query($connect, "SELECT * FROM listadviser WHERE dept='$dept' AND course='$course' AND section='$section'");

                $company_details_query = mysqli_query($connect, "SELECT * FROM company_info WHERE companyCode='$student_Company'");
                $rowCompany = mysqli_fetch_array($company_details_query);
                $companyName = $rowCompany['companyName'];
                $companyAdd = $rowCompany['companyAddress'];
                $companyNum = $rowCompany['trainerContact'];
                $companyEmail = $rowCompany['trainerEmail'];
                $workType = $rowCompany['workType'];

                if (mysqli_num_rows($adviser_details_query) > 0) {
                    $adviserNames = [];
                    while ($row = mysqli_fetch_array($adviser_details_query)) {
                        $adviserNames[] = $row['fullName'];
                    }
                    $adviserNamesStr = implode(", ", $adviserNames);
                    $str .= "
                        <p class='card-text'><b>Name: </b> $student_FirstName $student_MiddleName $student_LastName | $student_Number </p>
                        <p class='card-text'><b>Department:</b> $student_Dept </p>
                        <p class='card-text'><b>Section:</b> $student_Course - $student_Section</p>
                        <p class='card-text'><b>Adviser:</b> $adviserNamesStr</p><br>


                        <div class='container'>
                            <div class='row'>
                                <div class='col-md-7'>
                                    <p class='card-text'><b>Company Name:</b> <br>$companyName</p><br>
                                    <p class='card-text'><b>Address:</b> <br>$companyAdd</p><br>
                                    <p class='card-text'><b>Contact Number:</b> <br>$companyNum</p><br>
                                </div>'
                                <div class='col-md-4'>
                                    <p class='card-text'><b>Trainer Email:</b> <br>$companyEmail</p><br>
                                    <p class='card-text'><b>Work Type:</b> <br>$workType</p>
                                </div>
                            </div>
                        </div>
                        
                        ";
                } else {

                    $str .= "
                    <p class='card-text'><b>Name: </b> $student_FirstName $student_MiddleName $student_LastName | $student_Number </p>
                    <p class='card-text'><b>Department:</b> $student_Dept </p>
                    <p class='card-text'><b>Section:</b> $student_Course - $student_Section</p><br>

                    <div class='container'>
                        <div class='row'>
                            <div class='col-md-7'>
                                <p class='card-text'><b>Company Name:</b> <br>$companyName</p><br>
                                <p class='card-text'><b>Address:</b> <br>$companyAdd</p><br>
                                <p class='card-text'><b>Contact Number:</b> <br>$companyNum</p><br>
                            </div>'
                            <div class='col-md-4'>
                                <p class='card-text'><b>Trainer Email:</b> <br>$companyEmail</p><br>
                                <p class='card-text'><b>Work Type:</b> <br>$workType</p>
                            </div>
                        </div>
                    </div>
                

                    ";
                }
                echo $str;
            }
        } else {
            echo "ERROR";
        }
    }

    function loadLogs($connect, $studentNumber, $dateFrom = null, $dateTo = null)
    {

        $queryParams = [$studentNumber];
        $query = "SELECT * FROM logdata WHERE student_num = ?";

        if ($dateFrom && $dateTo) {
            $query .= " AND date BETWEEN ? AND ?";
            $queryParams[] = $dateFrom;
            $queryParams[] = $dateTo;
        }

        $stmt = $connect->prepare($query);
        if ($stmt === false) {
            throw new Exception("Prepare failed: " . $connect->error);
        }

        if (!$stmt->bind_param(str_repeat('s', count($queryParams)), ...$queryParams)) {
            throw new Exception("Binding parameters failed: " . $stmt->error);
        }

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {

            $time_in_12hour = date("g:i a", strtotime($row['time_in']));
            $time_out_12hour = "";
            $total = '';

            if (!empty($row['time_out'])) {
                $time_out_12hour = date("g:i a", strtotime($row['time_out']));
                $seconds = strtotime($row['time_out']) - strtotime($row['time_in']);
                $hours = floor($seconds / 3600);
                $seconds %= 3600;
                $minutes = floor($seconds / 60);
                $total = $hours . "hrs " . $minutes . "mins";
            }

            echo "<tr>
                    <td>{$row['date']}</td>
                    <td>{$time_in_12hour}</td>
                    <td>{$time_out_12hour}</td>
                    <td>{$total}</td>
                </tr>";
        }
        $stmt->close();
    }
}



if (isset($_POST['logState'], $_POST['studentNum'], $_POST['log_course'], $_POST['log_section'], $_POST['log_company'])) {

    include_once("../../includes/connection.php");

    $date = date("Y-m-d");
    $time = date("H:i:s");

    $logState = $_POST['logState'];
    $studentNum = $_POST['studentNum'];
    $logDept = $_POST['log_dept'];
    $logCourse = $_POST['log_course'];
    $logSection = $_POST['log_section'];
    $logCompany = $_POST['log_company'];

    if ($logState == 'In') {

        $status = "Out";
        $sql = "INSERT INTO logdata (date, time_in, status, student_num, log_dept, log_course, log_section, log_company) 
                VALUES ('$date', '$time', '$status', '$studentNum', '$logDept', '$logCourse', '$logSection', '$logCompany')";

        if (mysqli_query($connect, $sql)) {
            echo "<script>alert('Logged In Successfully!');</script>";
        } else {
            echo "ERROR: Could not able to execute $sql. " . mysqli_error($connect);
        }
    } else if ($logState == 'Out') {
        $status = "In";
        $sql = "UPDATE logdata SET time_out='$time', status='$status' WHERE student_num='$studentNum' AND time_in IS NOT NULL AND time_out IS NULL";

        if (mysqli_query($connect, $sql)) {
            echo "<script>alert('Logged Out.');</script>";
        } else {
            echo "ERROR: Could not able to execute $sql. " . mysqli_error($connect);
        }
    }
}
