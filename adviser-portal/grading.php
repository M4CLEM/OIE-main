<?php
    session_start();
    include("../includes/connection.php");

    $email = $_SESSION['adviser'];
    $query = "SELECT * FROM listadviser WHERE email ='$email'";
    $result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>Adviser Portal</title>
        <?php include("embed.php"); ?>
    </head>
    <body id="page-top">
        <div class="wrapper">
            <aside id="sidebar" class="expand">
                <?php include('../elements/adv_sidebar.php') ?>
            </aside>
            <div class="main">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">

                    <!-- Title -->
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Grading</h4>

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
                                <a class="dropdown-item" href="../logout.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <div class="row m-1">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold text-dark">Student Grades</h6>
                                <ul class="nav nav-pills flex-column flex-sm-row" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active btn-sm" id="pills-section-tab" data-toggle="pill" href="#section-tab" role="tab" aria-controls="section-tab" aria-selected="true">Handled Sections</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link btn-sm" id="pills-students-tab" data-toggle="pill" href="#students-tab" role="tab" aria-controls="students-tab" aria-selected="false">Students</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="tab-content" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="section-tab" role="tabpanel" aria-labelledby="section-tab">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th scope="col" class="small">Section</th>
                                                        <th width="11%" align="center" class="small">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    while ($rows = mysqli_fetch_assoc($result)) {
                                                    ?>
                                                        <tr>
                                                            <td><a href="#" class="section-link" data-section="<?php echo $rows['section']; ?>" data-course="<?php echo $rows['course'];?>"><?php echo $rows['course'];?> <?php echo $rows['section']; ?></a></td>
                                                            <td>
                                                                <a title="Edit" href="" class="btn btn-xs"><span class="fa fa-edit fw-fa"></span></a>
                                                                <a title="Delete" href="" class="btn btn-xs"><span class="fa fa-trash"></span></a>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="students-tab" role="tabpanel" aria-labelledby="students-tab">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover" id="students-table" width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th scope="col" class="small"></th>
                                                        <th scope="col" class="small">STUDENT NUMBER</th>
                                                        <th scope="col" class="small">NAME</th>
                                                        <th scope="col" class="small">YEAR LEVEL</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="4" class="text-center small">Loaded Masterlists will appear here</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4 p-lg-0">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-dark">STUDENT INFORMATION</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <div>
                                            <label for="stud_id" class="small">Student No:</label>
                                            <p class="small font-weight-bold" id="stud_id"></p>
                                        </div>
                                        <div>
                                            <label for="surname" class="small">Surname:</label>
                                            <p class="small font-weight-bold" id="surname"></p>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div>
                                            <label for="section" class="small">Section:</label>
                                            <p class="small font-weight-bold" id="section"></p>
                                        </div>
                                        <div>
                                            <label for="firstName" class="small">First Name:</label>
                                            <p class="small font-weight-bold" id="firstName"></p>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div>
                                            <label for="program" class="small">Program:</label>
                                            <p class="small font-weight-bold" id="program"></p>
                                        </div>
                                        <div>
                                            <label for="midName" class="small">Middle Name:</label>
                                            <p class="small font-weight-bold" id="midName"></p>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <form action="" id="criteriaForm" method="POST">
                                        <div class="row">
                                            <label for="criteriaContainer" class="text-center small font-weight-bold border-0">Grading</label>
                                        </div>
                                        <div id="criteriaContainer">
                                            <p class="text-center p-4">Criteria will be displayed here</p>
                                        </div>
                                        <div class="d-flex justify-content-end">
                                            <button type="submit" class="btn btn-success">
                                                <span class="fas fa-save fw-fa"></span> Submit Grade
                                            </button>
                                            <div class="col-4">
                                                <label class="sr-only" for="totalGrade">Total Grade</label>
                                                <div class="input-group mb-2 mr-sm-2">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">Total</div>
                                                    </div>
                                                    <input type="number" id="totalGrade" name="totalGrade" class="form-control" min="0" max="100" oninput="distributeTotalGrade()" required>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
        <script src="../assets/js/sidebarscript.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

        <script>
            $(document).ready(function() {
                $('.section-link').click(function(e) {
                    e.preventDefault();
                    var section = $(this).data('section');
                    var course = $(this).data('course');

                    $.ajax({
                        url: 'functions/fetch_masterlist.php', // Provide the path to your PHP script that fetches student data
                        method: 'POST',
                        data: {
                            section: section,
                            course: course
                        },
                        success: function(response) {
                            // Destroy existing DataTable
                            if ($.fn.DataTable.isDataTable('#students-table')) {
                                $('#students-table').DataTable().clear().destroy();
                            }

                            // Clear table body
                            $('#students-table tbody').empty();

                            // After successfully loading data, call the loadMasterList function
                            loadMasterList(section, course, response);
                        }
                    });
                });

                function loadMasterList(section, course, response) {
                    // Show SweetAlert2 alert with loading animation
                    Swal.fire({
                        title: 'Please Wait...',
                        showConfirmButton: false,
                        position: 'top',
                        toast: true,
                        willOpen: () => {
                            Swal.showLoading();
                        },
                        didOpen: () => {
                            setTimeout(() => {
                                Swal.fire({
                                    icon: 'success',
                                    title: course + ' ' + section + ' Masterlist Loaded',
                                    position: 'top',
                                    toast: true,
                                    showConfirmButton: false,
                                    timer: 2000
                                });

                                // Replace table data with new response
                                $('#students-table tbody').html(response);

                                // Check if DataTables has already been initialized on the element
                                if ($.fn.DataTable.isDataTable('#students-table')) {
                                    // If DataTables is already initialized, clear and destroy it
                                    $('#students-table').DataTable().clear().destroy();
                                }

                                // Delay the initialization of DataTables to ensure the table is fully rendered
                                setTimeout(function() {
                                    // Check if the table body has rows
                                    if ($('#students-table tbody tr').length > 0) {
                                        // If there are rows, reinitialize DataTables
                                        $('#students-table').DataTable();
                                    }
                                }, 100); // Delay of 100 milliseconds
                            }, 2000); // Simulated loading delay of 2 seconds
                        }
                    });
                }


            })
        </script>
    </body>
</html>