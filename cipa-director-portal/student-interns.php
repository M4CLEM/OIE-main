<?php
    session_start();
    include_once("../includes/connection.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("../elements/meta.php"); ?>
    <title>CIPA ADMIN</title>
    <?php include("embed.php"); ?>
    <link rel="stylesheet" href="../assets/css/new-style.css">
</head>


<body id="page-top">

    <!-- Page Wrapper -->
    <div class="wrapper">

        <!--Sidebar Wrapper-->
        <aside id="sidebar" class="expand">
            <?php include('../elements/cipa_sidebar.php')?>
        </aside>

        <div class="main">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar static-top shadow mb-3">
                <!-- Dashboard Title -->
                <h2 class="my-0 mr-auto font-weight-bold text-dark ml-3">Student Interns</h2>
                <?php include('../elements/cipa_navbar_user_info.php') ?>
            </nav>
            <!-- End of Topbar -->

            <div id="content" class="py-2">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="d-flex align-items-center mb-3">
                            <div class="dropdown mr-3">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdowndept" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Select Department
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdowndept">

                                    <?php 
                                        $queryDept="select * from department_list";
                                        $resultDept=mysqli_query($connect,$queryDept);
                                        while($rowDept=mysqli_fetch_assoc($resultDept))
                                        {
                                    ?>
                                        <li>
                                            <button type="button" class="dropdown-item" onclick="showCourses('<?php echo $rowDept['department'];?>'); 
                                            updateButtonDept('<?php echo $rowDept['department'];?>')"><?php echo $rowDept['department'];?></button>
                                        </li>

                                    <?php 
                                        }
                                    ?> 	

                                </ul>
                            </div>

                            <div class="dropdown mr-3">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdowncourse" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Select Course
                                </button>
                                <ul class="dropdown-menu course-buttons" aria-labelledby="dropdowncourse"></ul>
                            </div>

                            <div class="dropdown mr-3">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownsection" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Select Section
                                </button>
                                <ul class="dropdown-menu section-list" aria-labelledby="dropdownsection"></ul>
                            </div>
                        </div>

                        <div class="col-lg-12 mb-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h4 class="m-0 font-weight-bold text-dark">List of Students</h4> 
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive table-bordered">
                                        <table class="table" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Student Number</th>
                                                    <th scope="col">Grade</th>
                                                </tr>
                                            </thead>
                                            <tbody class="student-list"></tbody>
                                        </table>
                                    </div>
                                <div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>


    <script>     

        function updateButtonDept(newText) {
            document.getElementById('dropdowndept').innerText = newText;
        }
        function updateButtonCourse(newText) {
            document.getElementById('dropdowncourse').innerText = newText;
        }
        function updateButtonSection(newText) {
            document.getElementById('dropdownsection').innerText = newText;
        }

        function showCourses(department) {
            $.ajax({
                url: 'functions/get_courses.php',
                type: 'GET',
                data: { department: department },
                success: function(data) {
                document.querySelector('.course-buttons').innerHTML = data;
                }
            });
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
                "lengthMenu": [[10,  25,  50, -1], [10,  25,  50, "All"]], // Remove this line to disable the length menu
                "searching": false, // Disables the search box
                // ... other options
            });
        });

    </script>
    
</body>
