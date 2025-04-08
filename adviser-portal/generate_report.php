<?php
    session_start();
    include("../includes/connection.php");

    $email = $_SESSION['adviser'];
    $activeSemester = $_SESSION['semester'];
    $activeSchoolYear = $_SESSION['schoolYear'];
    
    // Check if dept_sec is set and is an array
    if (isset($_SESSION['dept_sec']) && is_array($_SESSION['dept_sec']) && count($_SESSION['dept_sec']) > 0) {
        // Create placeholders dynamically for the number of sections
        $placeholders = implode(',', array_fill(0, count($_SESSION['dept_sec']), '?'));
        $query = "SELECT * FROM studentinfo WHERE department= ? AND course= ? AND semester = ? AND school_year = ? AND section IN ($placeholders) ORDER BY section ASC, lastName ASC";

        // Prepare the statement
        $stmt = $connect->prepare($query);

        // Merge department, course, and section values
        $params = array_merge([$_SESSION['dept_adv'], $_SESSION['dept_crs'], $activeSemester, $activeSchoolYear], $_SESSION['dept_sec']);

        // Define parameter types
        $types = str_repeat('s', count($params));

        // Bind the parameters dynamically
        $stmt->bind_param($types, ...$params);

        // Execute the statement
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                // Fetch all rows
            } else {
                echo "No results found for the given criteria.";
            }
        } else {
            echo "SQL Error: " . $stmt->error;
        }
    } else {
        echo "No sections found for the adviser.";
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>Adviser Portal</title>
        <?php include("embed.php"); ?>
    </head>
    <body id="page-top">
        <div class="wrapper">
            <aside id="sidebar" class="expand">
                <?php include('../elements/adv_sidebar.php') ?>
            </aside>
            <div class="main">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                    <!-- Title -->
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Generate Grades</h4>
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                <?php (isset($_SESSION['adviser'])) ?> <?php echo $_SESSION['adviser']; ?></span>
                                <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
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

                <div class="col-lg-12 mb-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-3">
                                    <h6 class="m-0 font-weight-bold text-dark">STUDENT INFORMATION</h6>
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col">
                                            <?php
                                                // Assuming $connect is your mysqli connection object
                                                $getsections = "SELECT section FROM listadviser WHERE email = '$email'";
                                                $sections = mysqli_query($connect, $getsections);

                                                // Check if query was successful
                                                if ($sections) {
                                                    echo '<div class="input-group input-group-sm">';
        
                                                    // Dropdown
                                                    echo '<select name="sections" id="sections" class="form-control form-control-sm">';
                                                    echo '<option value="All Sections">All Sections</option>';
                                                    while ($sect = mysqli_fetch_assoc($sections)) {
                                                        echo '<option value="' . $sect['section'] . '">' . $sect['section'] . '</option>';
                                                    }
                                                    echo '</select>
<form method="POST" action="export_pdf.php" target="_blank" style="display:inline-block; margin-left: 10px;">
    <input type="hidden" name="selected_section" id="selected_section_input">
    <button type="submit" class="btn btn-primary"><i class="fa fa-file-pdf-o"></i> Export PDF</button>
</form>
';

                                                    echo '</div>'; // Close input-group
                                                } else {
                                                    echo "Error: " . mysqli_error($connect);
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="studentTable" class="table table-hover" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th colspan="8" class="text-center font-weight-bold border-0 pt-1"><input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Search..."></th>
                                        </tr>
                                        <tr>
                                            <th scope="col" class="small">StudentID</th>
                                            <th scope="col" class="small">Name</th>
                                            <th scope="col" class="small">Department</th>
                                            <th scope="col" class="small">Course-Section</th>
                                            <th scope="col" class="small">Status</th>
                                            <th scope="col" class="small" width="10%">Adviser Grade</th>
                                            <th scope="col" class="small" width="12%">Company Grade</th>
                                            <th scope="col" class="small" width="10%">Final Grade</th>
                                            <th scope="col" class="small">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $semester = $row['semester'];
                                                    $schoolYear = $row['school_year'];
                                        ?>
                                        <?php            
                                                    echo "<tr>";
                                                        echo "<td>" . $row['studentID'] . "</td>";
                                                        echo "<td>" . $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'] . "</td>";
                                                        echo "<td>" . $row['department'] . "</td>";
                                                        echo "<td>" . $row['course'] . '-' . $row['section'] . "</td>";
                                                        echo "<td>" . $row['status'] . "</td>";

                                                        $adviserCriteriaGrouped = [];
                                                        $companyCriteriaGrouped = [];

                                                        $adviserGradeStmt = $connect->prepare("SELECT * FROM adviser_student_grade WHERE email = ? AND semester = ? AND schoolYear = ?");
                                                        $adviserGradeStmt->bind_param("sss", $row['email'], $activeSemester, $activeSchoolYear);
                                                        $adviserGradeStmt->execute();
                                                        $adviserGradeResult = $adviserGradeStmt->get_result();

                                                        while ($rowAdviserGrade = $adviserGradeResult->fetch_assoc()) {
                                                            $adviserGrade = $rowAdviserGrade['finalGrade'];
                                                            $criteria = json_decode($rowAdviserGrade['criteria'], true);
                                                            $grade = json_decode($rowAdviserGrade['grade'], true);

                                                            foreach ($criteria as $criterion) {
                                                                $name = $criterion['criteria'];
                                                                $percentage = $criterion['percentage'];
                                                                $score = isset($grade[$name]) ? $grade[$name] : 0;
                                                                
                                                                $adviserCriteriaGrouped[] = [
                                                                    'adviserCriteria' => $name,
                                                                    'adviserPercentage' => $percentage,
                                                                    'adviserDescription' => $criterion['description'],
                                                                    'score' => $score
                                                                ];
                                                            }
                                                        }

                                                        echo "<td align=\"center\">" . $adviserGrade . "</td>";

                                                        $companyGradeStmt = $connect->prepare("SELECT * FROM student_grade WHERE email = ? AND semester = ? AND schoolYear = ?");
                                                        $companyGradeStmt->bind_param("sss", $row['email'], $activeSemester, $activeSchoolYear);
                                                        $companyGradeStmt->execute();
                                                        $companyGradeResult = $companyGradeStmt->get_result();

                                                        while ($rowCompanyGrade = $companyGradeResult->fetch_assoc()) {
                                                            $companyGrade = $rowCompanyGrade['finalGrade'];
                                                            $criteria = json_decode($rowCompanyGrade['criteria'], true);
                                                            $grade = json_decode($rowCompanyGrade['grade'], true);

                                                            foreach ($criteria as $criterion) {
                                                                $name = $criterion['criteria'];
                                                                $percentage = $criterion['percentage'];
                                                                $score = isset($grade[$name]) ? $grade[$name] : 0;
                                                                
                                                                $companyCriteriaGrouped[] = [
                                                                    'companyCriteria' => $name,
                                                                    'companyPercentage' => $percentage,
                                                                    'companyDescription' => $criterion['description'],
                                                                    'score' => $score
                                                                ];
                                                            }
                                                        }

                                                        echo "<td align=\"center\">" . $companyGrade . "</td>";

                                                        $finalizedGradeQuery = "SELECT * FROM grading_rubics WHERE department = ? AND semester = ? AND schoolYear = ?";
                                                        $stmt = $connect->prepare($finalizedGradeQuery);
                                                        $stmt->bind_param("sss", $row['department'], $semester, $schoolYear);
                                                        $stmt->execute();
                                                        $gradingResult = $stmt->get_result();
                                                        $gradingInfo = $gradingResult->fetch_assoc();

                                                        // Assuming 'adviserWeight' and 'companyWeight' are columns in the grading_rubics table
                                                        if ($gradingInfo) {
                                                            $adviserWeight = $gradingInfo['adviserWeight'];
                                                            $companyWeight = $gradingInfo['companyWeight'];
                                                        } else {
                                                            // Handle the case where no grading information is available
                                                            $adviserWeight = 0;  // Set default values or handle the error
                                                            $companyWeight = 0;  // Set default values or handle the error
                                                            $finalizedGrade = 0; // Set default grade or handle the error
                                                        }

                                                        $finalizedGrade = ($adviserGrade * ($adviserWeight / 100)) + ($companyGrade * ($companyWeight / 100));

                                                        echo "<td align=\"center\">" . $finalizedGrade . "</td>";
                                                        echo "<td>
                                                                    <a href=\"#\" 
                                                                        class=\"btn btn-primary btn-sm editBtn\" 
                                                                        data-toggle=\"modal\" 
                                                                        data-target=\"#editModal\"
                                                                        data-adviser='" . json_encode($adviserCriteriaGrouped, JSON_HEX_APOS | JSON_HEX_QUOT) . "'
                                                                        data-company='" . json_encode($companyCriteriaGrouped, JSON_HEX_APOS | JSON_HEX_QUOT) . "'
                                                                        data-advisergrade=\"" . htmlspecialchars($adviserGrade) . "\"
                                                                        data-companygrade=\"" . htmlspecialchars($companyGrade) . "\"
                                                                        data-finalgrade=\"" . htmlspecialchars($finalizedGrade) . "\"
                                                                        data-studentID=\"" . htmlspecialchars($row['studentID']) . "\">
                                                                        <i class=\"fa fa-edit fw-fa\"></i> Edit
                                                                    </a>
                                                                </td>";

                                                    echo "</tr>";
                                                }
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <form action="functions/update_final_grade.php" method="POST">
                                <input type="hidden" name="studentID" id="studentID" value="<?php $row['studentID'] ;?>">
                                <div class="modal-header">
                                    <h5>Edit Grades</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <!-- Adviser Evaluation -->
                                        <div class="col-md-6">
                                            <h6>Adviser Evaluation</h6>
                                            <div id="adviserCriteriaContainer"></div>
                                        </div>
                                        <!-- Company Evaluation -->
                                        <div class="col-md-6">
                                            <h6>Company Evaluation</h6>
                                            <div id="companyCriteriaContainer"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-primary btn-sm" type="submit" id="submitBtn">
                                        <span class="fa fa-save fw-fa"></span> Save
                                    </button>
                                    <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>

        <script>
            $(document).on('click', '.editBtn', function () {
                const adviserData = JSON.parse($(this).attr('data-adviser'));
                const companyData = JSON.parse($(this).attr('data-company'));

                // Clear previous content in the containers
                $('#adviserCriteriaContainer').empty();
                $('#companyCriteriaContainer').empty();

                // Function to initialize event listeners for input elements in both containers
                function initializeInputs(containerId) {
                    const criteriaInputs = document.querySelectorAll(`#${containerId} .custom-number-input`);
                    criteriaInputs.forEach(function(input) {
                        input.addEventListener('input', function() {
                            enforceMaxLimit(input, input.id.replace('displayValue', ''));
                            updateTotal(containerId);
                        });
                    });
                }

                // Function to update the total grade dynamically
                function updateTotal(containerId) {
                    const criteriaInputs = document.querySelectorAll(`#${containerId} .custom-number-input`);
                    let total = 0;
                    criteriaInputs.forEach(function(input) {
                        const value = parseInt(input.value);
                        if (!isNaN(value)) {
                            total += value;
                        }
                    });

                    const totalGradeInput = document.querySelector(`#${containerId} .totalGrade`);
                    if (totalGradeInput) {
                        totalGradeInput.value = total;
                    }
                }

                // Function to ensure input values stay within the allowed range
                function enforceMaxLimit(input, criteriaId) {
                    const maxValue = parseInt(input.max);
                    let value = parseInt(input.value);
                    if (value < 0) {
                        input.value = 0;
                    } else if (value > maxValue) {
                        input.value = maxValue;
                    }
                    // Update the hidden value if necessary
                    updateValue(value, criteriaId);
                }

                // Function to update the hidden input value when criteria changes
                function updateValue(value, criteriaId) {
                    const hiddenInput = document.getElementById(`hiddenInputForCriteria${criteriaId}`);
                    if (hiddenInput) {
                        hiddenInput.value = value;
                    }
                }

                // Append Adviser data to the adviser container
                adviserData.forEach(item => {
                    $('#adviserCriteriaContainer').append(`
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>${item.adviserCriteria}</strong>
                                <div class="input-group input-group-sm mb-2 mr-sm-2" style="max-width: 200px;">
                                    <input type="number" required class="form-control custom-number-input" min="0" max="${item.adviserPercentage}" value="${item.score}" name="adviserScore[${item.adviserCriteria}]" id="displayValue${item.id}" data-percentage="${item.adviserPercentage}">
                                    <div class="input-group-append">
                                        <div class="input-group-text">${item.adviserPercentage}%</div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="adviserCriteria[${item.adviserCriteria}][criteria]" value="${item.adviserCriteria}">
                            <input type="hidden" name="adviserCriteria[${item.adviserCriteria}][description]" value="${item.adviserDescription}">
                            <input type="hidden" name="adviserCriteria[${item.adviserCriteria}][percentage]" value="${item.adviserPercentage}">

                            <div class="description-row">
                                <small>${item.adviserDescription}</small>
                            </div>
                        </div>
                    `);
                });

                // Append Company data to the company container
                companyData.forEach(item => {
                    $('#companyCriteriaContainer').append(`
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>${item.companyCriteria}</strong>
                                <div class="input-group input-group-sm mb-2 mr-sm-2" style="max-width: 200px;">
                                    <input type="number" required class="form-control custom-number-input" min="0" max="${item.companyPercentage}" value="${item.score}" name="companyScore[${item.companyCriteria}]" id="displayValue${item.id}" data-percentage="${item.companyPercentage}">
                                    <div class="input-group-append">
                                        <div class="input-group-text">${item.companyPercentage}%</div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="companyCriteria[${item.companyCriteria}][criteria]" value="${item.companyCriteria}">
                            <input type="hidden" name="companyCriteria[${item.companyCriteria}][description]" value="${item.companyDescription}">
                            <input type="hidden" name="companyCriteria[${item.companyCriteria}][percentage]" value="${item.companyPercentage}">

                            <div class="description-row">
                                <small>${item.companyDescription}</small>
                            </div>
                        </div>
                    `);
                });

                // Append Final Grade input for Adviser
                $('#adviserCriteriaContainer').append(`
                    <hr>
                    <div class="form-group d-flex align-items-center">
                        <label for="adviserGrade" class="mb-0 mr-2" style="min-width: 160px;"><strong>Adviser Final Grade:</strong></label>
                        <div class="input-group" style="max-width: 120px;">
                            <input type="number" 
                                step="0.01" min="0" max="100" 
                                class="form-control totalGrade" 
                                oninput="distributeTotalGrade(); updateFinalGrade();" 
                                id="adviserGrade" 
                                name="adviserGrade" 
                                value="<?= $adviserGrade ?>" 
                                data-weight="<?= $adviserWeight ?>">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                `);

                // Append Final Grade input for Company
                $('#companyCriteriaContainer').append(`
                    <hr>
                    <div class="form-group d-flex align-items-center">
                        <label for="companyGrade" class="mb-0 mr-2" style="min-width: 160px;"><strong>Company Final Grade:</strong></label>
                        <div class="input-group" style="max-width: 120px;">
                            <input type="number" 
                                step="0.01" min="0" max="100" 
                                class="form-control totalGrade" 
                                oninput="distributeTotalGrade(); updateFinalGrade();" 
                                id="companyGrade" 
                                name="companyGrade"
                                value="<?= $companyGrade ?>" 
                                data-weight="<?= $companyWeight ?>">
                            <div class="input-group-append">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                `);

                // Set existing grade values
                $('#adviserGrade').val($(this).data('advisergrade'));
                $('#companyGrade').val($(this).data('companygrade'));
                $('#finalGrade').text($(this).data('finalgrade') + '%');
                $('#studentID').val($(this).data('studentid'));

                // Initialize inputs for both Adviser and Company containers
                initializeInputs('adviserCriteriaContainer');
                initializeInputs('companyCriteriaContainer');

                // Handle distributeTotalGrade
                function distributeTotalGrade(event) {
                    // Get the total grade input field (either adviserGrade or companyGrade)
                    const totalGradeInput = event.target;  // This is the input that triggered the event
                    const containerId = $(totalGradeInput).closest('.form-group').parent().attr('id');  // Get the parent container ID (either adviserCriteriaContainer or companyCriteriaContainer)

                    // Get the total grade value from the input field
                    let totalGradeValue = $(totalGradeInput).val().trim();
                    if (totalGradeValue === "") {
                        totalGradeValue = "0";
                        $(totalGradeInput).val(totalGradeValue);
                        $(this).siblings('.custom-number-input').val("0");
                        return;
                    }

                    let totalGrade = parseFloat(totalGradeValue);
                    totalGrade = Math.max(0, Math.min(100, totalGrade));  // Ensure the grade is between 0 and 100
                    $(totalGradeInput).val(totalGrade);

                    // Logic to distribute points across criteria
                    let remainingPoints = totalGrade;
                    const criteriaInputs = $(`#${containerId} .custom-number-input`);
                    const criteriaPoints = [];

                    // First pass: Calculate points for each criterion based on its percentage
                    criteriaInputs.each(function() {
                        const maxValue = parseInt($(this).data('percentage'));  // Get the max percentage from data-attribute
                        let pointsForThisCriterion = Math.floor((maxValue / 100) * totalGrade);  // Calculate based on percentage
                        pointsForThisCriterion = Math.min(pointsForThisCriterion, maxValue);  // Ensure it doesn't exceed max value
                        criteriaPoints.push(pointsForThisCriterion);
                        remainingPoints -= pointsForThisCriterion;
                    });

                    // Second pass: Distribute any remaining points
                    if (remainingPoints > 0) {
                        criteriaInputs.each(function(index) {
                            if (remainingPoints > 0) {
                                let points = criteriaPoints[index];
                                const maxValue = parseInt($(this).data('percentage'));
                
                                // Add remaining points without exceeding the max value
                                if (points < maxValue) {
                                    let additionalPoints = Math.min(remainingPoints, maxValue - points);
                                    points += additionalPoints;
                                    criteriaPoints[index] = points;  // Update points for this criterion
                                    remainingPoints -= additionalPoints;  // Subtract from remaining points
                                }
                            }
                        });
                    }

                    // Update the input fields and hidden values with the calculated points
                    criteriaInputs.each(function(index) {
                        const points = criteriaPoints[index];
                        $(this).val(points);
                        $(this).siblings('input[type="hidden"]').val(points);  // Update the hidden input with the calculated points
                    });
                }

                // Bind the event listeners for the total grade inputs (Adviser and Company)
                $(document).ready(function() {
                    // Bind the event listener for the adviser final grade
                    $('#adviserCriteriaContainer .totalGrade').on('input', function(event) {
                        distributeTotalGrade(event);
                    });

                    // Bind the event listener for the company final grade
                    $('#companyCriteriaContainer .totalGrade').on('input', function(event) {
                        distributeTotalGrade(event);
                    });
                });
            });
        </script>

        <script>
        document.getElementById('sections').addEventListener('change', function() {
            var selectedSection = this.value;
            filterTable(selectedSection);
        });

            function filterTable(section) {
            var table = document.getElementById('studentTable');
            var tr = table.getElementsByTagName('tr');

            for (var i = 0; i < tr.length; i++) {
                var td = tr[i].getElementsByTagName('td')[3]; // Column index for course-section
                if (td) {
                    var txtValue = td.textContent || td.innerText;
                    if (section === "All Sections" || txtValue.indexOf(section) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        // Function to filter table based on search input
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#studentTable tbody tr').filter(function() {
                var rowText = $(this).text().toLowerCase();
                var isVisible = rowText.indexOf(value) > -1;
                $(this).toggle(isVisible);
            });

            // Check if any rows are visible after filtering
            var visibleRows = $('#studentTable tbody tr:visible').length;
            if (visibleRows === 0) {
                $('#noResult').show(); // Display "No Results" message
            } else {
                $('#noResult').hide(); // Hide "No Results" message if there are visible rows
            }
        });
        </script>
    
<script>
    // Update hidden input when section changes
    document.getElementById('sections').addEventListener('change', function () {
        document.getElementById('selected_section_input').value = this.value;
    });

    // Set default section on page load
    window.addEventListener('DOMContentLoaded', function () {
        var selected = document.getElementById('sections').value;
        document.getElementById('selected_section_input').value = selected;
    });
</script>

</body>
</html>