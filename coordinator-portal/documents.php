<?php
    session_start();
    include("../includes/connection.php");

    $department = $_SESSION['department'];
    $coordinatorRole = $_SESSION['coordinator'];

    $result = mysqli_query($connect, "SELECT * FROM documents_list WHERE department = '$department'");
    if (!$result) {
        die("Query Failed: " . mysqli_error($connect));
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>OJT COORDINATOR PORTAL</title>
        <?php include("embed.php"); ?>

        <style>
            .addBtn {
                display: inline-flex;
                justify-content: flex-end;
                margin-left: 53rem;
            }
            .table td, .table th {
                font-size: 12px;
            }
            .typeDropdown {
                margin-bottom: 10px;
            }
        </style>
    </head>
    <body id="page-top">
        <!-- PAGE WRAPPER -->
        <div id="wrapper">
            <!-- SIDEBAR WRAPPER -->
            <aside id="sidebar" class="expand">
                <?php include('../elements/cood_sidebar.php') ?>
            </aside>

            <div class="main">
            <!-- NAVIGATION BAR -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Documents</h4>
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo $_SESSION['coordinator']; ?>
                                </span>
                                <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#"><i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profile</a>
                                <a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i> Settings</a>
                                <a class="dropdown-item" href="#"><i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i> Activity Log</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout</a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <div id="content" class="py-2">
                    <div class="col-lg-13 m-3">
                        <div class="card shadow mb-4">
                            <div class="card-header py-2">
                                <div class="col">
                                    <label for="document list" style="font-size:26px;">Document</label>
                                    <a href="modal.php" class="btn btn-primary btn-sm addBtn"  data-toggle="modal" data-target="#addModal">+Add Documents</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th width="20%" scope="col">Document Name</th>
                                                <th width="20%" scope="col">Document Type</th>
                                                <th width="35%" scope="col">Template</th>
                                                <th width="22%" scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                while ($rows = mysqli_fetch_assoc($result)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $rows['documentName'];?></td>
                                                <td><?php echo $rows['documentType'];?></td>
                                                <td>
                                                    
                                                </td>
                                                <td>
                                                    <a href="modal.php" class="btn btn-primary btn-sm editBtn" data-toggle="modal" data-target="#editModal" 
                                                    data-id="<?php echo $rows['id'];?>" 
                                                    data-documentName="<?php echo $rows['documentName'];?>"
                                                    data-documentType="<?php echo $rows['documentType'];?>"><i class="fa fa-edit fw-fa"></i>Edit</a>

                                                    <button type="button" class="btn btn-danger btn-sm deleteBtn" data-toggle="modal" data-target="$deleteModal" data-id="<?php echo $rows['id'];?>">
                                                        <i class="fa fa-trash fw-fa"></i>Delete
                                                    </button>
                                                    
                                                    <!-- FILE UPLOAD -->
                                                    <button class="btn btn-success btn-sm" onclick="document.getElementById('fileInput_').click();">
                                                        <i class="fa fa-upload"></i>Update File
                                                        <input type="file" id="fileInput_" style="display: none;" accept=".docx,.pdf" onchange="">
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                            <!-- Modal for edit and delete -->
                                            <!-- Delete Modal -->
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
                                                Are you sure to delete this row?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                            <!--Edit Modal-->
                                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="functions/document-edit.php" id="editForm" method="post">
                                                <div class="modal-body">
                                                    <h5>Edit Document Info</h5>
                                                    <input type="hidden" id="editId" name="id">
                                                    <div class="form-group">
                                                        <div class="col-md-12 mt-3">
                                                            <label for="editDocumentName">Document Name</label>
                                                            <input type="text" class="form-control input-sm" id="editDocumentName" name="editDocumentName" value="" autocomplete="none">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label for="editDocumentType">Document Type</label>
                                                            <input class="form-control input-sm" type="text" name="editDocumentType" id="editDocumentType" value="" autocomplete="none">
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
                                            <!-- Add Document Modal -->
                                <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="" method="post" id="documentForm">
                                                <div class="modal-body">
                                                    <h5>Add Document</h5>
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <label for="addDocumentName">Document Name:</label>
                                                            <input class="form-control input-sm" id="addDocumentName" type="text" value="" autocomplete="none">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label for="addDocumentType">Document Type:</label>
                                                            <select class="col-md-12 typeDropdown" name="documentTypeDropdown" id="documentTypeDropdown">
                                                                <option value="">Select Document Type</option>
                                                            </select>
                                                            <input class="form-control input-sm" id="addDocumentType" placeholder="If Document type is not available, enter here..." type="text" value="" autocomplete="none">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="uploadTemplate">Upload Template</label>
                                                        <input type="file" class="form-control-file" id="newFile" name="newFile" accept=".docx,.pdf">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button class="btn btn-primary btn-sm" name="submit" type="submit"><span class="fa fa-save fw-fa"></span> Submit</button>
                                                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>

    <script>
        $(document).ready(function() {
            $('.editBtn').click(function () {
                var id = $(this).data('id');
                var title = $(this).data('title');
                var description = $(this).data('description');

                $('#editTitle').val(title);
                $('#editDescription').val(description);
                $('#editId').val(id); // Set hidden input value
            });

            $('.deleteBtn').click(function() {
                var id = $(this).data('id');
                $('#confirmDelete').data('id', id);
            });

            $('#confirmDelete').click(function() {
                var id = $(this).data('id');
                $.ajax({
                    url: 'functions/criteria-delete.php',
                    type: 'POST',
                    data: {id: id},
                    success: function(response) {
                        alert(response);
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        alert('An error occured:' + error);
                    }
                })
            });
        });
    </script>

</html>