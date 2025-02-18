<?php
session_start();
include_once("../includes/connection.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CIPA PORTAL</title>

    <?php include("assets.php"); ?>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-dark sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="header.php">
                <div class="sidebar-brand-icon rotate-n-15">
                </div>
                <div class="sidebar-brand-text mx-3">Plmunian's OIE</div>
            </a>

             <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item ">
                <a class="nav-link" href="home.php">
                   <i class="fas fa-home fa-sm fa-fw mr-2 text-gray-400"></i>
                      <span>Home</span></a>

            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="student-list.php">
                   <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                      <span>List of Student Interns</span></a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item ">
                <a class="nav-link" href="company.php">
                   <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                      <span>List and Add Company</span></a>         

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

             <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
               <a class="nav-link collapsed" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                 <i class="fa fa-building fa-sm fa-fw mr-2 text-gray-400"></i>
                    <span>Company by Colleges</span>
            <!-- Divider -->
            <hr class="sidebar-divider">

            <li class="nav-item">
                <a class="nav-link collapsed" href="Pstatus.php" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fa fa-star fa-bars fa-sm fa-fw mr-2 text-gray-400"></i>
                    <span>Performance Status</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Department:</h6>
                        <a class="collapse-item" href="utilities-color.html">CBA</a>
                        <a class="collapse-item" href="utilities-border.html">CTE</a>
                        <a class="collapse-item" href="utilities-animation.html">CCJ</a>
                        <a class="collapse-item" href="utilities-animation.html">CAS</a>
                        <a class="collapse-item" href="utilities-other.html">CITCS</a>
                        <a class="collapse-item" href="utilities-other.html">ALMUNI</a>
                    </div>
                </div>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                	<?php (isset($_SESSION['adviser'])) ?> <?php echo $_SESSION['adviser']; ?></span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                             <!-- Dropdown - User Information -->
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                  <div class="dropdown"></div>
                                <a class="dropdown-item" href="../logout.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->            
                        <div class="col-lg-10 mb-4">
                        <!-- Illustrations -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                	<form action="student-edit-process.php" method="POST">
                                    <h4 class="m-0 font-weight-bold text-dark">Edit</h4> 
                                </div>
                                <div class="card-body">   
                                <?php
                                        $query = "SELECT * FROM studentinfo WHERE studentID =".$_GET['studentID'];
                                        $result = mysqli_query($connect, $query) or die(mysqli_error($connect));
                                             while($rows = mysqli_fetch_array($result))
                                                      {   
                                                     
                                            $studentID = $rows['studentID'];
                                            $firstname = $rows['firstname'];
                                            $middlename = $rows['middlename'];
                                            $lastname = $rows['lastname'];
                                            $department = $rows['department'];
                                            $course = $rows['course'];
                                            $status = $rows['status'];

                                                     
                                                      }
                                                      
                                                      $studentID = $_GET['studentID'];
                                                 
                                        ?> 
                                <div class="row">
                                 <div class="form-group col-lg-5">  
                                  <div class="col-md-10">                    
                                  <label  for= "studentID">Student ID:</label>
                                 <input class="form-control" id="studentID" name="studentID" type="text" value="<?php echo $studentID;?>" autocomplete="none">
                                  </div>
                                </div>
                                <div class="form-group col-lg-5">
                                <div class="col-md-10">
                                  <label for= "firstname">Firstname:</label>  
                                     <input class="form-control input-sm" id="firstname" name="firstname" type="text" value="<?php echo $firstname;?>" autocomplete="none">
                                  </div>
                                </div>
                            </div> 
                            <div class="row">
                                 <div class="form-group col-lg-5">  
                                  <div class="col-md-10">                    
                                  <label  for= "middlename">Middlename:</label>
                                 <input class="form-control" id="middlename" name="middlename" type="text" value="<?php echo $middlename;?>" autocomplete="none">
                                  </div>
                                </div>
                                <div class="form-group col-lg-5">
                                <div class="col-md-10">
                                  <label for= "lastname">Lastname:</label>  
                                     <input class="form-control input-sm" id="lastname" name="lastname" type="text" value="<?php echo $lastname;?>" autocomplete="none">
                                  </div>
                                </div>
                            </div> 
                            <div class="row">
                              <div class="form-group col-lg-5 ">
                                <div class="col-md-10">
                                  <label for="department">Department:</label> 
                                    <input class="form-control input-sm" id="department" name="department" type="text" value="<?php echo $department;?>" required  onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off"></input>
                                  </div>
                                </div>
                              <div class="form-group col-lg-5">
                                <div class="col-md-10">
                                  <label for="course">Course:</label>
                                     <input class="form-control input-sm" id="course" name="course"  type="text" value="<?php echo $course;?>" autocomplete="none">
                                  </div>
                                </div>
                                <div class="row">
                                <div class="form-group col-lg-5">
                                <div class="col-md-10">
                                  <label for="status">Status:</label>
                                     <input class="form-control input-sm" id="status" name="status"  type="text" value="<?php echo $status;?>" autocomplete="none">
                                  </div>
                                </div>
                              <div class="form-group">
                                <div class="col-md-8">
                                  <label class="col-md-4 control-label" for="idno"></label>  
                                  <div class="col-md-8">
                                     <button class="btn btn-primary btn-sm" name="update" type="submit" ><span class="fa fa-save fw-fa"></span> Update</button>
                                  <a href="company.php" class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-arrow-left"></span>Back</a>
                                 
                                 </div>
                                </div>
                              </div> 
                      </form>
            <!-- End of Main Content -->
        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

</body>
</html>