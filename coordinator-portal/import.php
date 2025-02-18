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

        for ($i =  6; $i < count($sheetData); $i++) {
            //SKIP IF EMPTY
            if (empty($sheetData[$i]) || !array_filter($sheetData[$i])) {
                continue;
            } {
                $studentid = $sheetData[$i][1];
                $lastName = $sheetData[$i][2];
                $firstName = $sheetData[$i][3];
                $excelCourse = $sheetData[$i][4];
                $year = $sheetData[$i][5];


                $sql = "INSERT INTO student_masterlist(studentID,lastName,firstName,course,year,section) VALUES('$studentid', '$lastName', '$firstName','$excelCourse', '$year', '$section')";

                // Directory where you want to create the folder
                $directory = "documents/$dept/$course/$SY/$semester/$section/";

                // Combine the directory path and folder name
                $folderPath = $directory . $course . '_' . $studentid;

                // Check if the folder doesn't already exist
                if (!is_dir($folderPath)) {
                    // Create the folder
                    if (mkdir($folderPath, 0777, true)) {
                        echo "Folder created successfully.";
                    } else {
                        echo "Failed to create the folder.";
                    }
                } else {
                    echo "Folder already exists.";
                }




                if (mysqli_query($conn, $sql)) {

?>

                    <!DOCTYPE html>
                    <html lang="en">

                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

                    </head>

                    <body>
                        <div class="alert alert-success m-4" role="alert">
                            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

                            <h4 class="alert-heading">Well done!</h4>

                            <?php echo $lastName . ' ' . $firstName ?>
                            <p> upload successfully
                            <p>


                        </div>

                    </body>

                    </html>

<?php
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
            }
        }
    }
}
?>