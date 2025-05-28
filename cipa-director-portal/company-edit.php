<?php
    session_start();
    include_once("../includes/connection.php");

    if (isset($_GET['number'])) {
        $compNum = $_GET['number'];
    } else {
        echo "ERROR!";
    }

    $query = "SELECT * FROM companylist WHERE No={$compNum}";
    $result = mysqli_query($connect, $query) or die(mysqli_error($connect));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include("../elements/meta.php"); ?>
    <title>CIPA ADMIN</title>
    <?php include("embed.php"); ?>
</head>


<body>

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar Wrapper -->
        <aside id="sidebar" class="expand">
            <?php include('../elements/cipa_sidebar.php')?>
        </aside>

        <div class="main py-3">

            <!-- Content Wrapper -->
            <div id="content-wrapper" class="d-flex flex-column">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar static-top shadow mb-3">
                    <!-- Dashboard Title -->
                    <h2 class="my-0 mr-auto font-weight-bold text-dark ml-3">Company</h2>
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <?php include('../elements/cipa_navbar_user_info.php') ?>
                </nav>
                <!-- End of Topbar -->

                    <!-- Main Content -->
                    <div id="content">

                        <!-- Begin Page Content -->            
                        <div class="col-lg-12 mb-4">

                            <!-- Illustrations -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <form action="functions/company-edit-process.php?dept=<?php echo $_GET['dept']?>" method="POST">

                                        <h4 class="m-0 font-weight-bold text-dark">Edit</h4> 

                                        <div class="card-body">   

                                            <?php
                                                
                                            while($rows = mysqli_fetch_array($result)) {   
                                                $compNum = $rows['No'];            
                                                $companyName = $rows['companyName'];
                                                $companyaddress = $rows['companyaddress'];
                                                $contactPerson = $rows['contactPerson'];
                                                $jobrole =$rows['jobrole'];
                                                $jobdescription = $rows['jobdescription'];
                                                $jobreq = $rows['jobreq'];
                                                $link = $rows['link'];
                                                $slots = $rows['slots'];
                                            }
                                                                                                
                                            ?> 

                                            <div class="row">

                                                <div class="form-group col-lg-5">  
                                                    <div class="col-md-10">                    
                                                    <label for= "companyName">Company Name:</label>  
                                                        <input class="form-control input-sm" id="companyName" name="companyName" type="text" value="<?php echo $companyName;?>" autocomplete="none">
                                                    </div>
                                                </div>   

                                                <div class="form-group col-lg-5 ">
                                                    <div class="col-md-10">
                                                        <label for="companyaddress">Company Address:</label> 
                                                        <input class="form-control input-sm" id="companyaddress" name="companyaddress" type="text" value="<?php echo $companyaddress;?>" required  onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off"></input>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="form-group col-lg-5">  
                                                    <div class="col-md-10">                    
                                                        <label for= "jobrole">Job Role</label>  
                                                        <input class="form-control input-sm" id="jobrole" name="jobrole" type="text" value="<?php echo $jobrole;?>" autocomplete="none">
                                                    </div>
                                                </div>   

                                                <div class="form-group col-lg-5 ">
                                                    <div class="col-md-10">
                                                        <label for= "link">Link:</label>                     
                                                        <input class="form-control input-sm" id="link" name="link" type="text" value="<?php echo $link;?>" autocomplete="none">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">

                                                <div class="form-group col-lg-5">  
                                                    <div class="col-md-10">                    
                                                        <label for= "contact">Contact Person:</label>  
                                                        <input class="form-control input-sm mb-4" id="contact" name="contact" type="text" value="<?php echo $contactPerson;?>" autocomplete="none">
                                                    </div>
                                                </div>
                                                
                                                <div class="form-group col-lg-5">
                                                    <div class="col-md-10">
                                                        <label for="workType">workType:</label>
                                                        <select name="workType" id="workType" class="form-control my-2">
                                                            <option hidden disable value="select">Select</option>
                                                            <option value="Onsite">On-Site</option>
                                                            <option value="WFH">Work from Home</option>
                                                            <option value="PB">Project-Based</option>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="row">
                                                <div class="form-group col-lg-5">
                                                    <div class="col-md-10">
                                                        <label for="slots">Slots:</label>
                                                        <input type="number" class="form-control input-sm mb-4" name="slots" id="slots" value="<?php echo $slots;?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-lg-12 ">
                                                    <div class="col-md-12">
                                                        <label for="jobdescription">Job Description:</label> 
                                                        <textarea class="form-control" id="jobdescription" name="jobdescription" type="text" required  rows="10" value="<?php echo $jobdescription;?>"
                                                        onkeyup="javascript:capitalize(this.id, this.value);" autocomplete="off"><?php echo $jobdescription;?></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="form-group col-lg-12">
                                                    <div class="col-md-12">
                                                        <label for="jobreq">Qualifications:</label>
                                                        <textarea class="form-control input-sm" id="jobreq" name="jobreq" rows="10" type="text" 
                                                        value ="<?php echo $jobreq;?>" autocomplete="none"><?php echo $jobreq;?></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-md-4 control-label" for="idno"></label>  
                                                <div class="col-md-8">
                                                    <button class="btn btn-primary btn-sm" name="update" type="submit" ><span class="fa fa-save fw-fa"></span> Update</button>
                                                    <a href="view-company.php?number=<?php echo $compNum; ?>&dept=<?php echo $_GET['dept']; ?>" class="btn btn-secondary btn-sm"><span class="glyphicon glyphicon-arrow-left"></span>Back</a>
                                                    <input type="hidden" name="No" value="<?php echo $compNum; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- End Illustrations -->
                        </div>
                        <!-- End Page Content -->            
                    </div>
                </div>
                <!-- End of Main Content -->
            </div>
            <!-- End of Content Wrapper -->
        </div>
    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>

</body>