<?php
session_start();
include_once("../includes/connection.php");
$query = "select * from listadviser";
$result = mysqli_query($connect, $query);
$SYquery = "Select * from academic_year";
$SYresult = mysqli_query($connect, $SYquery);
$department = $_SESSION['department'];
?>
    
<!DOCTYPE html>
<html lang="en">

<head>

    <?php include("../elements/meta.php"); ?>
    <title>OJT COORDINATOR PORTAL</title>
    <?php include("embed.php"); ?>
    <link rel="stylesheet" href="../assets/css/new-style.css">
    
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
                <?php include('../elements/cood_navbar_user_info.php')?>
            </nav>
            <!-- End of Topbar -->

            <!-- Main Content -->
            <div id="content" class="py-2">

                <div class="col-lg-12 mb-4">
                    <!-- Illustrations -->
                    <div class="card shadow mb-4">

                        <div class="card-body" style="font-size: 13px; height: 550px;">
                            <style>
                                .table td, .table th {
                                    font-size:  12px;
                                }
                            </style>
                            <div class="table-responsive" style=" height: 550px;">
                            
                                <div class="row">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="dropdown">

                                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdowncourse" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Select Course
                                            </button>

                                            <ul class="dropdown-menu" aria-labelledby="dropdowncourse">
                                                <?php 

                                                    $stmtSec = $connect->prepare("SELECT * FROM course_list WHERE department = ?");

                                                    $stmtSec->bind_param("s", $_SESSION['department']);

                                                    if (!$stmtSec->execute()) {
                                                        die("Error executing the statement: " . $stmtSec->error);
                                                    }

                                                    $resultSec = $stmtSec->get_result();
                                                    
                                                    if ($resultSec->num_rows > 0) {

                                                        while ($rowSec = $resultSec->fetch_assoc()) {
                                                    ?>

<li>
  <a class="dropdown-item" 
     onclick="showSections('<?php echo $rowSec['department']?>', '<?php echo $rowSec['course'];?>'); 
              updateButtonCourse('<?php echo $rowSec['course'];?>');
              reloadStudents();"
     data-course="<?php echo $rowSec['course'];?>">
    <?php echo $rowSec['course'];?>
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
                                            <div class="col-md-12">
                                                <select name="schoolYear" id="schoolYearDropdown" class="form-control my-2 text-center" style="background-color: #6b6d7d; color: white ;">
                                                    <option value="">Select School Year</option>
                                                    <?php while ($rowSY = mysqli_fetch_assoc($SYresult)) {?>
                                                        <option value="<?php echo htmlspecialchars($rowSY['schoolYear']);?>" data-SY="<?php echo htmlspecialchars($rowSY['schoolYear']);?>">
                                                            <?php echo htmlspecialchars($rowSY['schoolYear']);?>
                                                        </option>
                                                    <?php }?>
                                                </select>
                                            </div>
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
                                        <th width="10"scope="col">StudentID</th>
                                        <th width="25%" scope="col">Name</th>
                                        <th width="30%" scope="col">Email</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Semester</th>
                                        <th scope="col">School Year</th>
                                        <th scope="col">Grade</th>
                                        <th width="12%" align="center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="student-list"></tbody>
                            </table>                                    
                        </div>
                    </div>
                </div>
                
                <a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal" style="font-size: 13px;">  <i class="fa fa-plus-circle fw-fa"></i> Add New Student</a> 
                <a class="btn btn-primary btn-sm" href="addmasterlist.php" style="font-size: 13px;"> Add Masterlist</a>

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
                                    <input class="form-control input-sm" id="lastName" name="lastName" type="text" value="" autocomplete="none" placeholder="Last Name" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                                </div>
                            </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control input-sm" id="firstName" name="firstName" type="text" value="" autocomplete="none" placeholder="First Name" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control input-sm" id="course" name="course"  type="text" value="" autocomplete="none" placeholder="Course" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control input-sm" id="dept" name="dept" type="text" value="" autocomplete="none" placeholder="Department" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control input-sm" id="section" name="section" type="text" value="" required  onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" placeholder="Section" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control input-sm" id="year" name="year"  type="text" value="" autocomplete="none" placeholder="Year Level" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">  
                                <select name="semester" class="form-control my-2">
                                    <option hidden disable value="select ">Select Semester</option>
                                    <option value="1st Semester">1st Semester</option>
                                    <option value = "2nd Semester">2nd Semester</option>          
                                </select>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control input-sm" type="number" name="hourRequirement" id="hourRequirement" autocomplete="none" placeholder="Hours Required" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control input-sm" id="SY" name="SY" type="text" value="" autocomplete="none" placeholder="School Year" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off">
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
        var sectionButton = document.getElementById('dropdownsection');
        if(sectionButton){
            sectionButton.innerHTML = newText;
        }
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

    function showStudents(department, course, section, schoolYear, status) {
        $.ajax({
            url: 'functions/get_students.php',
            type: 'GET',
            data: {
                department: department,
                course: course,
                section: section,
                schoolYear: schoolYear,
                status: status
            },
            success: function(data) {
                document.querySelector('.student-list').innerHTML = data;
            }
        });
    }

    // Helper to get current filter values
    function getCurrentFilters() {
        const department = "<?php echo $_SESSION['department']; ?>";
        const course = document.getElementById('dropdowncourse').innerText.trim();
        const section = document.getElementById('dropdownsection').innerText.trim();
        const schoolYear = document.getElementById('schoolYearDropdown').value;
        const status = document.getElementById('statusDropdown').value;
        return { department, course, section, schoolYear, status };
    }

    // Reload students based on current filters
    function reloadStudents() {
        const filters = getCurrentFilters();

        // If course or section is "Select Course"/"Select Section", treat as empty
        const courseVal = (filters.course === "Select Course") ? "" : filters.course;
        const sectionVal = (filters.section === "Select Section") ? "" : filters.section;

        showStudents(filters.department, courseVal, sectionVal, filters.schoolYear, filters.status);
    }

    // Attach event listeners to dropdowns to reload students dynamically
    document.addEventListener('DOMContentLoaded', function() {
        // School Year change
        document.getElementById('schoolYearDropdown').addEventListener('change', reloadStudents);

        // Status change
        document.getElementById('statusDropdown').addEventListener('change', reloadStudents);

        // Since course and section dropdowns are built dynamically, 
        // ensure you call reloadStudents() after you update those buttons
        // For example, inside your existing onclick handlers, call reloadStudents();

        // Optionally, you could also reload students initially (e.g., page load)
        reloadStudents();
    });
</script>

<!-- Loading Modal -->
<div id="loadingModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; text-align: center;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 10px;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="sr-only">Loading...</span>
        </div>
        <p>Processing... Please wait.</p>
    </div>
</div>

<!-- Loading script to show the loading Modal -->
<script>
    function showLoading() {
        document.getElementById("loadingModal").style.display = "block";
    }
    
    document.addEventListener("DOMContentLoaded", function() {
        let saveButton = document.querySelector("button[name='save']");
        if (saveButton) {
            saveButton.addEventListener("click", function() {
                showLoading();
            });
        }
    });
</script>

</body>
</html>	