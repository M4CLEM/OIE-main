<?php

use Symfony\Contracts\Service\Attribute\Required;
session_start();
include_once("../includes/connection.php"); 
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <?php include("../elements/meta.php"); ?>
    <title>Student Portal</title>
    <?php include("embed.php"); ?>

</head>

<body id="page-top">

   <!-- Page Wrapper -->
   <div id="wrapper">

        <!--Sidebar Wrapper-->
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
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">

                <!-- Title -->
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Deployment</h4>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php (isset($_SESSION['student'])) ?> <?php echo $_SESSION['student']; ?></span>
                                <?php
                                function get_drive_image_url($image) {
                                    // Check if the image is a Google Drive URL
                                    if (strpos($image, 'drive.google.com') !== false) {
                                        // Extract the File ID from different Drive URL formats
                                        preg_match('/(?:id=|\/d\/)([a-zA-Z0-9_-]{25,})/', $image, $matches);
                                        $image = $matches[1] ?? null; // Get the File ID if found
                                    }

                                    // If a valid Google Drive File ID is found, return the direct image link
                                    if ($image && preg_match('/^[a-zA-Z0-9_-]{25,}$/', $image)) {
                                        return "https://lh3.googleusercontent.com/d/{$image}=w1000";
                                    }
                                    // If it's not a Google Drive image, return it as is
                                        return $image;
                                }
                            ?>
                            <img class="img-profile rounded-circle" src="<?php echo $image ? get_drive_image_url($image) : '../img/undraw_profile.svg'; ?>">
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
            <!-- End of Topbar -->

            <?php

                if (isset($_SESSION['student'])) {

                    $stmt = $connect->prepare("SELECT * FROM company_info WHERE student_email = ?");
                    $stmt->bind_param("s", $_SESSION['student']);
                    $stmt->execute();
                    $result = $stmt->get_result(); 
                    $row = $result->fetch_assoc();

                    if ($row) {
                        echo '<div class="col-md-12 my-3">';
                        echo '<div class="card shadow-sm px-5">';
                        echo '<div class="py-5">';
                        echo '<h4 class="mb-2">Company Info</h4>';
                        echo '<div class="pt-3 mb-2"><p>Company Name: ' . $row['companyName'] . '</p></div>';
                        echo '<div class="pt-3 mb-2"><p>Company Address: ' . $row['companyAddress'] . '</p></div>';
                        echo '<div class="pt-3 mb-2"><p>Trainer\'s Contact Number: ' . $row['trainerContact'] . '</p></div>';
                        echo '<div class="pt-3"><p>Trainer\'s Email Address: ' . $row['trainerEmail'] . '</p></div>';
                        echo '<div class="pt-3 mb-2"><p>Work Type: ' . $row['workType'] . '</p></div>';
                        echo '<div class="mb-2 mt-4"><h3>Status: <b style="color: ';
                        if ($row['status'] == 'Pending') {
                            echo 'red';
                        } elseif ($row['status'] == 'Approved') {
                            echo 'green';
                        } else {
                            echo 'orange';
                        }
                        echo ';">' . $row['status'] . '</b></h3></div>';
                        echo '</div>';
                        echo '</div>';
                    
                        if ($row['status'] != 'Change Request') {
                            echo '<div class="row"><div class="col text-center mt-3">';
                            echo '<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#areYouSureModal"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Request to Change</button>';
                            echo '</div></div>';
                        }                        

                    } else {
                        echo '<div class="col-md-12 my-3">';
                        echo '    <div class="card shadow-sm px-5">';
                        echo '        <div class="py-5">';
                        echo '            <form action="deployment-process.php" method="POST" enctype="multipart/form-data">';
                        echo '                <h2 class="mb-5 text-center">DEPLOYMENT INFORMATION</h2>';
                        echo '                <div class="row">';
                        echo '                    <div class="col-6">';
                        echo '                        <h4 class="mb-2">Company Info</h4>';
                        echo '                        <div class="pt-3 mb-2">';
                        echo '                            <label for="companyName">Company Name:</label>';
                        echo '                            <input type="text" class="form-control" id="companyName" name="companyName" placeholder="Enter Company Name" required>';
                        echo '                        </div>';
                        echo '                        <div class="pt-3 mb-2">';
                        echo '                            <label for="companyAddress">Company Address:</label>';
                        echo '                            <input type="text" class="form-control" id="companyAddress" name="companyAddress" placeholder="Enter Company Address" required>';
                        echo '                        </div>';
                        echo '                        <div class="pt-3 mb-2">';
                        echo '                            <label for="contactPerson">Contact Person:</label>';
                        echo '                            <input type="text" class="form-control" id="contactPerson" name="contactPerson" placeholder="Enter Contact Person" required>';
                        echo '                        </div>';
                        echo '                        <div class="pt-3 mb-2">';
                        echo '                            <label for="trainerContact">Trainer\'s Contact Number:</label>';
                        echo '                            <input type="text" class="form-control" id="trainerContact" name="trainerContact" placeholder="Enter Contact Number" required>';
                        echo '                        </div>';
                        echo '                        <div class="pt-3 mb-2">';
                        echo '                            <label for="trainerEmail">Trainer\'s Email Address:</label>';
                        echo '                            <input type="text" class="form-control" id="trainerEmail" name="trainerEmail" placeholder="Enter Trainer\'s Email Address" required>';
                        echo '                        </div>';
                        echo '                        <div class="pt-3 mb-2">';
                        echo '                            <label for="companyLink">Company Link:</label>';
                        echo '                            <input type="text" class="form-control" id="companyLink" name="companyLink" placeholder="Enter Company Website/Social Media Link">';
                        echo '                        </div>';
                        echo '                    </div>';
                        echo '                    <div class="col-6">';
                        echo '                        <h4 class="mb-4">Work Type</h4>';
                        echo '                        <div class="row mb-5">';
                        echo '                            <div class="col">';
                        echo '                                <div class="pt-3 mb-2">';
                        echo '                                    <label for="workType">Type of work:</label>';
                        echo '                                    <select class="form-control" name="workType" id="select">';
                        echo '                                        <option hidden disable value="select">Select</option>';
                        echo '                                        <option value="WFH">Work from Home</option>';
                        echo '                                        <option value="Onsite">On site</option>';
                        echo '                                        <option value="PB">Project-based</option>';
                        echo '                                    </select>';
                        echo '                                </div>';
                        echo '                                <div class="pt-3 mb-2">';
                        echo '                                    <label for="jobrole">Jobrole:</label>';
                        echo '                                    <input type="text" class="form-control" id="jobrole" name="jobrole" placeholder="Enter Job Role" required>';
                        echo '                                </div>';
                        echo '                                <div class="pt-3 mb-2">';
                        echo '                                    <label for="jobDescription">Job Description:</label>';
                        echo '                                    <textarea class="form-control" name="jobDescription" id="jobDescription" placeholder="Enter Job description"></textarea>';
                        echo '                                </div>';

                        echo '                                <div class="pt-3 mb-2">';
                        echo '                                    <label for="jobRequirement">Job Requirement:</label>';
                        echo '                                    <textarea class="form-control" name="jobRequirement" id="jobRequirement" placeholder="Enter Job Requirement"></textarea>';
                        echo '                                </div>';
                        echo '                            </div>';
                        echo '                        </div>';
                        echo '                        <div class="row">';
                        echo '                            <div class="col text-right">';
                        echo '                                <button type="submit" class="btn btn-success" name="submit">';
                        echo '                                    <span class="fas fa-save fw-fa"></span> Submit Info';
                        echo '                                </button>';
                        echo '                            </div>';
                        echo '                        </div>';
                        echo '                    </div>';
                        echo '                </div>';
                        echo '            </form>';
                        echo '        </div>';
                        echo '    </div>';
                        echo '</div>';

                    }
                } else {
                    echo "Session variable 'student' is not set.";
                }
            ?>
        </div>
    </div>

    <div class="modal fade" id="areYouSureModal" tabindex="-1" role="dialog" aria-labelledby="areYouSureModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="areYouSureModalLabel">Are you sure?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to request a change?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirmChange">Yes</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
            </div>
        </div>
    </div>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous"></script>
