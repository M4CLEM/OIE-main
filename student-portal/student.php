<?php
session_start();
include_once("../includes/connection.php");
$query = "select * from users";
$result = mysqli_query($connect, $query);

$currentPage = basename($_SERVER['PHP_SELF']);
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

            <?php
                
                $email = $_SESSION['student'];
                $query = "SELECT * FROM studentinfo WHERE email ='$email'";
                $result = mysqli_query($connect, $query);
                while ($rows = mysqli_fetch_array($result)) {

                    $studentID = $rows['studentID'];
                    $firstname = $rows['firstname'];
                    $middlename = $rows['middlename'];
                    $lastname = $rows['lastname'];
                    $address = $rows['address'];
                    $age = $rows['age'];
                    $gender = $rows['gender'];
                    $contactNo = $rows['contactNo'];
                    $department = $rows['department'];
                    $course = $rows['course'];
                    $email = $rows['email'];
                    $status = $rows['status'];
                    $image = $rows['image'];
                    $objective = $rows['objective'];
                    $skills = explode(',', $rows['skills']);
                    $seminars = explode(',', $rows['seminars']);

                }

            ?>

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">

            <!-- Title -->
            <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Student Interns</h4>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                            <?php (isset($_SESSION['student'])) ?> <?php echo $_SESSION['student']; ?></span>
                            <img class="img-profile rounded-circle" src="<?php echo $image ? $image : '../img/undraw_profile.svg'; ?>">
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
                            <a class="dropdown-item" href="../logout.php" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </li>

                </ul>

            </nav>
            <!-- End of Topbar -->

            
            
            <!-- First Column -->
            <div class="p-4">
                <!-- Illustrations -->
                <div class="card shadow mb-4">

                    <div class="card-header py-2">
                        <div class="col-lg-12 py-3">
                            <div class="d-flex align-items-center">
                                <h4 class="font-weight-bold text-dark mt-2">Profile Details</h4>
                                <button class="btn btn-sm btn-primary export-btn ml-2" data-studentid="<?php echo $studentID; ?>"><i class="fas fa-file-export"></i></button>
                                <button type="button" id="editButton" class="btn btn-sm btn-primary ml-2"><i class="fas fa-edit"></i></button>
                                <button type="button" id="revertButton" class="btn btn-sm btn-primary ml-2" hidden><i class="fas fa-window-close"></i></button>
                            </div>
                   
                        </div>
                    </div>

                    <form action="update_stud_info.php" method="POST">
                    
                        <div class="card-body row">

                            <div class="col col-md-6 px-3">
                                <div class="row">
                                    <div class="form-group col col-md-6">
                                        <div class="d-flex justify-content-center mx-auto">
                                            <img class="img-profile rounded-circle" id="default" src="<?php echo $image ? $image : '../img/undraw_profile.svg'; ?>" width="200" height="200">
                                        </div>
                                    </div>
                                    <div class="col col-md-6">
                                        <div class="form-group">
                                            <label for="name">Name:</label>
                                            <h5 class="font-weight-bold text-center"><?php echo $lastname . ', ' . $firstname . ' ' . $middlename ?></h5>
                                        </div>
                                        <div class="form-group">
                                            <div class="">
                                                <label for="studentID">Student ID</label>
                                                <input class="form-control text-center" id="studentID" name="studentID" type="text" value="<?php echo $studentID; ?>" autocomplete="none" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="">
                                                <label for="status">Deployment Status</label>
                                                <p class="h6 text-center" id="status" name="status" type="text" value="<?php echo $status ?>"><?php echo $status ?></p>
                                                
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <div class="">
                                            <label for="department">Department</label>
                                            <input class="form-control " id="department" name="department" type="text" value="<?php echo $department; ?>" required onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" readonly></input>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <div class="">
                                            <label for="course">Course</label>
                                            <input class="form-control " id="course" name="course" type="text" value="<?php echo $course; ?>" autocomplete="none" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col">
                                        <div class="">
                                            <label for="contact">Contact No.</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="basic-addon1">+63</span>
                                                </div>
                                                <input class="form-control" id="contact" name="contact" type="text" value="<?php echo $contactNo; ?>" autocomplete="off" readonly></input>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col">
                                        <div class="">
                                            <label for="email">Instituitional Email</label>
                                            <input class="form-control" id="email" name="email" type="text" value="<?php echo $email; ?>" autocomplete="off" readonly></input>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col col-md-6 px-3">
                                <div class="row">
                                    <div class="form-group col">
                                        <label for="objective" class="font-weight-bold">Objective:</label>
                                        <p style="text-align: justify;"><?php echo $objective ?></p>
                                        <textarea class="form-control" id="objective" name="objective" autocomplete="off" rows="4" readonly hidden><?php echo $objective; ?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col">
                                        <label for="skills" class="font-weight-bold">Skills:</label>
                                        <?php if (!empty($skills)) : ?>
                                            <ul class="genlist">
                                                <?php foreach ($skills as $skill) : ?>
                                                    <li><?php echo htmlspecialchars($skill); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else : ?>
                                            <p>No skills available.</p>
                                        <?php endif; ?>
                                        <textarea class="form-control" id="skills" name="skills" autocomplete="off" rows="4" readonly hidden><?php echo implode(',', $skills); ?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col">
                                        <label for="skills" class="font-weight-bold">Seminars Attended:</label>
                                        <?php if (!empty($seminars)) : ?>
                                            <ul class="genlist">
                                                <?php foreach ($seminars as $smnrs) : ?>
                                                    <li><?php echo htmlspecialchars($smnrs); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else : ?>
                                            <p>No work experience available.</p>
                                        <?php endif; ?>
                                        <textarea class="form-control" id="seminars" name="seminars" autocomplete="off" rows="4" readonly hidden><?php echo implode(',', $seminars); ?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col text-right">
                                        <button type="submit" id="submitButton" class="btn btn-primary" hidden>Save</button>
                                    </div>
                                </div>
                                <!-- End of Main Content -->
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Main Content -->

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="../logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script>
    document.getElementById('editButton').addEventListener('click', function() {
        var inputs = document.querySelectorAll('input[type="text"], textarea');
        inputs.forEach(function(input) {
            var id = input.id;
            if (id !== 'studentID' && id !== 'course' && id !== 'department' && id !== 'email'&& id !== 'section') {
                input.removeAttribute('readonly');
                input.removeAttribute('hidden');
            }
        });
        document.querySelectorAll('p').forEach(function(paragraph) {
            paragraph.setAttribute('hidden', true);
        });
        document.querySelectorAll('.genlist').forEach(function(ul) {
            ul.setAttribute('hidden', true);
        });
        // Hide input and show select
        var inputStatus = document.getElementById('status');
        inputStatus.setAttribute('hidden', true);
        var selectStatus = document.getElementById('statusSelect');
        selectStatus.removeAttribute('hidden');
        var submitButton = document.getElementById('submitButton');
        submitButton.removeAttribute('hidden');
        var revertButton = document.getElementById('revertButton');
        revertButton.removeAttribute('hidden');
        var editButton = document.getElementById('editButton');
        editButton.setAttribute('hidden', true);
    });
    document.getElementById('revertButton').addEventListener('click', function() {
        var inputs = document.querySelectorAll('input[type="text"], textarea');
        inputs.forEach(function(input) {
            var id = input.id;
            if (id === 'contact') {
                input.setAttribute('readonly', true);
            } else if (id !== 'studentID' && id !== 'course' && id !== 'department' && id !== 'email'&& id !== 'section') {
                input.setAttribute('readonly', true);
                input.setAttribute('hidden', true);
            }
        });
        document.querySelectorAll('p').forEach(function(paragraph) {
            paragraph.removeAttribute('hidden');
        });
        document.querySelectorAll('.genlist').forEach(function(ul) {
            ul.removeAttribute('hidden');
        });
        // Show input and hide select
        var inputStatus = document.getElementById('status');
        inputStatus.removeAttribute('hidden');
        var selectStatus = document.getElementById('statusSelect');
        selectStatus.setAttribute('hidden', true);
        var submitButton = document.getElementById('submitButton');
        submitButton.setAttribute('hidden', true);
        var revertButton = document.getElementById('revertButton');
        revertButton.setAttribute('hidden', true);
        var editButton = document.getElementById('editButton');
        editButton.removeAttribute('hidden');
    });

    $(document).ready(function() {
        $('form').submit(function(e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: 'update_stud_info.php',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: response.message,
                            toast: true,
                            position: 'top',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            window.location.href = 'student.php';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...Something went wrong!',
                            showConfirmButton: false,
                            toast: true,
                            position: 'top',
                            timer: 1500
                        });
                    }
                }
            });
        });
    });
    $(document).ready(function() {
        $(".export-btn").click(function() {
            var studentID = $(this).data("studentid");
            window.location.href = "export-resume.php?studentID=" + studentID;
        });
    });
</script>
</html>