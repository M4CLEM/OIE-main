<?php 

include_once("../includes/connection.php");
$connect = new mysqli('localhost', 'root', '', 'plmunoiedb');
session_start();

class viewStudentLogs{

    public function loadStudentInfo($connect, $studentNum){

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
                $student_Company = $row['company'];

                        
                $student_fullName = $student_FirstName . " " . $student_MiddleName . " " . $student_LastName;

                $str .= "
                        <h3 class='card-text'><a href='#' onclick='history.back();' class='btn btn-primary'><i class='fa fa-arrow-circle-left' aria-hidden='true'></i></a>
                        $student_fullName</h3> <br>
                        <p class='card-text'><b>Student Number:</b> $student_Number </p>
                        <p class='card-text'><b>Department:</b> $student_Dept </p>
                        <p class='card-text'><b>Section:</b> $student_Course - $student_Section</p><br>
                        <p class='card-text'><b>Company:</b> $student_Company</p>
                        ";
            }
            echo $str;
        }
    }

    function loadStudentLogs($connect, $studentNumber, $dateFrom = null, $dateTo = null) {
    
        $queryParams = [$studentNumber];
        $query = "SELECT * FROM logdata WHERE student_num = ?";
        $data = [];
        
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
            
            //$is_approved = $row["is_approved"];
            //if ($is_approved) {}

            $time_in_12hour = date("g:i a", strtotime($row['time_in']));
            $time_out_12hour = "";
            $total = '';
            $date = $row['date'];

            if (!empty($row['time_out'])) {
                $time_out_12hour = date("g:i a", strtotime($row['time_out']));
                $seconds = strtotime($row['time_out']) - strtotime($row['time_in']);
                $hours = floor($seconds / 3600);
                $seconds %= 3600;
                $minutes = floor($seconds / 60);
                $total = $hours . "hrs " . $minutes . "mins";
            }

            $data[] = [
                'student_number' => $studentNumber,
                'date' => $date,
                'time_in' => $time_in_12hour,
                'time_out' => $time_out_12hour,
                'total_hours' => $total,
            ];

            echo "<tr>
                    <td>{$date}</td>
                    <td>{$time_in_12hour}</td>
                    <td>{$time_out_12hour}</td>
                    <td>{$total}</td>
                </tr>";

        }

        return $data;
        $stmt->close();
    }    
}

?>