<?php
    session_start();
    include_once("../includes/connection.php");

    $query="select * from department_list";
    $result=mysqli_query($connect,$query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("../elements/meta.php"); ?>
    <title>CIPA ADMIN</title>
    <?php include("embed.php"); ?>
    <link rel="stylesheet" href="../assets/css/new-style.css">
</head>


<body>

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar Wrapper -->
        <aside id="sidebar" class="expand">
            <?php include('../elements/cipa_sidebar.php')?>
        </aside>

        <div class="main">

            <!-- Content Wrapper -->
            <div id="content-wrapper" class="d-flex flex-column">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar static-top shadow mb-3">
                <!-- Dashboard Title -->
                <h2 class="my-0 mr-auto font-weight-bold text-dark ml-3">Manage Colleges</h2>
                <?php include('../elements/cipa_navbar_user_info.php') ?>
            </nav>
            <!-- End of Topbar -->

            <!-- Main Content -->
            <div id="content">
            
                <!-- Begin Page Content -->
                <div class="col-lg-12 mb-4">

                    <!-- Company -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between">
                            <h3 class="m-0 font-weight-bold text-dark">List of Departments</h3> 
                            <a href="modal.php" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">  <i class="fa fa-plus-circle fw-fa"></i> Add Department</a> 
                        </div>

                        <!-- Jobs Start -->
                        <div class="container-xxl py-4">
                            <div class="container">
                                <div class="tab-class text-center wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="tab-content">
                                        <div id="tab-1" class="tab-pane fade show p-0 active">

                                            <?php while($rows=mysqli_fetch_assoc($result)){?>

                                                <div class="job-item p-4 mb-4">
                                                    <div class="row g-4">
                                                        <div class="col-sm-12 col-md-8 d-flex align-items-center">
                                                            <div class="text-start ps-4">
                                                                <h4 class="mb-3"><?php echo $rows['department_title'];?> - <?php echo $rows['department'];?></h4>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12 col-md-4 d-flex flex-column align-items-start align-items-md-end justify-content-center">
                                                            <div class="d-flex mb-3">
                                                                <a class="btn btn-primary mr-3" href='view-department.php?dept=<?php echo $rows["department"]; ?>'>View</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            <?php 
                                                }
                                            ?> 	

                                        </div>
                                    </div>
                                </div>
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
                <form action="functions/department-add-process.php?>" method="POST">

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Department</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                        <div class="col-md-10">
                            <label for= "deptTitle">Department Title:</label>  
                            <input class="form-control input-sm" id="deptTitle" name="deptTitle" type="text" value="" autocomplete="none">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-10">
                            <label for="deptAcr">Department Acronym:</label> 
                            <input class="form-control input-sm" id="deptAcr" name="deptAcr" type="text" value="" required  autocomplete="none"></input>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary btn-sm" name="save" type="submit" ><span class="fa fa-save fw-fa"></span> Save</button>
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>
   
</body>
