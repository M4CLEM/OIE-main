<?php
session_start();
include("includes/connection.php");

if (isset($_SESSION['registration_success'])) {
    echo '<div id="registrationSuccess" class="alert alert-success ml-5" role="alert">';
    echo '<button type="button" class="close" onclick="dismissSuccessMessage()">&times;</button>';
    echo $_SESSION['registration_success'];
    echo '</div>';
    unset($_SESSION['registration_success']);
}

$output = "";

if (isset($_POST['login'])) {

    $uname = $_POST['uname'];
    $pass = $_POST['pass'];

    if (empty($uname) || empty($pass)) {
        // Handle empty fields
    } else {

        $query = "SELECT * FROM users WHERE username='$uname' AND password='$pass'";
        $res = mysqli_query($connect, $query);

        if (mysqli_num_rows($res) == 1) {
            $row = mysqli_fetch_assoc($res);
            $role = $row['role']; // Fetch role from the database result

            if ($role == "CIPA") {
                $_SESSION['CIPA'] = $uname;
                header("Location: cipa-director-portal/student-interns.php");
            } else if ($role == "Coordinator") {
                $_SESSION['coordinator'] = $uname;
                $stmt = $connect->prepare("SELECT department FROM users WHERE username = ?");
                $stmt->bind_param("s", $uname);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $_SESSION['department'] = $row['department'];
                }
                header("Location: coordinator-portal/masterlist.php");
            } else if ($role == "Adviser") {
                $stmt = $connect->prepare("SELECT * FROM listadviser WHERE email = ?");
                $stmt->bind_param("s", $uname);
                $stmt->execute();
                $resultAdv = $stmt->get_result();
                if ($resultAdv->num_rows > 0) {
                    $rowsAdv = $resultAdv->fetch_assoc();
                    $_SESSION['adviser'] = $uname;
                    $_SESSION['dept_adv'] = $rowsAdv['dept'];
                    $_SESSION['dept_crs'] = $rowsAdv['course'];
                    $stmtSections = $connect->prepare("SELECT section FROM listadviser WHERE email = ?");
                    $stmtSections->bind_param("s", $uname);
                    $stmtSections->execute();
                    $resultSections = $stmtSections->get_result();
                    $sections = array();
                    while ($row = $resultSections->fetch_assoc()) {
                        $sections[] = $row['section'];
                    }
                    $_SESSION['dept_sec'] = $sections;
                    header("Location: adviser-portal/student-list.php");
                }
            } else if ($role == "Student") {
                $_SESSION['student'] = $uname;
                $stmtStudent = $connect->prepare("SELECT department FROM users WHERE username = ?");
                $stmtStudent->bind_param("s", $uname);
                $stmtStudent->execute();
                $studentResult = $stmtStudent->get_result();
                if ($studentResult->num_rows > 0) {
                    $rowsStudent = $studentResult->fetch_assoc();
                    $_SESSION['department'] = $rowsStudent['department'];
                }
                $stmtCourse = $connect->prepare("SELECT course FROM studentinfo WHERE email = ?");
                $stmtCourse->bind_param("s", $uname);
                $stmtCourse->execute();
                $courseResult = $stmtCourse->get_result();
                if ($courseResult->num_rows > 0) {
                    $rowsCourse = $courseResult->fetch_assoc();
                    $_SESSION['course'] = $rowsCourse['course'];
                }
                header("Location: student-portal/student.php");
            } else if ($role == "IndustryPartner") {
                $_SESSION['IndustryPartner'] = $uname;
                $stmtCompany = $connect->prepare("SELECT companyName FROM users WHERE username = ?");
                $stmtCompany->bind_param("s", $uname);
                $stmtCompany->execute();
                $companyNameResult = $stmtCompany->get_result();
                if ($companyNameResult->num_rows > 0) {
                    $rowsCompanyName = $companyNameResult->fetch_assoc();
                    $_SESSION['companyName'] = $rowsCompanyName['companyName'];
                }
                $sql = "SELECT companyCode FROM company_info WHERE trainerEmail = ?";
                $stmt = $connect->prepare($sql);
                $stmt->bind_param("s", $uname);
                $stmt->execute();
                $stmt->bind_result($companyCode);
                if ($stmt->fetch()) {
                    $_SESSION['IP_num'] = $companyCode;
                }
                $_SESSION['trainerEmail'] = $uname;
                $stmt->close();
                $connect->close();
                header("Location: industry-portal/industryPartner.php");
            }

            $output .= "You have logged in successfully";
        } else {
            $output .= "Failed to login";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/login.css">
    <title>OJT Portal</title>
</head>

<body>

    <!-- Main Container -->
    <div class="container d-flex justify-content-center align-items-center min-vh-100">

        <!-- Login Container -->
        <div class="row border rounded-5 p-4 bg-white shadow box-area">

            <!-- Left Box -->
            <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box" style="background: #104911;">
                <div class="featured-image mb-3">
                    <img src="img/logo2.png" class="img-fluid" style="width: 200px;">
                </div>
                <p class="text-white fs-2" style="font-weight: 600;">OJT Portal</p>
            </div>

            <!-- Right Box -->
            <div class="col-md-6 right-box">
                <div class="row align-items-center">
                    <form method="post">
                        <div class="header-text mb-2">
                            <h2>Hello!</h2>
                            <p>Welcome to PLMUN OJT Portal</p>
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" name="uname" class="form-control form-control-lg bg-light fs-6" placeholder="Email address">
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="pass" class="form-control form-control-lg bg-light fs-6" placeholder="Password">
                        </div>

                        <div class="input-group mb-3">
                            <!-- Removed role dropdown -->
                        </div>

                        <div class="input-group mb-3">
                            <input type="submit" name="login" class="btn btn-success w-100 rounded-2" value="Login"><span class="fa fa-sign-in fw-fa"></span>
                        </div>

                        <div class="row">
                            <small>Don't have an account? <a href="student-register.php">Sign Up</a></small>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div class="modal" id="loadingModal" tabindex="-1" role="dialog" style="display: none;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                    <div class="spinner-border text-success" role="status" style="width: 4rem; height: 4rem;"></div>
                    <h5 class="text-success mt-3">Please Wait...</h5>
                    <p>Logging in...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelector("form").addEventListener("submit", function() {
            document.getElementById("loadingModal").style.display = "block";
        });
    </script>

</body>

</html>

<script>
    function dismissSuccessMessage() {
        document.getElementById('registrationSuccess').style.display = 'none';
    }
</script>
