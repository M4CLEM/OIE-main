<?php
    session_start();
    include_once("../includes/connection.php");

    $companyNum = $_GET['number'];
    $department = isset($_GET['dept']) ? $_GET['dept'] : '';

    $query="select * from companylist where No={$companyNum}";
    $result=mysqli_query($connect,$query);
    $rows=mysqli_fetch_assoc($result);

    $targetPage = $department !== '' ? 'company-filter.php?dept=' . $department : 'company.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("../elements/meta.php"); ?>
    <title>CIPA ADMIN</title>
    <?php include("embed.php"); ?>
    <link rel="stylesheet" href="../assets/css/new-style.css">
</head>


<body id="page-top">

    <!-- Page Wrapper -->
    <div class="wrapper">

        <!--Sidebar Wrapper-->
        <aside id="sidebar" class="expand">
            <?php include('../elements/cipa_sidebar.php')?>
        </aside>

        <div class="main py-3">
            <div id="wrapper">
                <!-- Job Detail Start -->
                <div class="container-xxl py-8 ml-3 mt-3 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="container">
                        <div class="row gy-5 gx-4">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center mb-5">
                                    <div class="text-start">
                                        <a href="<?php echo $targetPage; ?>" class="btn btn-primary"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i></a>
                                        <br><br><h3 class="mb-3"><?php echo $rows['jobrole'];?></h3>
                                        <span class="text-truncate me-0"><i class="fa fa-briefcase" aria-hidden="true"></i> <?php echo $rows['companyName'];?></span>
                                        <span class="text-truncate me-3"><i class="fa fa-map-marker-alt text-primary me-2 ml-2"></i><?php echo $rows['companyaddress'];?></span>
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <h4 class="mb-3">Work Type</h4>
                                    <p><?php echo $rows['workType'];?></p>
                                    <br><h4 class="mb-3">Description</h4>
                                    <p><?php echo nl2br($rows['jobdescription']);?></p>
                                    <br><h4 class="mb-3 mt-3">Qualifications</h4>
                                    <p><?php echo nl2br($rows['jobreq']);?></p>   
                                    <br><h5 class="mb-3 mt-3">For those who are interested, contact <?php echo $rows['contactPerson'];?></h5>
                                    <p></p>  
                                </div>

                                <form>
                                    <div class="row g-3 mb-5">
                                        <div class="col-4">
                                            <a title="Edit" href="company-edit.php?number=<?php echo $rows['No']; ?>&dept=<?php echo $department ?>" class="btn btn-primary w-40"> <i class="fa fa-edit fw-fa"></i> Edit</a>
                                            <a title="Delete" href="functions/company-delete-process.php?number=<?php echo $rows['No']; ?>&dept=<?php echo $department ?>" class="btn btn-primary w-40 btn-danger ml-3"><i class="fa fa-trash"></i> Delete</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>
    
</body>
