<?php
session_start();
include("../includes/connection.php");

// Get the student's email from the session
$email = $_SESSION['student'];

// Fetch the department for the student based on the email
$departmentResult = mysqli_query($connect, "SELECT department FROM studentinfo WHERE email='$email'");

// Check if the query was successful and if there is a department
if (!$departmentResult) {
    die("Query Failed: " . mysqli_error($connect));
}

// Check if the department exists in the database
if (mysqli_num_rows($departmentResult) > 0) {
    $row = mysqli_fetch_assoc($departmentResult);
    $department = $row['department'];
    
    // Trim any extra spaces from the department value
    $department = trim($department);

    // Now that we have the department, set it in the session
    $_SESSION['department'] = $department;

    // Fetch documents related to the department
    $documentQuery = "SELECT * FROM documents_list WHERE department='$department'";

    $documentResult = mysqli_query($connect, $documentQuery);

    if (!$documentResult) {
        die("Query Failed: " . mysqli_error($connect));
    }

} else {
    echo "No department found for this student.<br>";  // Debugging if no department is found for the student
}
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>Intern Portal</title>
        <?php include("embed.php"); ?>
    </head>
    <body id="page-top">
        <div id="wrapper">
            <!-- Sidebar Wrapper -->
            <aside id="sidebar" class="expand">
                <?php include('../elements/stud_sidebar.php')?>
            </aside>

            <div class="main">
                <?php
                $email = $_SESSION['student'];
                $query = "SELECT * FROM studentinfo WHERE email ='$email'";
                $result = mysqli_query($connect, $query);
                while ($rows = mysqli_fetch_array($result)) {
                    $image = $rows['image'];
                }
                ?>
                
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Documents</h4>
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo $_SESSION['student']; ?>
                                </span>
                                <img class="img-profile rounded-circle" src="<?php echo $image ? $image : '../img/undraw_profile.svg'; ?>">
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
                            <a class="dropdown-item" href="../logout.php" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                        </li>
                    </ul>
                </nav>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h3 class="m-0 font-weight-bold text-dark">OJT Documents</h3>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th scope="col">Document</th>
                                        <th width="25%" scope="col">File Name</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Date</th>
                                        <th width="34%" align="center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        // Fetch and display document rows inside the HTML table
                                        if (mysqli_num_rows($documentResult) > 0) {
                                            while ($row = mysqli_fetch_assoc($documentResult)) {
                                                $doc_template  = $row['file_template'];

                                                
                                                // Extract file ID from the stored Google Drive URL
                                                preg_match('/\/d\/([^\/]+)/', $doc_template, $matches);
                                                $file_id = $matches[1] ?? '';

                                                if ($file_id) {
                                                    $drive_file_url = "https://drive.google.com/uc?export=download&id=" . urlencode($file_id);
                                                } else {
                                                    $drive_file_url = "#"; // Handle case when file ID is missing or URL is incorrect
                                                }
                                    ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['documentName']); ?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>
                                                <a href="<?php echo $drive_file_url; ?>" class="btn btn-success btn-sm"><i class="fas fa-download"></i>Download Template</a>

                                                <button class="btn btn-success btn-sm uploadButton" id="uploadButton" data-toggle="modal" data-target="#uploadFileModal" data-document="<?php echo htmlspecialchars($row['documentName']); ?>"><i class="fas fa-upload">Upload</i></button>

                                                <button class="btn btn-primary btn-sm" onclick="viewPDF('<?php echo $file; ?>')"><i class="far fa-eye"></i>View</button>

                                                <button class="btn btn-danger btn-sm"><i class="fa fa-trash">Delete</i></button>
                                            </td>
                                        </tr>
                                    <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='5'>No documents available.</td></tr>";  // If no documents are found
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
                                    <!-- LOG OUT MODAL-->
            <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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

            <!--Upload File Modal-->
            <div class="modal fade" id="uploadFileModal" tabindex="-1" role="dialog" aria-labelledby="uploadFileModal" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadModalLabel">Upload File</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="functions/upload_docs.php" enctype="multipart/form-data" method="POST">
                                <div class="form-group">
                                    <label for="documentTypeLabel">Document Type:</label>
                                    <input type="text" class="form-control input-sm" id="documentType" name="documentType" value="" autocomplete="none" readonly>
                                    <label for="fileUploadLabel">Upload File</label>
                                    <input type="file" class="form-control-file" id="uploadFile" name="uploadFile" accept=".docx,.pdf">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
        <script src="../assets/js/sidebarscript.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Select all upload buttons
                let uploadButtons = document.querySelectorAll(".uploadButton");

                uploadButtons.forEach(button => {
                    button.addEventListener("click", function() {
                        // Get document name from data attribute
                        let documentName = this.getAttribute("data-document");
            
                        // Set it in the modal's input field
                        document.getElementById("documentType").value = documentName;
                    });
                });
            });
        </script>
    </body>
</html>
