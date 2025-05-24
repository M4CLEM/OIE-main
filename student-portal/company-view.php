<?php
    session_start();
    include_once("../includes/connection.php");

    $email = $_SESSION['student'];
    $semester = $_SESSION['semester'];
    $schoolYear = $_SESSION['schoolYear'];

    $query="select * from companylist";
    $result=mysqli_query($connect,$query);
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <?php include("../elements/meta.php"); ?>
    <title>Intern Portal</title>
    <?php include("embed.php"); ?>
    <link rel="stylesheet" href="../assets/css/new-style.css">

</head>



<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!--Sidebar Wrapper-->
        <aside id="sidebar" class="expand">
            <?php include('../elements/stud_sidebar.php')?>
        </aside>

        <div class="main">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                <!-- Title -->
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Student Interns</h4>
                <!-- Topbar Navbar -->
                <?php include('includes/navbar_user_info.php'); ?>
            </nav>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->            
            <div class="col-lg-12 mb-4">
                <!-- Illustrations -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h4 class="m-0 font-weight-bold text-dark">Company Details 
                        <a href="company.php" class="close" type="" aria-label="Close"> <span aria-hidden="true">×</span></h4>
                        </a> 
                    </div>
                    <div class="card-body">   
                    <?php
                            $query = "SELECT * FROM companylist WHERE No =".$_GET['id'];
                            $result = mysqli_query($connect, $query) or die(mysqli_error($connect));
                            while($rows = mysqli_fetch_array($result))
                            {   
                                $No = $rows['No'];
                                $companyName = $rows['companyName'];
                                $jobrole = $rows['jobrole'];            
                                $companyaddress = $rows['companyaddress'];
                                $jobdescription = $rows['jobdescription'];
                                $jobreq = $rows['jobreq'];
                                $link = $rows['link'];
                                            
                            }
                                            
                            $No = $_GET['id'];

                            // Check if the student already applied for this company
                            $checkQuery = "SELECT * FROM applications 
                                WHERE email = '$email' 
                                AND companyName = '$companyName'
                                AND jobrole = '$jobrole' 
                                AND semester = '$semester' 
                                AND schoolYear = '$schoolYear'";
                            $checkResult = mysqli_query($connect, $checkQuery);
                            $alreadyApplied = mysqli_num_rows($checkResult) > 0;
                                        
                    ?> 
                    <form id="applicationForm" method="POST">
                        <div class="col-md-10">                    
                            <input type="hidden" name="No" value="<?php echo $No; ?>">
        
                            <dl class="row">
                                <dt class="col-sm-4">Company Name: </dt>
                                <dd class="col-sm-8"><?php echo $companyName ?></dd>
                                <input type="hidden" name="companyName" value="<?php echo $companyName; ?>">
                            </dl>

                            <dl class="row">
                                <dt class="col-sm-4">Company Address: </dt>
                                <dd class="col-sm-8"><?php echo $companyaddress ?></dd>
                            </dl>

                            <dl class="row">
                                <dt class="col-sm-4">Job Role: </dt>
                                <dd class="col-sm-8"><?php echo $jobrole ?></dd>
                                <input type="hidden" name="jobrole" value="<?php echo $jobrole; ?>">
                            </dl>

                            <dl class="row">
                                <dt class="col-sm-4">Job Description:  </dt>
                                <dd class="col-sm-8"><?php echo $jobdescription; ?></dd>
                            </dl>

                            <dl class="row">
                                <dt class="col-sm-4">Requirements/Qualification: </dt>
                                <dd class="col-sm-8"><?php echo $jobreq; ?></dd>
                            </dl>

                            <dl class="row">
                                <dt class="col-sm-4">Link: </dt>
                                <dd class="col-sm-8"><a href="<?php echo $link; ?>"><?php echo $link; ?></a></dd>
                                <input type="hidden" name="link" value="<?php echo $link; ?>">
                            </dl>

                            <div class="text-end mt-3">
                                <?php if ($alreadyApplied): ?>
                                    <button type="button" class="btn btn-secondary" disabled>You already applied</button>
                                <?php else: ?>
                                    <button type="submit" class="btn btn-success">Apply</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End of Main Content -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">Application Submitted</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    You have successfully applied for this job.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS (v5) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $('#applicationForm').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            $.ajax({
                type: 'POST',
                url: 'functions/application_process.php',
                data: $(this).serialize(),
                success: function(response) {
                    // Show the success modal using Bootstrap 4 jQuery API
                    $('#successModal').modal('show');

                    // Listen for modal close event using Bootstrap 4 event
                    $('#successModal').one('hidden.bs.modal', function () {
                        location.reload();
                    });
                },
                error: function() {
                    alert("There was an error processing your application.");
                }
            });
        });
    </script>
</body>
</html>