<?php
    session_start();
    include_once("../includes/connection.php");

    $dept = $_GET['dept'];

    $query="select * from department_list where department='{$dept}'";
    $result=mysqli_query($connect,$query);
    $rows=mysqli_fetch_assoc($result);
    
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

        <!--Main content-->
        <div class="main py-3">
            <div id="wrapper">
                <div class="container-xxl py-8 ml-3 mt-3 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="container">
                        <div class="row gy-5 gx-4">
                            <div class="col-lg-12">
                                <div class="d-flex align-items-center">
                                    <div class="text-start mb-2">
                                        <a href="managecollege.php" class="btn btn-primary"><i class="fa fa-arrow-circle-left" aria-hidden="true"></i></a>
                                        <br><br><h3 class="mb-3"><?php echo $rows['department_title'];?></h3>
                                        <h1 class="text-truncate me-0"> <?php echo $rows['department'];?></h1>
                                    </div>
                                </div>

                                <form>
                                    <div class="row g-3 mb-5">
                                        <div class="col-4">
                                            <a title="Edit" href="#" class="btn btn-primary w-40" data-toggle="modal" data-target="#editModal"> <i class="fa fa-edit fw-fa"></i> Edit</a>
                                            <a title="Delete" href="functions/department-delete-process.php?dept=<?php echo $rows['department']; ?>" class="btn btn-primary w-40 btn-danger ml-3"><i class="fa fa-trash"></i> Delete</a>
                                        </div>
                                    </div>
                                </form>
                                
                                <div class="col-lg-12 mb-4 mt-4">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3 d-flex justify-content-between">
                                            <h4 class="m-0 font-weight-bold text-dark">List of Courses</h4> 

                                           

                                            <a href='#' class='btn btn-primary btn-sm' data-toggle='modal' data-target='#addModal'>  
                                                <i class='fa fa-plus-circle fw-fa'></i> Add Course
                                            </a>
                                                
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive table-bordered">
                                                    <table class="table" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col" style="width: 50%;">Course Title</th>
                                                                <th scope="col" style="width: 20%;">Course Acronym</th>
                                                                <th scope="col" style="width: 10%;"># of Sections</th>
                                                                <th scope="col" style="width: 10%;">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                            <?php 

                                                            $querySection="SELECT * FROM course_list WHERE department='{$dept}'";
                                                            $resultSection=mysqli_query($connect,$querySection);
                                                                    
                                                            while($rowsSection=mysqli_fetch_assoc($resultSection))
                                                            {

                                                                $queryNumSection = "SELECT COUNT(*) as section_count FROM sections_list WHERE course = ? AND department = ?";
                                                                $stmtNumSection = $connect->prepare($queryNumSection);
                                                                $stmtNumSection->bind_param('ss', $rowsSection['course'], $dept);
                                                                $stmtNumSection->execute();

                                                                $resultNumSection = $stmtNumSection->get_result();
                                                                $rowNumSection = $resultNumSection->fetch_assoc();

                                                                echo "<tr>
                                                                        <td>
                                                                            <p>{$rowsSection['course_title']}</p>
                                                                        </td>
                                                                        <td>
                                                                            <p>{$rowsSection['course']}</p>
                                                                        </td>

                                                                        <td>
                                                                            <p>{$rowNumSection['section_count']}</p>
                                                                        </td>

                                                                        <td>
                                                                            <div class='d-flex'>

                                                                                <button type='button' class='btn btn-primary edit-course-btn' data-toggle='modal' data-target='#editCourseModal' data-id='{$rowsSection['id']}'>
                                                                                    <i class='fas fa-edit'></i>
                                                                                </button>

                                                                                <a href='functions/course-delete-process.php?dept={$rows['department']}&course={$rowsSection['course']}' class='btn btn-primary w-40 btn-danger ml-3'>
                                                                                    <i class='fa fa-trash'></i> 
                                                                                </a>

                                                                            </div>
                                                                        </td>
                                                                    </tr>";
                                                            }

                                                            ?>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            <div>
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

    <!-- Edit Course Modal -->
    <div class="modal fade" id="editCourseModal" tabindex="-1" role="dialog" aria-labelledby="editCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editCourseForm" action="functions/course-edit-process.php?course=" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCourseModalLabel">Edit Course</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="form-group">
                            <div class="col-md-10">
                                <label for="editCourseTitle">Course Title:</label>   
                                <input class="form-control input-sm" id="editCourseTitle" name="editCourseTitle" type="text" autocomplete="none"">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-10">
                                <label for="editCourseAcr">Course Acronym:</label>  
                                <input class="form-control input-sm" id="editCourseAcr" name="editCourseAcr" type="text" required autocomplete="none""></input>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-5">
                                <label for="editNumSections">Number of Sections:</label>
                                <select class="form-control input-sm" id="editNumSections" name="editNumSections">
                                    <?php for ($i =  1; $i <=  27; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary btn-sm" name="edit" type="submit"><span class="fa fa-save fw-fa"></span> Save</button>
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Course Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="functions/course-add-process.php?dept=<?php echo $dept ?>" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Add Course</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="col-md-10">
                                <label for="courseTitle">Course Title:</label>   
                                <input class="form-control input-sm" id="courseTitle" name="courseTitle" type="text" autocomplete="none">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-10">
                                <label for="courseAcr">Course Acronym:</label>  
                                <input class="form-control input-sm" id="courseAcr" name="courseAcr" type="text" required autocomplete="none"></input>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-5">
                                <label for="numSections">Number of Sections:</label>
                                <select class="form-control input-sm" id="numSections" name="numSections">
                                    <?php for ($i =  1; $i <=  27; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary btn-sm" name="add" type="submit"><span class="fa fa-save fw-fa"></span> Add</button>
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- edit Department Modal -->
   <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="functions/department-edit-process.php?id=<?php echo $rows['id']; ?>" method="POST">

                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Department</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                        <div class="col-md-10">
                            <label for= "deptTitle">Department Title:</label>  
                            <input class="form-control input-sm" id="deptTitle" name="deptTitle" type="text" value="<?php echo $rows ['department_title'];?>" autocomplete="none">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-10">
                            <label for="deptAcr">Department Acronym:</label> 
                            <input class="form-control input-sm" id="deptAcr" name="deptAcr" type="text" value="<?php echo $rows ['department'];?>" required  autocomplete="none"></input>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary btn-sm" name="save" type="submit" ><span class="fa fa-save fw-fa"></span> Save</button>
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
            crossorigin="anonymous"></script>
    <script src="../assets/js/sidebarscript.js"></script>

    <script>
        $(document).ready(function() {
            $('.edit-course-btn').on('click', function() {
                var courseId = $(this).data('id'); 
                $.ajax({
                    url: 'functions/get-course-data.php', 
                    type: 'POST',
                    data: { courseId: courseId },
                    success: function(response) {

                        var courseData = JSON.parse(response);
                        $('#editCourseTitle').val(courseData.course_title);
                        $('#editCourseAcr').val(courseData.course);

                        $('#editCourseForm').attr('action', 'functions/course-edit-process.php?courseID=' + courseId + '&dept=<?php echo $dept; ?>' );

                    },
                    error: function(xhr, status, error) {
                        // Handle any errors that occur during the request
                        console.error('Error fetching course data:', error);
                    }
                });
            });
        });
    </script>

</body>
