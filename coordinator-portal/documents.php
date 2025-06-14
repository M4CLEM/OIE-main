<?php
session_start();
include("../includes/connection.php");

$department = $_SESSION['department'];
$coordinatorRole = $_SESSION['coordinator'];

$result = mysqli_query($connect, "SELECT * FROM documents_list WHERE department = '$department'");
if (!$result) {
    die("Query Failed: " . mysqli_error($connect));
}

$documentType = mysqli_query($connect, "SELECT DISTINCT documentType FROM documents_list WHERE department = '$department'");
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
                <?php include('../elements/cood_navbar_user_info.php')?>
            </nav>

            <div id="content" class="py-2">
                <div class="col-lg-13 m-3">
                    <div class="card shadow mb-4">
                        <div class="card-header py-2">
                            <div class="col">
                                <label for="document list" style="font-size:26px;">Document</label>
                                <a href="modal.php" class="btn btn-primary btn-sm addBtn" data-toggle="modal" data-target="#addModal">+Add Documents</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th width="20%" scope="col">Document Name</th>
                                            <th width="20%" scope="col">Document Type</th>
                                            <th scope="col">Template</th>
                                            <th width="19%" scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($rows = mysqli_fetch_assoc($result)) {
                                            $file = $rows['file_name'];
                                            $doc_template = $rows['file_template'];
                                        ?>
                                            <tr>
                                                <td><?php echo $rows['documentName']; ?></td>
                                                <td><?php echo $rows['documentType']; ?></td>
                                                <td>
                                                    <?php echo $file; ?>
                                                </td>
                                                <td>

                                                    <!--    
                                                    <a href="modal.php" class="btn btn-primary btn-sm editBtn" data-toggle="modal" data-target="#editModal" 
                                                    data-id="<?php echo $rows['id']; ?>" 
                                                    data-documentName="<?php echo $rows['documentName']; ?>"
                                                    data-documentType="<?php echo $rows['documentType']; ?>"><i class="fa fa-edit fw-fa"></i>Edit</a>
                                                -->

                                                    <button type="button" class="btn btn-danger btn-sm deleteBtn" data-toggle="modal" data-target="#deleteModal" data-id="<?php echo $rows['id']; ?>">
                                                        <i class="fa fa-trash fw-fa"></i>Delete
                                                    </button>

                                                    <button class="btn btn-primary btn-sm" onclick="viewPDF('<?php echo $doc_template; ?>')"><i class="far fa-eye"></i>View</button>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Modal for edit and delete -->
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
                                            Are you sure you want to delete this document?
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
                                        <div class="spinner-border text-success my-3" role="status" style="width: 3rem; height: 3rem;">
                                            <span class="sr-only">Deleting...</span>
                                        </div>
                                        <p class="mt-3">Deleting document, please wait...</p>
                                    </div>
                                </div>
                            </div>

                            <!-- SUCCESS LOADING MODAL -->
                            <div class="modal fade" id="successLoadingModal" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content text-center p-4">
                                        <h5 class="text-success">Success!</h5>
                                        <p>The document has been deleted successfully.</p>
                                        <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
                                    </div>
                                </div>
                            </div>

                            <!--Edit Modal-->
                            <!-- 
                              <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="functions/document-edit.php" id="editForm" method="POST">
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
                                                    <div class="form-group">
                                                        <label for="uploadTemplate">Update Template</label>
                                                        <input type="file" class="form-control-file" id="updateFile" name="updateFile" accept=".docx,.pdf">
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
                                -->


                            <!-- Add Document Modal -->
                            <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="functions/add_docs.php" method="post" id="documentForm" enctype="multipart/form-data">
                                            <div class="modal-body">
                                                <h5>Add Document</h5>
                                                <div class="form-group">
                                                    <div class="col-md-12">
                                                        <label for="addDocumentName">Document Name:</label>
                                                        <input class="form-control input-sm" id="addDocumentName" type="text" value="" autocomplete="none" name="addDocumentName">
                                                    </div>
                                                    <div class="col-md-12">
                                                        <label for="addDocumentType">Document Type:</label>
                                                        <select class="col-md-12 typeDropdown" name="documentTypeDropdown" id="documentTypeDropdown">
                                                            <option value="">Select Document Type</option>
                                                            <?php while ($rows = mysqli_fetch_assoc($documentType)) { ?>
                                                                <option value="<?php echo htmlspecialchars($rows['documentType']); ?>">
                                                                    <?php echo htmlspecialchars($rows['documentType']); ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                        <input class="form-control input-sm" id="addDocumentType" placeholder="If Document type is not available, enter here..." type="text" value="" autocomplete="none" name="addDocumentType">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="form-check form-switch">
                                                        <label class="form-check-label me-5" for="multipleUploads">Enable Multiple Uploads</label>
                                                        <input class="form-check-input" type="checkbox" id="multipleUploads" name="multipleUploads" value="1" onchange="updateSwitchLabel()">
                                                        <p id="uploadStatus" class="mt-2">Status: <strong>Disabled</strong></p>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="uploadTemplate">Upload Template</label>
                                                    <input type="file" class="form-control-file" id="newFile" name="newFile" accept=".docx,.pdf">
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-primary btn-sm" type="submit" id="submitBtn">
                                                    <span class="fa fa-save fw-fa"></span> Submit
                                                </button>
                                                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Loading Modal -->
                            <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body d-flex flex-column align-items-center justify-content-center text-center" style="min-height: 200px;">
                                            <h5 class="text-success">Please Wait...</h5> <!-- Green Text -->
                                            <div class="spinner-border text-success my-3" role="status" style="width: 3rem; height: 3rem;"></div> <!-- Green Spinner -->
                                            <p>Uploading document...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Success Modal -->
                            <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content text-center p-4">
                                        <h5 class="text-success">Success!</h5>
                                        <i class="fa fa-check-circle text-success fa-3x my-3"></i>
                                        <p>Document uploaded successfully.</p>
                                        <button class="btn btn-success" type="button" data-dismiss="modal" id="closeSuccessModal">OK</button>
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

<!-- Loading Modal JavaScript -->
<script>
    document.getElementById("documentForm").addEventListener("submit", function() {
        $("#addModal").modal("hide"); // Hide Add Document modal
        $("#loadingModal").modal("show"); // Show loading modal
    });

    // Show success modal if the URL contains "success=1"
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success')) {
            $("#loadingModal").modal("hide"); // Ensure loading modal is closed
            $("#successModal").modal("show"); // Show success modal

            // Remove "success=1" from the URL without reloading
            const newUrl = window.location.pathname + window.location.search.replace(/(\?|&)success=1/, '');
            window.history.replaceState(null, '', newUrl);
        }
    };

    // Ensure "OK" button properly closes the success modal
    document.getElementById("closeSuccessModal").addEventListener("click", function() {
        $("#successModal").modal("hide");
    });
</script>

<!--Script for Delete and View function -->
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
                url: 'functions/document-delete.php',
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

    function viewPDF(pdfPath) {
        // Open the PDF in a new tab/window
        window.open(pdfPath, '_blank');
    }

    function updateSwitchLabel() {
        const checkbox = document.getElementById('multipleUploads');
        const status = document.getElementById('uploadStatus');
        status.innerHTML = 'Status: <strong>' + (checkbox.checked ? 'Enabled' : 'Disabled') + '</strong>';
    }
</script>

</html>