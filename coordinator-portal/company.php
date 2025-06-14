<?php
session_start();
include_once("../includes/connection.php");
$query = "select * from companylist";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("../elements/meta.php"); ?>
    <title>OJT Coordinator</title>
    <?php include("embed.php"); ?>
    <link rel="stylesheet" href="../assets/css/new-style.css">
</head>


<body>

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar Wrapper -->
        <div class="wrapper">
            <aside id="sidebar" class="expand">
                <?php include('../elements/cood_sidebar.php') ?>
            </aside>


        </div>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar static-top shadow mb-3">
                <!-- Dashboard Title -->
                <h2 class="my-0 mr-auto font-weight-bold text-dark ml-3">Company</h2>
                <?php include('../elements/cood_navbar_user_info.php')?>
            </nav>
            <!-- End of Topbar -->

            <!-- Main Content -->
            <div id="content">

                <!-- Begin Page Content -->
                <div class="col-lg-12 mb-4">

                    <!-- Company -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between">
                            <h3 class="m-0 font-weight-bold text-dark">List of Companies</h3>
                            <div>
                                <a class="export-btn btn btn-primary btn-sm">Export <i class="far fa-file-pdf"></i></a>
                            </div>
                        </div>

                        <!-- Jobs Start -->
                        <div class="container-xxl py-4" style="background-color: #F5F5F5;">
                            <div class="container">
                                <div class="tab-class text-center wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="tab-content">
                                        <div id="tab-1" class="tab-pane fade show p-0 active">
                                            <?php
                                            while ($rows = mysqli_fetch_assoc($result)) {
                                            ?>
                                                <div class="job-item p-4 mb-4">

                                                    <div class="row g-4">
                                                        <div class="col-sm-12 col-md-8 d-flex align-items-center">
                                                            <div class="text-start ps-4">
                                                                <h5 class="mb-3"><?php echo $rows['jobrole']; ?></h5>
                                                                <span class="text-truncate"><i class="fa fa-briefcase" aria-hidden="true"></i> <?php echo $rows['companyName']; ?></span>
                                                                <span class="text-truncate"><i class="fa fa-map-marker-alt text-primary me-2 ml-2"></i><?php echo $rows['companyaddress']; ?></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12 col-md-4 d-flex flex-column align-items-start align-items-md-end justify-content-center">
                                                            <div class="d-flex mb-3">
                                                                <a class="btn btn-primary mr-3" href='view-company.php?number=<?php echo $rows["No"]; ?>'>View</a>

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
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="functions/company-add-process.php" method="POST">

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Company</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <div class="col-md-10">
                                <label for="companyName">Company Name:</label>
                                <input class="form-control input-sm" id="companyName" name="companyName" type="text" value="" autocomplete="none">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-10">
                                <label for="companyaddress">Company Address:</label>
                                <input class="form-control input-sm" id="companyaddress" name="companyaddress" type="text" value="" required autocomplete="none"></input>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-10">
                                <label for="companyaddress">Contact Person:</label>
                                <input class="form-control input-sm" id="contact" name="contact" type="text" value="" required autocomplete="none"></input>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-10">
                                <label for="jobrole">Job Role:</label>
                                <input class="form-control input-sm" id="jobrole" name="jobrole" type="text" value="" autocomplete="none">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-10">
                                <label for="jobdescription">Job Description:</label>
                                <textarea class="form-control" id="jobdescription" name="jobdescription" type="text" value="" autocomplete="none"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-10">
                                <label for="jobreq">Job Requirements/Qualification:</label>
                                <textarea class="form-control " id="jobreq" name="jobreq" type="text" value="" required onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-10">
                                <label for="dept">Department:</label>
                                <select name="dept" class="form-control my-2">
                                    <option hidden disable value="select ">Select</option>
                                    <option value="CBA">CBA</option>
                                    <option value="CAS">CAS</option>
                                    <option value="CTE">CTE</option>
                                    <option value="CCJ">CCJ</option>
                                    <option value="CITCS">CITCS</option>
                                    <option value="COM">COM</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-10">
                                <label for="link">Link:</label>
                                <input class="form-control input-sm" id="link" name="link" type="text" value="" value="" autocomplete="none">
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

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>
    <script>
        $(document).ready(function() {
            $(".export-btn").click(function() {
                window.location.href = "export_company.php";
            });
        });
    </script>
</body>