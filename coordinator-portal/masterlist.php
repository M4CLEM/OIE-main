<?php
session_start();
include_once("../includes/connection.php");
$query = "select * from listadviser";
$result = mysqli_query($connect, $query);
?>
    
<!DOCTYPE html>
<html lang="en">

<head>

    <?php include("../elements/meta.php"); ?>
    <title>OJT COORDINATOR PORTAL</title>
    <?php include("embed.php"); ?>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!--Sidebar Wrapper-->
        <aside id="sidebar" class="expand">
            <?php include('../elements/cood_sidebar.php')?>
        </aside>

        <div class="main">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">

            <!-- Title -->
            <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Masterlist - <?php echo $_SESSION['coordinator'];?></h4>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php (isset($_SESSION['coordinator'])) ?> <?php echo $_SESSION['coordinator']; ?></span>
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
                    <!-- Illustrations -->
                    <div class="card shadow mb-4">

                        <div class="card-body" style="font-size: 13px; height: 300px;">
                            <style>
                                .table td, .table th {
                                    font-size:  12px;
                                }
                            </style>
                            <div class="table-responsive" style=" height: 300px;">
                            
                                <div class="row">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="dropdown">

                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdowncourse" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Select Course
                                            </button>

                                            <ul class="dropdown-menu" aria-labelledby="dropdowncourse">
                                                <!--  COURSE DROPDOWN FIX ISSUE     -->
                                                <?php 

                                                    $stmtSec = $connect->prepare("SELECT * FROM course_list WHERE department = ?");

                                                    $stmtSec->bind_param("s", $_SESSION['dept_adv']);

                                                    if (!$stmtSec->execute()) {
                                                        die("Error executing the statement: " . $stmtSec->error);
                                                    }

                                                    $resultSec = $stmtSec->get_result();
                                                    
                                                    if ($resultSec->num_rows > 0) {

                                                        while ($rowSec = $resultSec->fetch_assoc()) {
                                                    ?>

                                                        <li>
                                                            <a class="dropdown-item" onclick="showSections('<?php echo $rowSec['department']?>', '<?php echo $rowSec['course'];?>');
                                                            updateButtonCourse('<?php echo $rowSec['course'];?>')" data-course="<?php echo $rowSec['course'];?>"><?php echo $rowSec['course'];?>
                                                            </a>
                                                        </li>

                                                    <?php 
                                                    } 
                                                    }else {
                                                        echo "No data found.";
                                                    }  
                                                    ?> 	

                                            </ul>
                                        </div>

                                        <div class="dropdown">
                                            <button class="btn btn-secondary dropdown-toggle ml-2" type="button" id="dropdownsection" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Select Section
                                            </button>
                                            <ul class="dropdown-menu section-list" aria-labelledby="dropdownsection"></ul>
                                        </div>

                                        <div class="dropdown">
                                            <div class="col-md-12 ">  
                                                <select name="status" id="statusDropdown" class="form-control my-2 text-center" style="background-color: green; color: white;">
                                                    <option hidden disable value="select ">Select Status</option>
                                                    <option value="Deployed">Deployed</option>
                                                    <option value="Undeployed">Undeployed</option>
                                                    <option value="Completed">Completed</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            <table class="table table-bordered" width="100%" cellspacing="0"> 
                                <thead>
                                    <tr>
                                        <th width="15%"scope="col">StudentID</th>
                                        <th width="25%" scope="col">Name</th>
                                        <th width="30%" scope="col">Email</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Grade</th>
                                        <th width="12%" align="center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="student-list"></tbody>
                            </table>                                    
                        </div>
                    </div>
                </div>
                
                <a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal" style="font-size: 13px;">  <i class="fa fa-plus-circle fw-fa"></i> Add new student</a> 
                <a class="btn btn-primary btn-sm" href="addmasterlist.php" style="font-size: 13px;"> Add masterlist</a>

            </div>
        </div>
    </div>

    <!-- add Modal-->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="functions/student-add.php" method="POST">

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Student</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control" id="studentID" name="studentID" type="text" value="" autocomplete="none" placeholder="Student ID" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                    <input class="form-control input-sm" id="firstname" name="firstname" type="text" value="" autocomplete="none" placeholder="Firstname" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                                </div>
                            </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control input-sm" id="lastname" name="lastname" type="text" value="" autocomplete="none" placeholder="Lastname" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control input-sm" id="course" name="course"  type="text" value="" autocomplete="none" placeholder="Course" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control input-sm" id="department" name="department" type="text" value="" required  onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" placeholder="department" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control input-sm" id="email" name="email"  type="text" value="" autocomplete="none" placeholder="email" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">  
                                <select name="status" class="form-control my-2">
                                    <option hidden disable value="select ">Select status</option>
                                    <option value="Graduating">Graduating</option>
                                    <option value = "Almuni">Almuni</option>          
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary btn-sm" name="save" type="submit" ><span class="fa fa-save fw-fa"></span> Save</button>
                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>

    <script> 

        function updateButtonCourse(newText) {
            console.log("Updating course button text to:", newText); // Debugging
            var courseButton = document.getElementById('dropdowncourse');
            if (courseButton) {
                courseButton.innerHTML = newText; // Use innerHTML instead of innerText
            } else {
                console.error("Course button not found");
            }
        }

        function updateButtonSection(newText) {
            document.getElementById('dropdownsection').innerText = newText;
        }

        function showSections(department, course) {
            $.ajax({
                url: 'functions/get_sections.php',
                type: 'GET',
                data: { 
                    department: department,
                    course: course 
                },
                success: function(data) {
                document.querySelector('.section-list').innerHTML = data;
                }
            });
        }

        function showStudents(department, course, section) {
            $.ajax({
                url: 'functions/get_students.php',
                type: 'GET',
                data: {
                    department: department,
                    course: course,
                    section: section
                },
                success: function(data) {
                    document.querySelector('.student-list').innerHTML = data;
                }
            });
        }

        $(document).ready(function() {
            $('#dataTable').DataTable({
                "lengthMenu": [[10,  25,  50, -1], [10,  25,  50, "All"]], 
                "searching": false, 
            });
        });

    </script>

    <script>

        function filterTableByStatus() {
            var status = document.getElementById('statusDropdown').value; // Get the selected status
            var table = document.querySelector('.table'); // Get the table
            var rows = table.getElementsByTagName('tr'); // Get all table rows

            for (var i = 0; i < rows.length; i++) {
                var statusCell = rows[i].getElementsByTagName('td')[3]; // Assuming the status is in the 4th column
                if (statusCell) {
                    var txtValue = statusCell.textContent || statusCell.innerText;
                    if (txtValue.indexOf(status) > -1) {
                        rows[i].style.display = ""; // Show the row if the status matches
                    } else {
                        rows[i].style.display = "none"; // Hide the row if the status doesn't match
                    }
                }
            }
        }

        document.getElementById('statusDropdown').addEventListener('change', filterTableByStatus);
        
    </script>


</body>
</html>	