<?php
// Start the session to use session variables
session_start();

// Include the database connection
include_once("../includes/connection.php");

// Retrieve the program from the session
$program = $_SESSION['program'];

// Fetch the criteria list based on the program
$result = mysqli_query($connect, "SELECT * FROM criteria_presets WHERE program = '$program'");
if (!$result) {
    die("Query Failed: " . mysqli_error($connect)); // Debugging SQL errors
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("../elements/meta.php"); ?>
    <title>OJT COORDINATOR PORTAL</title>
    <?php include("embed.php"); ?>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <aside id="sidebar" class="expand">
            <?php include('../elements/cood_sidebar.php') ?>
        </aside>
        
        <div class="main">
            <!-- Top navigation bar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Grading</h4>
                <ul class="navbar-nav ml-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <!-- User profile dropdown -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php echo $_SESSION['coordinator']; ?></span>
                            <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                            aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#">Profile</a>
                            <a class="dropdown-item" href="#">Settings</a>
                            <a class="dropdown-item" href="#">Activity Log</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="../logout.php">Logout</a>
                        </div>
                    </li>
                </ul>
            </nav>
            
            <div id="content" class="py-2">
                <div class="col-lg-13 m-3">
                    <!-- Grading form -->
                    <form id="gradingForm" method="post" action="functions/submit.php">
                        <div id="gradingCriteria">
                            <div class="card shadow mb-4">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <div class="col-md-8">
                                            <label for="title">Grading Criteria Title</label>
                                            <select class="form-control grading-title" name="title[]" required>
                                                <option value="">Select Criteria</option>
                                                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                                    <option value="<?php echo htmlspecialchars($row['criteria']); ?>" 
                                                            data-description="<?php echo htmlspecialchars($row['description']); ?>">
                                                        <?php echo htmlspecialchars($row['criteria']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="percentage">Percentage</label>
                                            <select class="form-control" name="percentage[]">
                                                <?php for ($i = 5; $i <= 100; $i += 5) {
                                                    echo "<option value='$i'>$i%</option>";
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control description" name="description[]" rows="3" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary" id="addCriteria">+</button>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- External JavaScript libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/sidebarscript.js"></script>
    
    <script>
        // Function to dynamically add new grading criteria
        document.getElementById('addCriteria').addEventListener('click', function() {
            var gradingCriteria = document.getElementById('gradingCriteria');
            var newCriteria = document.createElement('div');
            newCriteria.innerHTML = `
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-8">
                                <label for="title">Grading Criteria Title</label>
                                <select class="form-control grading-title" name="title[]" required>
                                    <option value="">Select Criteria</option>
                                    <?php mysqli_data_seek($result, 0); while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <option value="<?php echo htmlspecialchars($row['criteria']); ?>" 
                                                data-description="<?php echo htmlspecialchars($row['description']); ?>">
                                            <?php echo htmlspecialchars($row['criteria']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="percentage">Percentage</label>
                                <select class="form-control" name="percentage[]">
                                    <?php for ($i = 5; $i <= 100; $i += 5) {
                                        echo "<option value='$i'>$i%</option>";
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control description" name="description[]" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
            `;
            gradingCriteria.appendChild(newCriteria);
        });
        
        // Function to auto-fill the description when a grading title is selected
        document.addEventListener("change", function(event) {
            if (event.target.classList.contains("grading-title")) {
                var selectedOption = event.target.options[event.target.selectedIndex];
                var descriptionField = event.target.closest(".card").querySelector(".description");
                descriptionField.value = selectedOption.getAttribute("data-description") || "";
            }
        });
    </script>
</body>
</html>
