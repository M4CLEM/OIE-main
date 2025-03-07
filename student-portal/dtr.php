<?php
session_start();
include_once("../includes/connection.php");

include("includes/logs.php");
$post = new updatelogs();

$uname = $_SESSION['student'];

$sql = "SELECT * FROM studentinfo WHERE email = '$uname'";
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
}

$studentNumber = $_SESSION['stud_code'];
$firstName = $_SESSION['stud_first'];
$middleName = $_SESSION['stud_mid'];
$surName = $_SESSION['stud_last'];
$dept = $_SESSION['stud_dept'];
$course = $_SESSION['stud_course'];
$section = $_SESSION['stud_section'];
$companyCode = $_SESSION['stud_company'];
$schoolYear = $_SESSION['stud_SY'];
$currentTime = date("h:i:sa");
$logState = "";



$data_query =  mysqli_query($connect, "SELECT status FROM logdata WHERE student_num='$studentNumber' 
                                    ORDER BY date DESC, time_in DESC LIMIT 1");

if(!$data_query) {
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

</head>
<body id="page-top">

    <!-- Page Wrapper -->
   <div id="wrapper">

        <!--Sidebar Wrapper-->
        <aside id="sidebar" class="expand">
            <?php include('../elements/stud_sidebar.php')?>
        </aside>

        <div class="main">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">

                <!-- Title -->
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Attendance</h4>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php (isset($_SESSION['student'])) ?> <?php echo $_SESSION['student']; ?></span>
                            <img class="img-profile rounded-circle"
                                src="../img/undraw_profile.svg">
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                            aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                Profile
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                Settings
                            </a>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                Activity Log
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="../logout.php" data-toggle="logout" data-target="logout">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            <!-- End of Topbar -->

            <!-- Main Content -->
            <div id="content" class="py-2">

                <div class="col-lg-12 mb-4">
                    <div class="container mt-6">
                        <div class="row">

                        
                            <div class='card-body col-md-9 border mt-2 rounded p-4'>
                                <h2 class='card-title'>On the Job Training: <?php echo $schoolYear?></h5><br>
                                <?php $post->loadInfo($connect, $dept, $course, $studentNumber, $section); ?>
                            </div>

                            <div class="col-md-3 border mt-2 p-5 text-center rounded">

                                <br><button id="toggleButton" class="btn btn-lg btn-block 
                                <?php echo $logState === 'In' ? 'btn-success' : 'btn-danger'; ?>" 
                                onclick="executePHPFunction()"><br><?php echo $logState ?><br><br></button>

                                <p id="timeLabel" class="mt-3"> </p> 
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
                                        <th>Total hours</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            if (isset($_POST['filter'])) {
                                                $dateFrom = $_POST['dateFrom'];
                                                $dateTo = $_POST['dateTo'];
                                                $post->loadLogs($connect, $studentNumber, $dateFrom, $dateTo);
                                            } else {
                                                $post->loadLogs($connect, $studentNumber);
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
        xhr.send("logState=" + logState 
                + "&studentNum=" + studentNum 
                + "&log_dept=" + logDept 
                + "&log_course=" + logCourse 
                + "&log_section=" + logSection 
                + "&log_company=" + logCompany);

        setTimeout(function() {
            location.reload();
        }, 1000);
    }
</script>
</body>
</html>