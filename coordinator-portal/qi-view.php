<?php
session_start();
include_once("../includes/connection.php");
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
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Qualified Interns</h4>

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
               
            <!-- Begin Page Content -->            
                <div class="col-lg-12 mb-4">
                    <!-- Illustrations -->	
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <form action="deploy-insert.php" method="POST">
                                <h4 class="m-0 font-weight-bold text-dark">Details</h4> 
                        </div>
                            
                        <?php
                            $query = "SELECT * FROM studentinfo WHERE studentID =".$_GET['id'];
                            $result = mysqli_query($connect, $query) ;
                            while($rows = mysqli_fetch_array($result))
                            {   
                                            
                                $studentID = $rows['studentID'];
                                $firstname = $rows['firstname']; 
                                $lastname = $rows['lastname'];
                                $department = $rows['department'];
                                $course = $rows['course'];
                                $companyName = $rows['company'];
          
                            }
                                            
                            $studentID = $_GET['id'];   
                        ?> 

                        <div class="card-body"> 
                            <div class="row">

                                <div class="form-group col-lg-5">  
                                    <div class="col-md-10">                    
                                        <label  for= "studentID">Student ID:</label>
                                        <input class="form-control" id="studentID" name="studentID" type="text" value="<?php echo $studentID;?>" autocomplete="none" readonly>
                                    </div>
                                </div>

                                <div class="form-group col-lg-5">
                                    <div class="col-md-10">
                                        <label for= "firstname">Firstname:</label>  
                                        <input class="form-control input-sm" id="firstname" name="firstname" type="text" value="<?php echo $firstname;?>" autocomplete="none"readonly>
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="form-group col-lg-5">
                                    <div class="col-md-10">
                                        <label for= "lastname">Lastname:</label>  
                                        <input class="form-control input-sm" id="lastname" name="lastname" type="text" value="<?php echo $lastname;?>" autocomplete="none"readonly>
                                    </div>
                                </div>

                                <div class="form-group col-lg-5 ">
                                    <div class="col-md-10">
                                        <label for="department">Department:</label> 
                                        <input class="form-control input-sm" id="department" name="department" type="text" value="<?php echo $department;?>" required  onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off"readonly></input>
                                    </div>
                                </div>

                                <div class="form-group col-lg-5">
                                    <div class="col-md-10">
                                        <label for="course">Course:</label>
                                        <input class="form-control input-sm" id="course" name="course"  type="text" value="<?php echo $course;?>" autocomplete="none"readonly>
                                    </div>
                                </div>

                                <div class="form-group col-lg-5">
                                    <div class="col-md-10">
                                        <label for= "companyName">companyName:</label>                     
                                        <input class="form-control input-sm mb-4" id="companyName" name="companyName" type="text" value="<?php echo $companyName;?>" value="" autocomplete="none"readonly>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-8">
                                        <label class="col-md-4 control-label" for="idno"></label>  
                                    <div class="col-md-8">
                                    
                                    <button class="btn btn-primary btn-sm" name="save" type="submit" ></span> Deploy</button>

                                        <!-- <a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#deployModal  ">Deploy</a> -->
                                </div>
                            </div> 
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

</body>
</html>	