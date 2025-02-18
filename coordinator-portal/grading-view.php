<?php
session_start();
include_once("../includes/connection.php");
$program = $_SESSION['program'];
$result=mysqli_query($connect,"SELECT * FROM criteria_list WHERE program = '$program'");
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
            <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Grading</h4>

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
                <div class="col-lg-13 m-3">

                    <div class="card shadow mb-4">

                        <div class="card-header py-3">
                            <a class="btn btn-primary btn-sm" href="grading.php" style="font-size: 13px;">+ Add Criteria</a> 
                        </div>
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
                                            <th width="20%" scope="col">Criteria Title</th>
                                            <th scope="col">Description</th>
                                            <th width="15%" scope="col">Percentage</th>
                                            <th width="22%" scope="col">Action</th>

                                        </tr>
                                    </thead>

                                    <tbody>

                                        <?php 
                                            while($rows=mysqli_fetch_assoc($result))
                                            {
                                        ?>
                                            <tr>
                                                <td><?php echo $rows['criteria'];?></td>
                                                <td><?php echo $rows['description'];?></td>
                                                <td><?php echo $rows['percentage'];?>%</td>
                                                <td>
                                                    <a href="modal.php" class="btn btn-primary btn-sm editBtn" data-toggle="modal" 
                                                    data-target="#editModal"data-id="<?php echo $rows['id'];?>" 
                                                    data-title="<?php echo $rows['criteria'];?>" 
                                                    data-description="<?php echo $rows['description'];?>" 
                                                    data-percentage="<?php echo $rows['percentage'];?>"> 
                                                    <i class="fa fa-edit fw-fa"></i> Edit</a> 
                                                    <button type="button" class="btn btn-danger btn-sm deleteBtn" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $rows['id'];?>">
                                                        <i class="fa fa-trash fw-fa"></i> Delete
                                                    </button>
                                                    
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

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this row?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal-->
   <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="functions/grading-edit.php?>" method="POST">

                    <div class="modal-body">
                        <div class="form-group">
                        <div class="col-md-12 mt-3">
                            <label for= "editTitle">Criteria Title:</label>  
                            <input class="form-control input-sm" id="editTitle" name="editTitle" type="text" value="" autocomplete="none">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label for="editDescription">Description:</label> 
                            <textarea class="form-control" id="editDescription" name="editDescription"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12">
                            <label for="editPercentage">Percentage:</label>
                            <select class="form-control" id="editPercentage" name="editPercentage">
                                <?php for ($i = 5; $i <= 100; $i += 5): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?>%</option>
                                <?php endfor; ?>
                            </select>
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

    


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>

    <script>
    $(document).ready(function() {
        $('.editBtn').click(function() {
            var id = $(this).data('id');
            var title = $(this).data('title');
            var description = $(this).data('description');
            var percentage = $(this).data('percentage');

            $('#editTitle').val(title);
            $('#editDescription').val(description);
            $('#editPercentage').val(percentage);
            $('form').attr('action', 'functions/grading-edit.php?id=' + id);
        });

        $('.deleteBtn').click(function() {
            var id = $(this).data('id');
            $('#confirmDelete').data('id', id);
        });

        $('#confirmDelete').click(function() {
            var id = $(this).data('id');
            $.ajax({
                url: 'functions/grading-delete.php',
                type: 'POST',
                data: {id: id},
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error);
                }
            });
        });
    });
    </script>


</body>
</html>	