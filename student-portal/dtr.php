<?php
session_start();
include_once("../includes/connection.php");

$semester = $_SESSION['semester'];
$schoolYear = $_SESSION['schoolYear'];

include("includes/logs.php");
date_default_timezone_set('Asia/Manila'); // Set correct timezone

$post = new updatelogs();

$uname = $_SESSION['student'];

$sql = "SELECT * FROM studentinfo WHERE email = '$uname' AND semester = '$semester' AND school_year = '$schoolYear'";
$result = mysqli_query($connect, $sql);

if (mysqli_num_rows($result) == 1) {

    $row = mysqli_fetch_assoc($result);
    $_SESSION['stud_code'] = $row['studentID'];
    $_SESSION['stud_first'] = $row['firstname'];
    $_SESSION['stud_mid'] = $row['middlename'];
    $_SESSION['stud_last'] = $row['lastname'];
    $_SESSION['stud_dept'] = $row['department'];
    $_SESSION['stud_course'] = $row['course'];
    $_SESSION['stud_section'] = $row['section'];
    $_SESSION['stud_company'] = $row['companyCode'];
    $_SESSION['stud_SY'] = $row['school_year'];
    $_SESSION['stud_image'] = $row['image'];
}

$timeNow = date('Y-m-d H:i:s'); // This will now match Asia/Manila timezone

$studentNumber = $_SESSION['stud_code'];
$firstName = $_SESSION['stud_first'];
$middleName = $_SESSION['stud_mid'];
$surName = $_SESSION['stud_last'];
$dept = $_SESSION['stud_dept'];
$course = $_SESSION['stud_course'];
$section = $_SESSION['stud_section'];
$companyCode = $_SESSION['stud_company'];
$schoolYear = $_SESSION['stud_SY'];
$image = $_SESSION['stud_image'];
$currentTime = date("h:i:sa");
$logState = "";



