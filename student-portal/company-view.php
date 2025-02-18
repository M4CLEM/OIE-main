<?php
session_start();
include_once("../includes/connection.php");
$query="select * from companylist";
$result=mysqli_query($connect,$query);
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <?php include("../elements/meta.php"); ?>
    <title>Intern Portal</title>
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

            <!-- Begin Page Content -->            
            <div class="col-lg-12 mb-4">
                <!-- Illustrations -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h4 class="m-0 font-weight-bold text-dark">Company Details 
                        <a href="company.php" class="close" type="" aria-label="Close"> <span aria-hidden="true">×</span></h4>
                        </a> 
                    </div>
                    <div class="card-body">   
                    <?php
                            $query = "SELECT * FROM companylist WHERE No =".$_GET['id'];
                            $result = mysqli_query($connect, $query) or die(mysqli_error($connect));
                            while($rows = mysqli_fetch_array($result))
                            {   
                                $No = $rows['No'];            
                                $companyaddress = $rows['companyaddress'];
                                $jobdescription = $rows['jobdescription'];
                                $jobreq = $rows['jobreq'];
                                $link = $rows['link'];
                                            
                            }
                                            
                            $No = $_GET['id'];
                                        
                    ?> 
                    <div class="col-md-10">                    
                        <input type="hidden" name="No" value="<?php echo $No; ?>">
                            <dl class="row">
                            <dt class="col-sm-4">companyName : </dt>
                            <dd class="col-sm-8"><?php echo$companyaddress ?></dd>
                        </dl>
                            <dl class="row">
                            <dt class="col-sm-4">Job Description:  </dt>
                            <dd class="col-sm-8"> <?php echo $jobdescription;?> </dd>
                        </dl>
                    
                            <dl class="row">
                            <dt class="col-sm-4">Requirements/Qualification: </dt>
                            <dd class="col-sm-8"> <?php echo $jobreq;?> </dd>
                        </dl>

                        <dl class="row">
                            <dt class="col-sm-4">Link: </dt>
                            <dd class="col-sm-8"> <a href="<?php echo $link;?>"><?php echo $link;?></a></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Main Content -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
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