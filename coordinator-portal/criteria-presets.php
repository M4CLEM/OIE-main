<?php
session_start();

include("../includes/connection.php");

$department = $_SESSION['department'];
$coordinatorRole = $_SESSION['coordinator'];

$result = mysqli_query($connect, "SELECT * FROM criteria_presets WHERE department = '$department'");
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
    <link rel="stylesheet" href="../assets/css/new-style.css">

    <style>
        .addBtn {
            display: inline-flex;
            justify-content: flex-end;
            margin-left: 53rem;
        }

        .table td,
        .table th {
            font-size: 12px;
        }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <aside id="sidebar" class="expand">
            <?php include('../elements/cood_sidebar.php'); ?>
        </aside>

        <div class="main">
            <!-- Navigation Bar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Grading</h4>
                <ul class="navbar-nav ml-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php echo $coordinatorRole; ?></span>
                            <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#">Profile</a>
                            <a class="dropdown-item" href="#">Settings</a>
                            <a class="dropdown-item" href="#">Activity Log</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="../logout.php">Logout</a>
                        </div>
                    </li>
                </ul>
            </nav>

            <div id="content" class="py-2">
                <div class="col-lg-13 m-3">
                    <div class="card shadow mb-4">
                        <div class="card-header py-2">
                            <div class="col">
                                <label for="criteria-presets" style="font-size: 26px;">Criteria Presets</label>
                                <a href="modal.php" class="btn btn-primary btn-sm addBtn" data-toggle="modal"
                                    data-target="#addModal">+Add Criteria</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th width="20%" scope="col">Criteria Title</th>
                                            <th scope="col">Desctription</th>
                                            <th width="15%" scope="col">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        while ($rows = mysqli_fetch_assoc($result)) {
                                        ?>
                                            <tr>
                                                <td><?php echo $rows['criteria']; ?></td>
                                                <td><?php echo $rows['description']; ?></td>
                                                <td>
                                                    <a href="modal.php" class="btn btn-primary btn-sm editBtn" data-toggle="modal"
                                                        data-target="#editModal" data-id="<?php echo $rows['id']; ?>"
                                                        data-title="<?php echo $rows['criteria']; ?>"
                                                        data-description="<?php echo $rows['description']; ?>"><i class="fa fa-edit fw-fa"></i>Edit</a>

                                                    <button type="button" class="btn btn-danger btn-sm deleteBtn" data-toggle="modal"
                                                        data-target="#deleteModal" data-id="<?php echo $rows['id']; ?>">
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

                            <!-- DELETE CONFIRMATION MODAL -->
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
                                            Are you sure you want to delete this criteria?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DELETE LOADING MODAL -->
                            <div class="modal fade" id="deleteLoadingModal" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content d-flex flex-column align-items-center justify-content-center p-4">
                                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                            <span class="sr-only">Deleting...</span>
                                        </div>
                                        <p class="mt-3">Deleting criteria, please wait...</p>
                                    </div>
                                </div>
                            </div>

                            <!-- SUCCESS LOADING MODAL -->
                            <div class="modal fade" id="successLoadingModal" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content text-center p-4">
                                        <h5 class="text-success">Success!</h5>
                                        <p>The criteria has been deleted successfully.</p>
                                        <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form id="editForm" method="post" action="functions/criteria-edit.php">
                                            <div class="modal-body">
                                                <h5>Edit Criteria</h5>
                                                <input type="hidden" id="editId" name="id">
                                                <div class="form-group">
                                                    <div class="col-md-12 mt-3">
                                                        <label for="editTitle">Criteria Title:</label>
                                                        <input class="form-control input-sm" id="editTitle" name="editTitle" type="text" value="" autocomplete="none">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-12">
                                                        <label for="editDescription">Desctription:</label>
                                                        <textarea class="form-control" name="editDescription" id="editDescription"></textarea>
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
                            <!-- Add Criteria Modal -->
                            <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form id="criteriaForm" method="post" action="functions/criteria-submit.php">
                                            <div class="modal-body">
                                                <h5>Add Criteria</h5>
                                                <div class="form-group">
                                                    <div class="col-md-12 mt-3">
                                                        <label for="addTitle">Criteria Title:</label>
                                                        <input class="form-control input-sm" id="addTitle" name="addTitle" type="text" value="" autocomplete="none">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-12">
                                                        <label for="addDescription">Desctription:</label>
                                                        <textarea class="form-control" name="addDescription" id="addDescription"></textarea>
                                                    </div>
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
        $('.editBtn').click(function() {
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

            // Hide delete modal, show delete loading modal
            $('#deleteModal').modal('hide');
            $('#deleteLoadingModal').modal('show');

            $.ajax({
                url: 'functions/criteria-delete.php',
                type: 'POST',
                data: {
                    id: id
                },
                success: function(response) {
                    // Hide delete loading modal, show success loading modal
                    $('#deleteLoadingModal').modal('hide');
                    $('#successLoadingModal').modal('show');

                    // Reload the page after a short delay (optional)
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                },
                error: function(xhr, status, error) {
                    $('#deleteLoadingModal').modal('hide'); // Hide loading modal
                    alert('An error occurred: ' + error);
                }
            });
        });
    });
</script>

</html>