$data_query =  mysqli_query($connect, "SELECT status FROM logdata WHERE student_num='$studentNumber' 
                                    ORDER BY date DESC, time_in DESC LIMIT 1");

if (!$data_query) {
    die('SQL Error: ' . mysqli_error($connect));
}

$row = mysqli_fetch_array($data_query);

if ($row !== null) {
    $logState = $row['status'];
} else {
    $logState = "In";
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <?php include("../elements/meta.php"); ?>
    <title>Student Portal</title>
    <?php include("embed.php"); ?>
    <link rel="stylesheet" href="../assets/css/new-style.css">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!--Sidebar Wrapper-->
        <aside id="sidebar" class="expand">
            <?php include('../elements/stud_sidebar.php') ?>
        </aside>

        <div class="main">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                <!-- Title -->
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Attendance</h4>
                <!-- Topbar Navbar -->
                <?php include('../elements/stud_navbar_user_info.php'); ?>
            </nav>
            <!-- End of Topbar -->

            <!-- Main Content -->
            <div id="content" class="py-2">

                <div class="col-lg-12 mb-4">
                    <div class="container mt-6">
                        <div class="row">


                            <div class='card-body col-md-9 border mt-2 rounded p-4'>
                                <h2 class='card-title'>On the Job Training: <?php echo $schoolYear ?></h5><br>
                                    <?php $post->loadInfo($connect, $dept, $course, $studentNumber, $section, $semester, $schoolYear); ?>
                            </div>

                            <div class="col-md-3 border mt-2 p-5 text-center rounded">
                                <br>
                                <button id="toggleButton" class="btn btn-lg btn-block 
        <?php echo $logState === 'In' ? 'btn-success' : 'btn-danger'; ?>"
                                    onclick="executePHPFunction()">
                                    <br><?php echo $logState ?><br><br>
                                </button>

                                <p id="timeLabel" class="mt-3"></p>

                                <!-- Adjusted Total Rendered Hours section with spacing -->
                                <div class="total-hours mt-4 p-2">
                                    <strong>Total Rendered Hours:</strong>
                                    <br> <!-- Adds line break for better spacing -->
                                    <span id="renderedHours" class="fw-bold fs-5 d-block mt-2">Loading...</span>

                                    <?php
                                    $studCode = $_SESSION['stud_code'] ?? null;

                                    // Store break time per student
                                    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setBreakTime'])) {
                                        if (isset($_POST['breakDuration']) && $studCode) {
                                            $selectedBreak = intval($_POST['breakDuration']);
                                            $_SESSION['student_breaks'][$studCode] = $selectedBreak;
                                        }
                                    }
                                    // Retrieve break time for the logged-in student
                                    $breakMinutes = $_SESSION['student_breaks'][$studCode] ?? 60;
                                    //Define break options
                                    $breakOptions = [
                                        30 => '30 minutes',
                                        60 => '1 hour',
                                        90 => '1 hour 30 minutes',
                                    ];
                                    ?>
                                    <!--Break Duration Form -->
                                    <form method="POST" class="mt-4">
                                        <strong>Break Time:</strong>
                                        <select name="breakDuration" class="form-select form-control mt-2" required>
                                            <?php foreach ($breakOptions as $value => $label): ?>
                                                <option value="<?= $value ?>" <?= ($breakMinutes == $value) ? 'selected' : '' ?>><?= $label ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" name="setBreakTime" class="btn btn-success btn-sm mt-2">Set Break Time</button>
                                    </form>
                                    <!--Display Current Setting -->
                                    <p class="mt-2 text-muted small">Current Break: <?= $breakMinutes ?> minutes</p>



                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12 border mt-3 rounded">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Time in</th>
                                            <th>Time out</th>
                                            <th>Break</th>
                                            <th>Total hours</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $dateFrom = null;
                                        $dateTo = null;
                                        if (isset($_POST['filter'])) {
                                            $dateFrom = $_POST['dateFrom'];
                                            $dateTo = $_POST['dateTo'];
                                            $post->loadLogs($connect, $studentNumber, $dateFrom, $dateTo, $semester, $schoolYear);
                                        } else {
                                            $post->loadLogs($connect, $studentNumber, $dateFrom, $dateTo, $semester, $schoolYear);
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 border mt-3 rounded">
                                <form method="post" class="form-inline p-4">
                                    <label for="dateFrom">Filter Date</label>
                                    <div class="input-group mb-2">
                                        <span class="input-group-text ml-3">From:</span>
                                        <input type="date" id="dateFrom" name="dateFrom" class="form-control">
                                        <span class="input-group-text ml-3">To:</span>
                                        <input type="date" id="dateTo" name="dateTo" class="form-control">
                                    </div>
                                    <div class="input-group-append mb-2 ml-3 text-right">
                                        <input type="submit" name="filter" value="Submit" class="btn btn-outline-secondary">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateTime() {
            let currentTime = new Date();
            let seconds = currentTime.getSeconds();
            currentTime.setSeconds(seconds + 10); // Add 10 seconds to the current time

            let formattedTime = currentTime.toLocaleString('en-US', {
                hour: 'numeric',
                minute: 'numeric',
                second: 'numeric',
                hour12: true
            });

            document.getElementById('timeLabel').textContent = 'Time: ' + formattedTime;
        }

        updateTime();
        setInterval(updateTime, 500);

        function executePHPFunction() {

            var logState = "<?php echo $logState; ?>";
            var studentNum = "<?php echo $studentNumber; ?>";
            var logDept = "<?php echo $dept; ?>"
            var logCourse = "<?php echo $course; ?>"
            var logSection = "<?php echo $section; ?>"
            var logCompany = "<?php echo $companyCode; ?>"

            var xhr = new XMLHttpRequest();
            xhr.timeout = 2000;
            xhr.open("POST", "includes/logs.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    console.log(xhr.responseText);
                }
            }
            xhr.send("logState=" + logState +
                "&studentNum=" + studentNum +
                "&log_dept=" + logDept +
                "&log_course=" + logCourse +
                "&log_section=" + logSection +
                "&log_company=" + logCompany);

            setTimeout(function() {
                location.reload();
            }, 1000);
        }

        function fetchStudentHours() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "redered_hours.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById("renderedHours").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Fetch total hours for the logged-in student every 10 seconds and on page load
        setInterval(fetchStudentHours, 10000);
        fetchStudentHours();
    </script>
</body>

</html>