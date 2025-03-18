<?php
session_start();
include("../includes/connection.php");

if (isset($_GET['studentId']) && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $studentId = $_GET['studentId'];

    // Fetch companyName and jobrole
    $studQuery = "SELECT companyName, jobrole FROM company_info WHERE studentID = ?";
    $stmtStud = $connect->prepare($studQuery);
    $stmtStud->bind_param("s", $studentId);
    $stmtStud->execute();
    $studResult = $stmtStud->get_result();

    if ($studResult->num_rows > 0) {
        $row = $studResult->fetch_assoc();
        $companyName = $row['companyName'];
        $jobrole = $row['jobrole'];

        // Fetch criteria based on companyName and jobrole
        $criteriaQuery = "SELECT * FROM criteria_list_view WHERE company = ? AND jobrole = ?";
        $stmtCriteria = $connect->prepare($criteriaQuery);
        $stmtCriteria->bind_param("ss", $companyName, $jobrole);
        $stmtCriteria->execute();
        $resultCriteria = $stmtCriteria->get_result();

        if ($resultCriteria->num_rows > 0) {
            echo "<ul class='list-group'>";
            while ($row = $resultCriteria->fetch_assoc()) {
                $criteriaData = json_decode($row['criteria'], true);
                foreach ($criteriaData as $criteriaItem) {
                    echo "<li class='list-group-item'>";
                    echo "<strong>" . htmlspecialchars($criteriaItem['companyCriteria']) . ":</strong> ";
                    echo htmlspecialchars($criteriaItem['companyDescription']);
                    echo " (" . htmlspecialchars($criteriaItem['companyPercentage']) . "%)";
                    echo "</li>";
                }
            }
            echo "</ul>";
        } else {
            echo "<p class='text-center text-warning'>No criteria found for this student.</p>";
        }
    } else {
        echo "<p class='text-center text-danger'>Invalid student selection.</p>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>INDUSTRY PARTNER PORTAL</title>
        <?php include("embed.php"); ?>
    </head>
    <body id="page-top">
        <div id="wrapper">
            <aside>
                <?php include('../elements/ip_sidebar.php') ?>
            </aside>
            <div class="main">
                <!--  NAVIGATION BAR HERE              
                  
                
                -->
                <div id="content" class="py-4 px-4">
                    <div class="col-lg-12 mb-4">
                        <?php
                        $queryIP = "SELECT companyCode FROM company_info WHERE trainerEmail = ?";
                        $stmtIP = $connect->prepare($queryIP);
                        $stmtIP->bind_param("s", $_SESSION['IndustryPartner']);
                        $stmtIP->execute();
                        $resultIP = $stmtIP->get_result();

                        if (!$resultIP) {
                            die("Query failed: " . $connect->error);
                        }

                        // Collect the company codes into an array
                        $companyCodes = array();

                        // Iterate over each companyCode
                        while ($rowIP = $resultIP->fetch_assoc()) {
                            $companyCodes[] = $rowIP['companyCode'];
                        }

                        if (!empty($companyCodes)) {
                            $companyCode = $companyCodes[0];
                        } else {
                            die("No company codes found for the trainer's email.");
                        }
                        $allStudents = [];

                        foreach ($companyCodes as $companyCode) {

                            // Fetch students who do not have records in the student_grade table for the current companyCode
                            $queryStudents = "SELECT * FROM studentinfo s WHERE s.companyCode = ? AND s.status = 'Deployed'
                                AND NOT EXISTS (
                                SELECT 1 FROM student_grade sg
                                WHERE sg.studentID = s.studentID
                            )";
                            $stmtStud = $connect->prepare($queryStudents);
                            $stmtStud->bind_param("s", $companyCode);
                            $stmtStud->execute();
                            $resultStud = $stmtStud->get_result();

                            if (!$resultStud) {
                                die("Query failed: " . $connect->error);
                            }

                            // Collect all students into the $allStudents array
                            while ($rowStud = $resultStud->fetch_assoc()) {
                                $studentName = $rowStud['lastname'] . ", " . $rowStud['firstname'] . " " . substr($rowStud['middlename'], 0, 1) . ".";
                                $allStudents[$rowStud['studentID']] = $studentName;
                            }
                        }
                        ?>

                        <form id="criteriaForm" method="post">
                            <!-- Illustrations -->
                            <div class="card shadow mb-4">
                                <div class="m-4">
                                    <div class="px-3">
                                        <label for="studentNameDropdown"><b>Select a Student:</b></label>
                                        <select name="studentId" id="studentNameDropdown" class="form-select mb-3" required>
                                            <option value="" selected disabled>Select a student...</option>
                                            <?php
                                            // Generate select options from the $allStudents array
                                            foreach ($allStudents as $studentId => $studentName) {
                                                echo "<option data-id='" . $studentId . "' value='" . $studentId . "'>" . $studentName . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div id="criteriaContainer">
                                                <p class="text-center p-4">Criterias will be displayed here</p>

                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <input type="hidden" name="studentId" id="studentId" value="">
                                                <input type="hidden" name="criteriaData" id="criteriaData" value="">

                                                <button type="submit" class="btn btn-success">
                                                    <span class="fas fa-save fw-fa"></span> Submit Grade
                                                </button>
                                                <div class="col-4">
                                                    <label class="sr-only" for="totalGrade">Total Grade</label>
                                                    <div class="input-group mb-2 mr-sm-2">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">Total</div>
                                                        </div>
                                                        <input type="number" id="totalGrade" class="form-control" min="0" max="100" oninput="distributeTotalGrade()">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!--      SCRIPT                     -->                                
            <script>
                $(document).ready(function() {
    $("#studentNameDropdown").change(function() {
        var studentId = $(this).val();
        
        if (studentId) {
            $.ajax({
                url: window.location.href,
                type: "GET",
                data: { studentId: studentId },
                headers: { 'X-Requested-With': 'XMLHttpRequest' }, // Identify as AJAX request
                success: function(response) {
                    $("#criteriaContainer").html(response);
                },
                error: function() {
                    $("#criteriaContainer").html("<p class='text-center text-danger'>Error fetching criteria.</p>");
                }
            });
        }
    });
});
            </script>      
        </div>
    </body>
</html>