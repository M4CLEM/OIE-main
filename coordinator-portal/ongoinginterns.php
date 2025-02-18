<?php
session_start();
include_once("../includes/connection.php");
$query="select * from studentinfo where status = 'Deployed'";
$result=mysqli_query($connect,$query);
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
            <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">On going Interns - <?php echo $_SESSION['dept_cood']; ?></h4>

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
                    <div class="card-body">
                        <style>
                            .table td, .table th {
                                font-size:  12px;
                            }
                        </style>
                        <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0"> 
                            <thead>
                                <tr>
                                    <th scope="col">Student ID</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Course & Sec</th>
                                    <th scope="col">Status</th>
                                    <!--<th width="15%" align="center">Action</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                while($rows=mysqli_fetch_assoc($result))
                                {
                            ?>
                            
                                <tr>
                                    <td><?php echo $rows['studentID'];?></td>
                                    <td><?php echo $rows['firstname'];?> <?php echo $rows['lastname'];?></td>
                                    <td><?php echo $rows['course'];?> <?php echo $rows['section'];?></td>
                                    <td><?php echo $rows['status'];?></td>
                                    
                                    <!--<td> 
                                        <a href= "done-view.php?id=<?php //echo $rows['studentID']; ?>" class="btn btn-info btn-sm"><span> View details</span>
                                    </td>-->
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
    </div>

    

        <!-- add Modal-->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="student-add.php" method="POST">
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
                  </div>
                <div class="modal-footer">
                    <button class="btn btn-primary btn-sm" name="save" type="submit" ><span class="fa fa-save fw-fa"></span> Save</button>
                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </form>
    <!-- End add modal-->
            <!-- End of Main Content -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>
</body>
</html>	