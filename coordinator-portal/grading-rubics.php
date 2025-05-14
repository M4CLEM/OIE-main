<?php
session_start();
include_once("../includes/connection.php");

$department = $_SESSION['department'];
$coordinatorRole = $_SESSION['coordinator'];

$result = mysqli_query($connect, "SELECT * FROM grading_rubics WHERE department = '$department'");
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
</head>

<body id="page-top">
    <div class="wrapper">
        <aside id="sidebar" class="expand">
            <?php include('../elements/cood_sidebar.php') ?>
        </aside>
        <div class="main">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Grading Rubrics</h4>
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
                            <a href="modal.php" class="btn btn-primary addBtn" data-toggle="modal" data-target="#addModal">+Add Grading Rubric</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th scope="col"></th>
                                            <th scope="col">Adviser Weight</th>
                                            <th scope="col">Company Weight</th>
                                            <th scope="col">School Year</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (mysqli_num_rows($result) > 0) {
                                            $counter = 1;

                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<tr>";
                                                echo    "<td>$counter</td>";
                                                echo    "<td>{$row['adviserWeight']}%</td>";
                                                echo    "<td>{$row['companyWeight']}%</td>";
                                                echo    "<td>{$row['schoolYear']}</td>";
                                                echo    "<td>
                                                            <a href=\"#\" class=\"btn btn-primary btn-sm editBtn\" 
                                                                data-toggle=\"modal\" data-target=\"#editModal\" 
                                                                data-id=\"{$row['id']}\"
                                                                data-adviser=\"{$row['adviserWeight']}\"
                                                                data-company=\"{$row['companyWeight']}\">
                                                                <i class=\"fa fa-edit fw-fa\"></i> Edit
                                                            </a>
                                                                    <button type=\"button\" class=\"btn btn-danger btn-sm deleteBtn\" data-toggle=\"modal\"
                                                                        data-target=\"#deleteModal\" data-id=\"{$row['id']}\">
                                                                        <i class=\"fa fa-trash fw-fa\"></i> Delete
                                                                    </button>
                                                                </td>";
                                                echo "</tr>";
                                                $counter++;
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action="functions/add_grading_rubic.php" method="POST">
                            <div class="modal-header">
                                <h5>Add Grading Rubic</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 text-right">
                                            <label for="adviserWeight">Adviser Weight</label>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <input type="range" id="weightSlider" min="0" max="100" value="50" class="form-control-range">
                                        </div>
                                        <div class="col-md-4 text-left">
                                            <label for="companyWeight">Company Weight</label>
                                        </div>
                                    </div>
                                    <div class="row align-items-center mt-2">
                                        <div class="col-md-4 text-right d-flex align-items-center">
                                            <div class="input-group">
                                                <input type="number" name="adviserWeight" id="adviserWeight" class="form-control text-center" min="0" max="100">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-dark text-white">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center"> <!-- Slider placeholder --> </div>
                                        <div class="col-md-4 text-left d-flex align-items-center">
                                            <div class="input-group">
                                                <input type="number" name="companyWeight" id="companyWeight" class="form-control text-center" min="0" max="100">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-dark text-white">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <div class="row">
                                            <label for="semester">Semester</label>
                                            <input type="text" id="semester" name="semester" class="form-control" required>
                                        </div>
                                        <div class="row">
                                            <label for="schoolYear">School Year</label>
                                            <input type="text" name="schoolYear" id="schoolYear" class="form-control" required>
                                        </div>
                                    </div>
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
            <!-- EDIT MODAL -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModal" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <!-- Remove action and add id="editForm" -->
                        <form id="editForm" method="POST">
                            <div class="modal-header">
                                <h5>Edit Grading Rubric</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="edit_id" name="id"> <!-- Hidden input for rubric ID -->

                                <div class="form-group">
                                    <div class="row align-items-center">
                                        <div class="col-md-4 text-right">
                                            <label for="edit_adviserWeight">Adviser Weight</label>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <input type="range" id="edit_weightSlider" min="0" max="100" value="50" class="form-control-range">
                                        </div>
                                        <div class="col-md-4 text-left">
                                            <label for="edit_companyWeight">Company Weight</label>
                                        </div>
                                    </div>
                                    <div class="row align-items-center mt-2">
                                        <div class="col-md-4 text-right d-flex align-items-center">
                                            <div class="input-group">
                                                <input type="number" name="adviserWeight" id="edit_adviserWeight" class="form-control text-center" min="0" max="100">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-dark text-white">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-center"> <!-- Slider placeholder --> </div>
                                        <div class="col-md-4 text-left d-flex align-items-center">
                                            <div class="input-group">
                                                <input type="number" name="companyWeight" id="edit_companyWeight" class="form-control text-center" min="0" max="100">
                                                <div class="input-group-append">
                                                    <span class="input-group-text bg-dark text-white">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <div class="row">
                                            <label for="edit_semester">Semester</label>
                                            <input type="text" id="edit_semester" name="semester" class="form-control">
                                        </div>
                                        <div class="row">
                                            <label for="edit_schoolYear">School Year</label>
                                            <input type="text" name="schoolYear" id="edit_schoolYear" class="form-control">
                                        </div>
                                    </div>
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


            <!-- DELETE MODAL -->
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
                            Are you sure you want to delete this grading rubic?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const slider = document.getElementById("weightSlider");
            const adviserInput = document.getElementById("adviserWeight");
            const companyInput = document.getElementById("companyWeight");

            function updateFromSlider() {
                adviserInput.value = slider.value;
                companyInput.value = 100 - slider.value;
            }

            function updateFromAdviserInput() {
                let value = parseInt(adviserInput.value);
                if (isNaN(value) || value < 0) value = 0;
                if (value > 100) value = 100;
                adviserInput.value = value;
                companyInput.value = 100 - value;
                slider.value = value;
            }

            function updateFromCompanyInput() {
                let value = parseInt(companyInput.value);
                if (isNaN(value) || value < 0) value = 0;
                if (value > 100) value = 100;
                companyInput.value = value;
                adviserInput.value = 100 - value;
                slider.value = 100 - value;
            }

            slider.addEventListener("input", updateFromSlider);
            adviserInput.addEventListener("input", updateFromAdviserInput);
            companyInput.addEventListener("input", updateFromCompanyInput);

            updateFromSlider();
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const slider = document.getElementById("edit_weightSlider");
            const adviserInput = document.getElementById("edit_adviserWeight");
            const companyInput = document.getElementById("edit_companyWeight");

            // Function to update input fields when slider is moved
            slider.addEventListener("input", function() {
                adviserInput.value = this.value;
                companyInput.value = 100 - this.value; // Adjust company weight accordingly
            });

            // Function to update slider when input field changes
            adviserInput.addEventListener("input", function() {
                if (this.value >= 0 && this.value <= 100) {
                    slider.value = this.value;
                    companyInput.value = 100 - this.value; // Ensure total is 100
                }
            });

            companyInput.addEventListener("input", function() {
                if (this.value >= 0 && this.value <= 100) {
                    adviserInput.value = 100 - this.value;
                    slider.value = adviserInput.value;
                }
            });

            // Auto-fill modal fields when Edit button is clicked
            document.querySelectorAll(".editBtn").forEach(button => {
                button.addEventListener("click", function() {
                    let id = this.getAttribute("data-id");
                    let adviserWeight = this.getAttribute("data-adviser");
                    let companyWeight = this.getAttribute("data-company");
                    let semester = this.getAttribute("data-semester");
                    let schoolYear = this.getAttribute("data-schoolYear");

                    document.getElementById("edit_id").value = id;
                    adviserInput.value = adviserWeight;
                    companyInput.value = companyWeight;
                    slider.value = adviserWeight;
                    document.getElementById("edit_semester").value = semester;
                    document.getElementById("edit_schoolYear").value = schoolYear;
                });
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#editForm").submit(function(event) {
                event.preventDefault(); // Prevent default form submission

                $.ajax({
                    url: "update_grading_rubic.php", // PHP file for processing
                    type: "POST",
                    data: $(this).serialize(), // Serialize form data
                    success: function(response) {
                        if (response.includes("Success")) {
                            $("#editModal").modal("hide"); // Close modal on success
                            alert("Grading rubric updated successfully!");
                            location.reload(); // Reload the page to update the table
                        } else {
                            alert("Error: " + response); // Show error message
                        }
                    },
                    error: function() {
                        alert("Something went wrong! Please try again.");
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            var deleteId = null; // Store the ID of the rubric to delete

            // Function to open delete modal with ID
            function openDeleteModal(id) {
                deleteId = id; // Store the ID
                $("#deleteModal").modal("show"); // Show modal
            }

            // Event listener for delete button
            $(".deleteBtn").click(function () {
                var rubricId = $(this).data("id"); // Get rubric ID from button
                openDeleteModal(rubricId); // Open modal with the ID
            });

            // Confirm delete action
            $("#confirmDelete").click(function () {
                if (deleteId) {
                    $.ajax({
                        url: "delete_grading_rubic.php",
                        type: "POST",
                        data: { id: deleteId },
                        success: function (response) {
                            if (response.includes("Success")) {
                                $("#deleteModal").modal("hide"); // Close modal
                                alert("Grading rubric deleted successfully!");
                                location.reload(); // Reload page to update table
                            } else {
                                alert("Error: " + response);
                            }
                        },
                        error: function () {
                            alert("Something went wrong! Please try again.");
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>