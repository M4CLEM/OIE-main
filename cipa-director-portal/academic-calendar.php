<?php
    session_start();
    include_once("../includes/connection.php");

    $result = mysqli_query($connect, "SELECT * FROM academic_year");
    if (!$result) {
        die("Query Failed: " . mysqli_error($connect));
    }

    while ($rows = mysqli_fetch_assoc($result)) {
        $id = $rows['id'];
        $startDate = $rows['start_date'];
        $endDate = $rows['end_date'];
        $semester = $rows['semester'];
        $schoolYear = $rows['schoolYear'];
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>CIPA ADMIN</title>
        <?php include("embed.php"); ?>
    </head>
    <body id="page-top">
        <div class="wrapper">
            <aside id="sidebar" class="expand">
                <?php include('../elements/cipa_sidebar.php') ?>
            </aside>
            <div class="main">
                <nav class="navbar navbar-expand navbar-light bg-white topbar static-top shadow mb-3">
                    <h2 class="my-0 mr-auto font-weight-bold text-dark ml-3">Academic Calendar</h2>
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo $_SESSION['CIPA']; ?></span>
                                <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
                            </a>
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
                                <a class="dropdown-item" href="../logout.php" data-toggle="logout" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <div id="content" class="py-2">
                    <div class="row">
                        <div class="ms-3">
                            <a href="management-acc.php" class="btn btn-primary"><i class="fa fa-arrow-circle-left"></i></a>
                        </div>
                        <h1 class="text-center">Current Timeline</h1>
                    </div>
                    <div class="col-lg-12">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal"> <i class="fa fa-plus-circle fw-fa"></i> Add Calendar </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th scope="col">Starting Date</th>
                                                <th scope="col">Ending Date</th>
                                                <th scope="col">Semester</th>
                                                <th scope="col">School Year</th>
                                                <th scope="col" width="14%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><?php echo $startDate ?></td>
                                                <td><?php echo $endDate ?></td>
                                                <td><?php echo $semester ?></td>
                                                <td><?php echo $schoolYear ?></td>
                                                <td>
                                                    <a href="modal.php" class="btn btn-primary btn-sm editBtn" data-toggle="modal"
                                                        data-target="#editModal" data-id="<?php echo $id ?>"
                                                        data-start-date="<?php echo  $startDate ?>"
                                                        data-end-date="<?php echo $endDate ?>" data-semester="<?php echo $semester ?>" data-schoolYear="<?php echo $schoolYear ?>"><i class="fa fa-edit fw-fa"></i>Edit</a>
                                                    <button type="button" class="btn btn-danger btn-sm deleteBtn" data-toggle="modal"
                                                        data-target="#deleteModal" data-id="<?php echo $rows['id']; ?>">
                                                        <i class="fa fa-trash fw-fa"></i> Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ADD MODAL -->
                <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="functions/add_academic_year.php" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addManagement">Add Academic Year</h5>
                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="row align-items-center">
                                            <div class="col-md-4 text-right">
                                                <label for="startingDate">Starting Date</label>
                                            </div>
                                            <div class="col-md-4 text-center"></div>
                                            <div class="col-md-4 text-left">
                                                <label for="endingDate">Ending Date</label>
                                            </div>
                                        </div>
                                        <div class="row align-items-center mt-2">
                                            <div class="col-md-5">
                                                <input type="date" id="startingDate" name="startingDate" class="form-control">
                                            </div>
                                            <div class="col-md-2 text-center">To</div>
                                            <div class="col-md-5">
                                                <input type="date" id="endingDate" name="endingDate" class="form-control">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <div class="row">
                                                <label for="semester">Semester</label>
                                                <select class="form-control" name="semester" id="semester" required>
                                                    <option value="">Select Semester</option>
                                                    <option value="1st Semester">1st Semester</option>
                                                    <option value="2nd Semester">2nd Semester</option>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <label for="schoolYear">School Year</label>
                                                <input type="text" name="schoolYear" id="schoolYear" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary btn-sm" type="submit" id="submitBtn">
                                        <span class="fa fa-save fw-fa"></span> Submit
                                    </button>
                                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- EDIT MODAL -->
                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="" method="POST">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Academic Year</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group">
                                        <div class="row align-items-center">
                                            <div class="col-md-4 text-right">
                                                <label for="startingDate">Starting Date</label>
                                            </div>
                                            <div class="col-md-4 text-center"></div>
                                            <div class="col-md-4 text-left">
                                                <label for="endingDate">Ending Date</label>
                                            </div>
                                        </div>
                                        <div class="row align-items-center mt-2">
                                            <div class="col-md-5">
                                                <input type="date" id="editStartingDate" name="editStartingDate" class="form-control">
                                            </div>
                                            <div class="col-md-2 text-center">To</div>
                                            <div class="col-md-5">
                                                <input type="date" id="editEndingDate" name="editEndingDate" class="form-control">
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="form-group">
                                            <div class="row">
                                                <label for="semester">Semester</label>
                                                <select class="form-control" name="editSemester" id="editSemester" required>
                                                    <option value="">Select Semester</option>
                                                    <option value="1st Semester">1st Semester</option>
                                                    <option value="2nd Semester">2nd Semester</option>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <label for="schoolYear">School Year</label>
                                                <input type="text" name="editSchoolYear" id="editSchoolYear" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary btn-sm" name="save" type="submit"><span class="fa fa-save fw-fa"></span> Save</button>
                                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- LOG OUT MODAL-->
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

            </div>
        </div>
    </body>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>

    <script>
        
    </script>
</html>