<?php
session_start();
include_once("../includes/connection.php");

// Check if dept_sec is set and is an array
if (isset($_SESSION['dept_sec']) && is_array($_SESSION['dept_sec']) && count($_SESSION['dept_sec']) > 0) {
    // Create placeholders dynamically for the number of sections
    $placeholders = implode(',', array_fill(0, count($_SESSION['dept_sec']), '?'));
    $query = "SELECT * FROM studentinfo WHERE department= ? AND course= ? AND section IN ($placeholders) ORDER BY section ASC, lastName ASC";

    // Prepare the statement
    $stmt = $connect->prepare($query);

    // Merge department, course, and section values
    $params = array_merge([$_SESSION['dept_adv'], $_SESSION['dept_crs']], $_SESSION['dept_sec']);

    // Define parameter types
    $types = str_repeat('s', count($params));

    // Bind the parameters dynamically
    $stmt->bind_param($types, ...$params);

    // Execute the statement
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // Fetch all rows
        } else {
            echo "No results found for the given criteria.";
        }
    } else {
        echo "SQL Error: " . $stmt->error;
    }
} else {
    echo "No sections found for the adviser.";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>

    <?php include("../elements/meta.php"); ?>
    <title>Adviser Portal</title>
    <?php include("embed.php"); ?>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!--Sidebar Wrapper-->
        <aside id="sidebar" class="expand">
            <?php include('../elements/adv_sidebar.php') ?>
        </aside>

        <div class="main">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">

                <!-- Title -->
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Student Interns</h4>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php (isset($_SESSION['adviser'])) ?> <?php echo $_SESSION['adviser']; ?></span>
                            <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
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

            <!-- Begin Page Content -->
            <div class="col-lg-12 mb-4">

                <!-- Illustrations -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <div class="row justify-content-between align-items-center">
                            <div class="col-md-3">
                                <h6 class="m-0 font-weight-bold text-dark">STUDENT INFORMATION</h6>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col">
                                        <div class="input-group input-group-sm">
                                            <select name="export_filter" id="export_filter" class="form-select form-select-sm">
                                                <option value="">Select Option</option>
                                                <option value="Student Information">Student Info</option>
                                                <option value="Deployed">Deployed</option>
                                                <option value="Undeployed">Undeployed</option>
                                                <option value="Completed">Completed</option>
                                            </select>
                                            <button class="export-btn btn btn-primary" disabled type="button">Export <i class="far fa-file-pdf"></i></button>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <?php
                                        $email = $_SESSION['adviser'];
                                        // Assuming $connect is your mysqli connection object
                                        $getsections = "SELECT section FROM listadviser WHERE email = '$email'";
                                        $sections = mysqli_query($connect, $getsections);

                                        // Check if query was successful
                                        if ($sections) {
                                            echo '<select name="sections" id="sections" class="form-control form-control-sm">';
                                            // Default option
                                            echo '<option value="All Sections">All Sections</option>'; // Change the value to "All Sections"
                                            // Loop through each row in the result set
                                            while ($sect = mysqli_fetch_assoc($sections)) {
                                                echo '<option value="' . $sect['section'] . '">' . $sect['section'] . '</option>';
                                            }
                                            echo '</select>';
                                        } else {
                                            echo "Error: " . mysqli_error($connect);
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="studentTable" class="table table-hover" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th colspan="8" class="text-center font-weight-bold border-0 pt-1"><input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search..."></th>
                                    </tr>
                                    <tr>
                                        <th scope="col">StudentID</th>
                                        <th scope="col">First Name</th>
                                        <th scope="col">Middle Name</th>
                                        <th scope="col">Last Name</th>
                                        <th scope="col">Department</th>
                                        <th scope="col">Course</th>
                                        <th scope="col">Section</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $row['studentID'] . "</td>";
                                            echo "<td>" . $row['firstname'] . "</td>";
                                            echo "<td>" . $row['middlename'] . "</td>";
                                            echo "<td>" . $row['lastname'] . "</td>";
                                            echo "<td>" . $row['department'] . "</td>";
                                            echo "<td>" . $row['course'] . "</td>";
                                            echo "<td>" . $row['section'] . "</td>";
                                            echo "<td>" . $row['status'] . "</td>";

                                            $stmtGrade = $connect->prepare("SELECT * FROM student_grade WHERE email = ?");
                                            $stmtGrade->bind_param("s", $row['email']);
                                            $stmtGrade->execute();
                                            $resultGrade = $stmtGrade->get_result();
                                        
                                            $totalGrade = 0;
                                        
                                            while($rowGrade = $resultGrade->fetch_assoc()){
                                                $totalGrade += intval($rowGrade['grade']);
                                            }

                                            echo "<td> <p>{$totalGrade}</p></td>";

                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='8'>No students found.</td></tr>";
                                    }
                                    ?>
                                    <tr id='noResult' class='text-center' style='display: none;'>
                                        <td colspan='8'>No Results Found</td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
    <!-- End of Main Content -->

    </div>
    </div>

    <!-- add Modal-->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                <input class="form-control input-sm" id="course" name="course" type="text" value="" autocomplete="none" placeholder="Course" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control input-sm" id="department" name="department" type="text" value="" required onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" placeholder="department" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control input-sm" id="email" name="email" type="text" value="" autocomplete="none" placeholder="email" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                            </div>
                        </div>

                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <select name="status" class="form-control my-2">
                                    <option hidden disable value="select ">Select status</option>
                                    <option value="Graduating">Graduating</option>
                                    <option value="Almuni">Almuni</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary btn-sm" name="save" type="submit"><span class="fa fa-save fw-fa"></span> Save</button>
                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>
    <script>
        $(document).ready(function() {
            $(".export-btn").click(function() {
                var selectedOption = $("#export_filter").val();
                window.location.href = "export_students.php?option=" + selectedOption;
            });

            // Disable export button if no option selected in export_filter
            $("#export_filter").change(function() {
                if ($(this).val() === "") {
                    $(".export-btn").prop("disabled", true);
                } else {
                    $(".export-btn").prop("disabled", false);
                }
            });
        });

        document.getElementById('sections').addEventListener('change', function() {
            var selectedSection = this.value;
            filterTable(selectedSection);
        });

        function filterTable(section) {
            var table = document.getElementById('studentTable');
            var tr = table.getElementsByTagName('tr');

            for (var i = 0; i < tr.length; i++) {
                var td = tr[i].getElementsByTagName('td')[6];
                if (td) {
                    var txtValue = td.textContent || td.innerText;
                    if (section === "All Sections" || txtValue.indexOf(section) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        // Function to filter table based on search input
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#studentTable tbody tr').filter(function() {
                var rowText = $(this).text().toLowerCase();
                var isVisible = rowText.indexOf(value) > -1;
                $(this).toggle(isVisible);
            });

            // Check if any rows are visible after filtering
            var visibleRows = $('#studentTable tbody tr:visible').length;
            if (visibleRows === 0) {
                $('#noResult').show(); // Display "No Results" message
            } else {
                $('#noResult').hide(); // Hide "No Results" message if there are visible rows
            }
        });
    </script>
</body>

</html>