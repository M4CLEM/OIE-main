<?php
// Start the session to use session variables
session_start();

// Include the database connection
include_once("../includes/connection.php");

// Retrieve the program from the session
$program = $_SESSION['program'];
$coordinatorRole = $_SESSION['coordinator']; // Get coordinator role

// Fetch criteria list based on the program
$result = mysqli_query($connect, "SELECT * FROM criteria_presets WHERE program = '$program'");
if (!$result) {
    die("Query Failed: " . mysqli_error($connect));
}

// Fetch the department corresponding to the program from the course_list database
$departmentQuery = mysqli_query($connect, "SELECT department FROM course_list WHERE course = '$program'");
if (!$departmentQuery) {
    die("Query Failed: " . mysqli_error($connect));
}

// Fetch the department from the result
$departmentRow = mysqli_fetch_assoc($departmentQuery);
$department = $departmentRow['department']; // Ensure 'department' is a valid field in the table

// Fetch company names and job roles filtered by the department from the companylist database
$companyQuery = mysqli_query($connect, "SELECT companyName, jobrole FROM companylist WHERE dept = '$department'");
if (!$companyQuery) {
    die("Query Failed: " . mysqli_error($connect));
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
        <aside id="sidebar" class="expand">
            <?php include('../elements/cood_sidebar.php'); ?>
        </aside>
        
        <div class="main">
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Grading</h4>
                <ul class="navbar-nav ml-auto">
                    <div class="topbar-divider d-none d-sm-block"></div>
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php echo $coordinatorRole; ?></span>
                            <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
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
                <form id="gradingForm" method="post" action="functions/submit.php">
    <div class="form-group row">
        <div class="col-md-6">
            <label for="companyDropdown">Company Name</label>
            <select class="form-control" id="companyDropdown" name="companyDropdown">
                <option value="">Select a Company</option>
                <?php while ($row = mysqli_fetch_assoc($companyQuery)) { ?>
                    <option value="<?php echo htmlspecialchars($row['companyName']); ?>" 
                            data-jobrole="<?php echo htmlspecialchars($row['jobrole']); ?>">
                        <?php echo htmlspecialchars($row['companyName']); ?> - 
                        <?php echo htmlspecialchars($row['jobrole']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <!-- Hidden field to store job role -->
        <input type="hidden" id="jobroleInput" name="jobrole">

        <div class="col-md-4">
            <label for="searchCompany">Search</label>
            <input type="text" class="form-control" id="searchCompany" placeholder="Search Company">
        </div>
    </div>
    
    <!-- New Row with 2 Grading Criteria sections -->
    <div id="gradingCriteria" class="form-group row">
        <!-- Company Criteria Section -->
        <div id="companyCriteria" class="col-md-6">
            <h5>Company Criteria</h5>
            <div class="card shadow mb-4">
                <div class="card-body" id="companyCards">
                    <div class="form-group row">
                        <div class="col-md-8">
                            <label for="companyTitle">Grading Criteria Title</label>
                            <select class="form-control grading-title" name="companyTitle[]" required>
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
                            <label for="companyPercentage">Percentage</label>
                            <select class="form-control" name="companyPercentage[]">
                                <?php for ($i = 5; $i <= 100; $i += 5) {
                                    echo "<option value='$i'>$i%</option>";
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="companyDescription">Description</label>
                        <textarea class="form-control description" name="companyDescription[]" rows="3" required></textarea>
                    </div>     
                </div> 
            </div>
            <!-- Only one Add button, placed outside the scrollable section -->
            <button type="button" class="btn btn-primary" id="addCompanyCriteria">+</button>
        </div>

        <?php mysqli_data_seek($result, 0); // Reset result pointer before using again ?>

        <!-- Adviser's Criteria Section -->
        <div id="adviserCriteria" class="col-md-6">
            <h5>Adviser's Criteria</h5>
            <div class="card shadow mb-4">
                <div class="card-body" id="adviserCards">
                    <div class="form-group row">
                        <div class="col-md-8">
                            <label for="adviserTitle">Grading Criteria Title</label>
                            <select class="form-control grading-title" name="adviserTitle[]" required>
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
                            <label for="adviserPercentage">Percentage</label>
                            <select class="form-control" name="adviserPercentage[]">
                                <?php for ($i = 5; $i <= 100; $i += 5) {
                                    echo "<option value='$i'>$i%</option>";
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="adviserDescription">Description</label>
                        <textarea class="form-control description" name="adviserDescription[]" rows="3" required></textarea>
                    </div>
                </div>
            </div>
            <!-- Only one Add button, placed outside the scrollable section -->
            <button type="button" class="btn btn-primary" id="addAdviserCriteria">+</button>
        </div>
    </div>

    <button type="submit" class="btn btn-success">Submit</button>
</form>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/sidebarscript.js"></script>
    
    <script>
        // Search functionality for filtering company dropdown options
        document.getElementById('searchCompany').addEventListener('input', function() {
            var searchValue = this.value.toLowerCase();
            var companyDropdown = document.getElementById('companyDropdown');
            var options = companyDropdown.options;
            
            // Loop through options and hide those that do not match the search value
            for (var i = 1; i < options.length; i++) { // Start from 1 to skip the "Select a Company" option
                var companyName = options[i].text.toLowerCase();
                if (companyName.includes(searchValue)) {
                    options[i].style.display = 'block';
                } else {
                    options[i].style.display = 'none';
                }
            }
        });
        
        document.addEventListener('DOMContentLoaded', function () {
    // Event listener for adding company criteria
    document.getElementById('addCompanyCriteria').addEventListener('click', function() {
        var companyCards = document.getElementById('companyCards');
        var newCompanyCriteria = document.createElement('div');
        newCompanyCriteria.innerHTML = `
            <div class="form-group row">
                <div class="col-md-8">
                    <label for="companyTitle">Grading Criteria Title</label>
                    <select class="form-control grading-title" name="companyTitle[]" required>
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
                    <label for="companyPercentage">Percentage</label>
                    <select class="form-control" name="companyPercentage[]">
                        <?php for ($i = 5; $i <= 100; $i += 5) {
                            echo "<option value='$i'>$i%</option>";
                        } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="companyDescription">Description</label>
                <textarea class="form-control description" name="companyDescription[]" rows="3" required></textarea>
            </div>
        `;
        companyCards.appendChild(newCompanyCriteria);
    });

    // Event listener for adding adviser criteria
    document.getElementById('addAdviserCriteria').addEventListener('click', function() {
        var adviserCards = document.getElementById('adviserCards');
        var newAdviserCriteria = document.createElement('div');
        newAdviserCriteria.innerHTML = `
            <div class="form-group row">
                <div class="col-md-8">
                    <label for="adviserTitle">Grading Criteria Title</label>
                    <select class="form-control grading-title" name="adviserTitle[]" required>
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
                    <label for="adviserPercentage">Percentage</label>
                    <select class="form-control" name="adviserPercentage[]">
                        <?php for ($i = 5; $i <= 100; $i += 5) {
                            echo "<option value='$i'>$i%</option>";
                        } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="adviserDescription">Description</label>
                <textarea class="form-control description" name="adviserDescription[]" rows="3" required></textarea>
            </div>
        `;
        adviserCards.appendChild(newAdviserCriteria);
    });

    // Use event delegation to handle change events on dynamically created select elements
    document.body.addEventListener('change', function(event) {
        if (event.target && event.target.classList.contains('grading-title')) {
            // Get the selected option's description
            var selectedOption = event.target.options[event.target.selectedIndex];
            var description = selectedOption.getAttribute("data-description");

            // Find the corresponding description textarea
            var descriptionTextarea = event.target.closest('div').nextElementSibling.querySelector('.description');
            if (descriptionTextarea) {
                descriptionTextarea.value = description;
            }
        }
    });
});


        // SEARCH BOX FUNCTIONALITY
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById("searchCompany");
            const companyDropdown = document.getElementById("companyDropdown");
            const companyList = [];
            
            for (let i = 1; i < companyDropdown.options.length; i++) {
                companyList.push({
                    name: companyDropdown.options[i].text,
                    value: companyDropdown.options[i].value
                });
            }

            const suggestionBox = document.createElement("div");
            suggestionBox.setAttribute("id", "suggestionBox");
            suggestionBox.style.position = "absolute";
            suggestionBox.style.zIndex = "1000";
            suggestionBox.style.background = "#fff";
            suggestionBox.style.border = "1px solid #ccc";
            suggestionBox.style.width = searchInput.offsetWidth + "px";
            suggestionBox.style.display = "none";
            searchInput.parentNode.appendChild(suggestionBox);

            searchInput.addEventListener("input", function () {
                const searchValue = this.value.toLowerCase();
                suggestionBox.innerHTML = "";
                if (searchValue.length > 0) {
                    const filteredCompanies = companyList.filter(company => 
                        company.name.toLowerCase().includes(searchValue)
                    );

                    filteredCompanies.forEach(company => {
                        const suggestionItem = document.createElement("div");
                        suggestionItem.textContent = company.name;
                        suggestionItem.style.padding = "5px";
                        suggestionItem.style.cursor = "pointer";
                        suggestionItem.addEventListener("click", function () {
                            searchInput.value = company.name;
                            companyDropdown.value = company.value;
                            suggestionBox.style.display = "none";
                        });
                        suggestionBox.appendChild(suggestionItem);
                    });

                    suggestionBox.style.display = "block";
                } else {
                    suggestionBox.style.display = "none";
                }
            });

            document.addEventListener("click", function (e) {
                if (!searchInput.contains(e.target) && !suggestionBox.contains(e.target)) {
                    suggestionBox.style.display = "none";
                }
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            document.addEventListener("change", function (event) {
                if (event.target.classList.contains("grading-title")) {
                    let selectedOption = event.target.options[event.target.selectedIndex];
                    let description = selectedOption.getAttribute("data-description");
            
                    // Find the nearest textarea to update its value
                    let cardBody = event.target.closest(".card-body");
                    if (cardBody) {
                        let descriptionTextarea = cardBody.querySelector(".description");
                        if (descriptionTextarea) {
                            descriptionTextarea.value = description;
                }
            }
        }
    });
});

        document.getElementById('companyDropdown').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var jobrole = selectedOption.getAttribute('data-jobrole');
            document.getElementById('jobroleInput').value = jobrole;
    });

    </script>
</body>
</html>