<script src="../assets/js/sidebarscript.js"></script>

<script>
    var $select1 = $('#select1'),
        $select2 = $('#select2');

    $select1.on('change', function() {
        var selectedDepartment = $(this).val();
        if (selectedDepartment === 'CBA') {
            $select2.html('<option hidden disable value="select">Select</option><option value="BSBA">BSBA</option><option value="BSA">BSA</option>');
        } else if (selectedDepartment === 'CAS') {
            $select2.html('<option hidden disable value="select">Select</option><option value="PSYCHOLOGY">PSYCHOLOGY</option><option value="MASS COMMUNICATION">MASS COMMUNICATION</option>');
        } else if (selectedDepartment === 'CTE') {
            $select2.html('<option hidden disable value="select">Select</option><option value="BEED">BEED</option><option value="BSED">BSED</option>');
        } else if (selectedDepartment === 'CCJ') {
            $select2.html('<option hidden disable value="select">Select</option><option value="BS CRIMINOLOGY">BS CRIMINOLOGY</option>');
        } else if (selectedDepartment === 'CITCS') {
            $select2.html('<option hidden disable value="select">Select</option><option value="BSIT">BSIT</option><option value="BSCS">BSCS</option><option value="ACT">ACT</option>');
        } else {
            $select2.html('<option hidden disable value="select">Select</option><option value="BSBA">BSBA</option><option value="BSA">BSA</option><option value="PSYCHOLOGY">PSYCHOLOGY</option><option value="MASS COMMUNICATION">MASS COMMUNICATION</option><option value="BEED">BEED</option><option value="BSED">BSED</option><option value="BS CRIMINOLOGY">BS CRIMINOLOGY</option><option value="BSIT">BSIT</option><option value="BSCS">BSCS</option><option value="ACT">ACT</option>');
        }
    }).trigger('change');

    $select2.on('change', function() {
        var selectedCourse = $(this).val();
        if (selectedCourse) {
            if (selectedCourse === 'BSBA' || selectedCourse === 'BSA') {
                $select1.val('CBA');
                $select2.val(selectedCourse);
            } else if (selectedCourse === 'PSYCHOLOGY' || selectedCourse === 'MASS COMMUNICATION') {
                $select1.val('CAS');
                $select2.val(selectedCourse)
            } else if (selectedCourse === 'BEED' || selectedCourse === 'BSED') {
                $select1.val('CTE');
                $select2.val(selectedCourse);
            } else if (selectedCourse === 'BS CRIMINOLOGY') {
                $select1.val('CCJ');
                $select2.val(selectedCourse);
            } else if (selectedCourse === 'BSIT' || selectedCourse === 'BSCS' || selectedCourse === 'ACT') {
                $select1.val('CITCS');
                $select2.val(selectedCourse);
            }
        }
    });
</script>

<script>
    function showAreYouSureModal() {
        $('#areYouSureModal').modal('show');
    }
</script>

<script>
$(document).ready(function() {
 $('#confirmChange').click(function() {
    $.ajax({
      url: 'request-process.php',
      type: 'POST',
      data: {status: 'Change Request'},
      success: function(response) {
        // Handle success, e.g., show a success message or reload the page
        alert('Status updated successfully.');
        location.reload();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        // Handle error
        alert('Error updating status: ' + textStatus);
      }
    });
 });
});
</script>
