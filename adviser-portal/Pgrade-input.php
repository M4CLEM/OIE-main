<?php
session_start();
include_once("../includes/connection.php");
$average = ' ';
$grade = ' ';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("../elements/meta.php"); ?>
    <title>Adviser Portal</title>
    <?php include("embed.php"); ?>
</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!--Sidebar Wrapper-->
        <aside id="sidebar" class="expand">
            <?php include('../elements/adv_sidebar.php')?>
        </aside>

        <div class="main">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">

            <!-- Title -->
            <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Student Interns</h4>

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php (isset($_SESSION['adviser'])) ?> <?php echo $_SESSION['adviser']; ?></span>
                            <img class="img-profile rounded-circle"
                                src="../img/undraw_profile.svg">
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
                            <a class="dropdown-item" href="../logout.php" data-toggle="logout" data-target="logout">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                Logout
                            </a>
                        </div>
                    </li>

                </ul>

            </nav>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <?php
            $query = "SELECT * FROM student_grade WHERE studentID =".$_GET['studentID'];
            $result = mysqli_query($connect, $query) or die(mysqli_error($connect));
                while($rows = mysqli_fetch_array($result))
                {   
                        
                $studentID = $rows['studentID']; 
                $Fullname = $rows['Fullname'];
                
                $SOU = $rows['SenseofUrgency'];
                $QOW = $rows['QualityofWork'];
                $EC = $rows['ExecutionConcept'];
                $PAP = $rows['PromptnessandPunctuality'];
                $WE = $rows['WorkEthics'];
                $D = $rows['Demeanor'];

                        
                }
                
                $studentID = $_GET['studentID'];
                    
            ?> 

            <div class="col-lg-12 mb-4">
            <!-- Illustrations -->         
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <form action="" method="POST">
                            <h6 class="m-0 font-weight-bold text-dark">INPUT GRADE</h6> <br>
                            <cite title = "1-unsatisfactory, 2-Devewloping, 3-Satisfactory, 4-Very satisfactory">1-unsatisfactory, 2-Developing, 3-Satisfactory, 4-Very satisfactory</cite>
                    </div>
                    
                    <div class="card-body">
                    <?php
                    if(isset($_POST['save']))
                    {
                    
                    
                        $total = $SOU + $QOW + $EC + $PAP + $WE;
                        $average = $total / 5.0;
                        $percentage = ($total / 500.0) * 100;

                        if ($average >= 90)
                            $grade = "A";
                        else if ($average >= 80 && $average < 90)
                            $grade = "B";
                        else if ($average >= 70 && $average < 80)
                            $grade = "C";
                        else if ($average >= 60 && $average < 70)
                            $grade = "D";
                        else
                            $grade = "E";
                    }
                    ?>
                            
                        <div class="row">
                        <div class="col-md-3">
                        <label for="studentID">StudentID</label>
                        <input type="studentID" id="disabledTextInput" name="studentID" class="form-control" value="<?php echo $studentID;?>" readonly >
                        </div>
                    <div class="col-md-3">
                        <label for="Fullname">Fullname</label>
                        <input type="Fullname" name="Fullname" class="form-control" value="<?php echo $Fullname;?>" readonly>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">                       
                        <label for="SenseofUrgency">Sense of Urgency</label>
                        <input  type="number" name="SenseofUrgency" class="form-control" value="" required>
                        </div>
                    <div class="col-md-3">
                        <label for="QualityofWork">Quality of Work</label>
                        <input type="number" name="QualityofWork" class="form-control" value="" required>
                        </div>
                        <div class="col-md-3">
                            <label for="ExecutionConcept">Execution Concept</label>
                            <input type="number"class="form-control" id="ExecutionConcept" name="ExecutionConcept" value="" required>
                        </div>
                    </div>
                        <div class="row">
                        <div class="col-md-3">
                            <label for= "PromptnessandPunctuality">Promptness and Punctuality</label>
                            <input  type="number" class="form-control" id="PromptnessandPunctuality" name="PromptnessandPunctuality" value="" required>
                            </div>
                        <div class="col-md-3">
                            <label for="WorkEthics">Work Ethics</label>
                            <input  type="number" class="form-control" id="WorkEthics" name="WorkEthics" value="" required>
                    </div>
                    <div class="col-md-3">
                            <label for= "Demeanor">Demeanor</label>             
                            <input type="number" class="form-control input-sm" id="Demeanor" name="Demeanor"  value="" required>
                        </div>

                    </div> 
                    <div class="row">
                    <div class="col-md-3">
                            <label for= "average">average</label>             
                            <input type="average" class="form-control input-sm" id="average" name="average"  value="<?php echo $average ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for= "grade">grade</label>             
                            <input type="grade" class="form-control input-sm" id="grade" name="grade"  value="<?php echo $grade ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-md-5 control-label" for= "idno"></label>  
                        <div class="col-md-8">
                            <button class="btn btn-primary btn-sm" name="save" type="submit" ><span class="fa fa-save fw-fa"></span> Save</button>
                        
                    </div>
                </div>
            </div> 
            </div>
            </form> 
    
            <!-- End of Main Content -->

        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>
</body>
</html>