<?php
require_once '\xampp\htdocs\OIE-main\vendor\autoload.php';
$conn = mysqli_connect("localhost", "root", "", "plmunoiedb");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

if (isset($_POST['submit'])) {

    $SY = $_POST['SY'];
    $course = $_POST['course'];
    $semester = $_POST['Semester'];
    $section = $_POST['section'];
    $dept = $_POST['dept'];

    $query = "INSERT INTO sections_list (department, course, section, school_year) 
          VALUES ('$dept', '$course', '$section', '$SY')";

    $result = mysqli_query($conn, $query);

    $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

    if (isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {

        $arr_file = explode('.', $_FILES['file']['name']);
        $extension = end($arr_file);

        if ('csv' == $extension) {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        } else {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }

        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        if (empty($sheetData[0]) || !array_filter($sheetData[0])) {
            echo "No data found in the sheet.";
            exit;
        }

        for ($i = 6; $i < count($sheetData); $i++) {
            if (empty($sheetData[$i]) || !array_filter($sheetData[$i])) {
                continue;
            }

            $studentid = $sheetData[$i][1];
            $lastName = $sheetData[$i][2];
            $firstName = $sheetData[$i][3];
            $excelCourse = $sheetData[$i][4];
            $year = $sheetData[$i][5];

            // Directory where you want to create the folder
            $directory = "documents/$dept/$course/$SY/$semester/$section/";
            $folderPath = $directory . $course . '_' . $studentid;

            // Check if studentID already exists
            $checkQuery = "SELECT studentID FROM student_masterlist WHERE studentID = '$studentid'";
            $checkResult = mysqli_query($conn, $checkQuery);

            if (mysqli_num_rows($checkResult) == 0) {
                // Only insert if studentID does not exist
                $sql = "INSERT INTO student_masterlist(studentID,lastName,firstName,course,year,section,semester) 
                        VALUES('$studentid', '$lastName', '$firstName','$excelCourse', '$year', '$section', '$semester')";

                if (!mysqli_query($conn, $sql)) {
                    echo "Error inserting student: " . mysqli_error($conn);
                }
            } else {
                echo "Student ID $studentid already exists. Skipping database insertion.<br>";
            }

            // Create the folder regardless of whether the student exists in the database
            if (!is_dir($folderPath)) {
                if (mkdir($folderPath, 0777, true)) {
                    echo "Folder created successfully for Student ID: $studentid <br>";
                } else {
                    echo "Failed to create folder for Student ID: $studentid <br>";
                }
            } else {
                echo "Folder already exists for Student ID: $studentid <br>";
            }

            // Display the success message in HTML for each student
?>
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            </head>

            <body>
                <div class="alert alert-success m-4" role="alert">
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

                    <h4 class="alert-heading">Well done!</h4>
                    <p><?php echo $lastName . ' ' . $firstName; ?> uploaded successfully.</p>
                </div>
            </body>

            </html>
<?php
        }
    }
}
?>
