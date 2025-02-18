<?php
    session_start();
    include_once("../includes/connection.php");
    $query = "SELECT * FROM users WHERE role ='IndustryPartner'";
    $result = mysqli_query($connect, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("../elements/meta.php"); ?>
    <title>CIPA ADMIN</title>
    <?php include("embed.php"); ?>
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

                <ul class="navbar-nav ml-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php echo $_SESSION['CIPA']; ?></span>
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

            <div id="content" class="py-2">
                <div class="col-lg-12">
                    <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal  "> <i class="fa fa-plus-circle fw-fa"></i> Add </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th scope="col">Company Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Password</th>
                                    <th width="10%" align="center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($rows = mysqli_fetch_assoc($result)) {
                                ?>
                                    <tr>
                                        <td><?php echo $rows['companyName']; ?></td>
                                        <td><?php echo $rows['username']; ?></td>
                                        <td><?php echo $rows['password']; ?></td>
                                        <td>
                                            <a title="Edit" href="advisers-edit.php?id=<?php echo $rows['id']; ?>" class="btn btn-xs"><span class="fa fa-edit fw-fa"></span>
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
                </div>
            </div>
        </div>
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addAdvisers" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="add_ip.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAdvisers">Add Company</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control" id="companyName" name="companyName" type="text" value="" autocomplete="none" placeholder="Company Name" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input class="form-control input-sm" id="email" name="email" type="text" value="" autocomplete="none" placeholder="Email" onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input type="password" class="form-control input-sm" id="password" name="password" type="text" value="" autocomplete="none" placeholder="Password" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="form-group md-5">
                            <div class="col-md-10">
                                <input type="password" class="form-control input-sm" id="confirm" name="confirm" type="text" value="" autocomplete="none" placeholder="Confirm Password" autocomplete="off" required>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>
  
</body>
