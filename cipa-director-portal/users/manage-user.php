	<?php
session_start();
include_once("../includes/connection.php");
$query="select * from users";
$result=mysqli_query($connect,$query);
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CIPA ADMIN</title>

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
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Department:</h6>
                        <a class="collapse-item" href="CBA.php">CBA</a>
                        <a class="collapse-item" href="CTE.php">CTE</a>
                        <a class="collapse-item" href="CCJ.php">CCJ</a>
                        <a class="collapse-item" href="CAS.php">CAS</a>
                        <a class="collapse-item" href="CITCS.php">CITCS</a>
                        <a class="collapse-item" href="ALUMNI.php">ALUMNI</a>
                    </div>
                </div>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item active ">
                <a class="nav-link" href="manage-user.php">
                 <i class="fa fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    <span>Manage User</span>
                </a>
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

                    <!-- Topbar -->
                    <ul class="navbar-nav ml-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle"  id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php (isset($_SESSION['admin'])) ?> <?php echo $_SESSION['admin']; ?></span>
                                <img class="img-profile rounded-circle"
                                    src="../img/undraw_profile.svg">
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
                        <div class="col-lg-12 mb-4">

                            <!-- Illustrations -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                   <a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal  ">  <i class="fa fa-plus-circle fw-fa"></i> Add new user</a> 
                                </div>
                                <div class="card-body">
                           		 <div class="table-responsive">
                           		 	<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th scope="col">Username</th>
                                                <th scope="col">Role</th>
                                                <th width="11%" align="center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        	<?php 
                                        	while($rows=mysqli_fetch_assoc($result))
                                        	{
                                        ?>
                                        	<tr>
	                                                <td><?php echo $rows['username'];?></td>
	                                                <td><?php echo $rows['role'];?></td>
	                                                <td> 
	                                                	<a title="Edit" href="user-edit.php?id=<?php echo $rows['id']; ?>" class="btn btn-xs"><span class="fa fa-edit fw-fa"></span>
                                                        <a title="Delete" href="functions/user-delete-process.php?id=<?php echo $rows['id']; ?>" class="btn btn-xs"><span class="fa fa-trash"></a></span>
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

            </div>
         <!-- add Modal-->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="functions/user-add.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                     <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <div class="col-md-10">
                         <input class="form-control input-sm" id="username" name="username" type="text" value="" autocomplete="none" placeholder="Enter Username">
                      </div>
                  </div>
                      <div class="form-group">
                      <div class="col-md-10">
                         <input class="form-control input-sm" id="password" name="password" type="password" value="" autocomplete="none" placeholder="Enter Password">
                      </div>
                  </div>
                    <div class="form-group">
                    <div class="col-md-10">  
                        <select name="role" class="form-control my-2">
                            <option hidden disable value="select ">Select Usertype</option>
                            <option value="adviser">Adviser</option>
                            <option value = "admin">Admin</option>
                            <option value = "coordinator">Coordinator</option>
                            <option value = "IndustryPartner">IndustryPartner</option>
                            <option value="Student">Student</option>    
                        </select>
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