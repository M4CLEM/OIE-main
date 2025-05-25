<?php
    session_start();
    include_once("../includes/connection.php");
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>CIPA ADMIN</title>
        <?php include("embed.php"); ?>
        <link rel="stylesheet" href="../assets/css/new-style.css">
    </head>
    <body id="page-top">
        <div class="wrapper">
            <aside id="sidebar" class="expand">
                <?php include('../elements/cipa_sidebar.php') ?>
            </aside>

            <div class="main">
                <nav class="navbar navbar-expand navbar-light bg-white topbar static-top shadow mb-3">
                    <!-- Dashboard Title -->
                    <h2 class="my-0 mr-auto font-weight-bold text-dark ml-3">Memorandum of Agreements</h2>
                    <?php include('../elements/cipa_navbar_user_info.php') ?>
                </nav>

                <div id="content" class="py-2">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="d-flex align-items-center mb-3">
                                <div class="dropdown mr-3">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdowndept" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span id="selectedDept">Select Department</span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdowndept">
                                        <?php 
                                            $queryDept="SELECT * FROM department_list";
                                            $resultDept=mysqli_query($connect,$queryDept);
                                            while($rowDept=mysqli_fetch_assoc($resultDept)) {
                                        ?>
                                            <li>
                                                <button 
                                                    type="button" 
                                                    class="dropdown-item" 
                                                    onclick="updateButtonDept('<?php echo $rowDept['department'];?>')"
                                                >
                                                    <?php echo $rowDept['department'];?>
                                                </button>
                                            </li>
                                        <?php 
                                            }
                                        ?> 	
                                    </ul>
                                </div>

                                <div class="dropdown mr-3">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdowncourse" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span id="selectedCourse">Select Course</span>
                                    </button>
                                    <ul class="dropdown-menu course-buttons" aria-labelledby="dropdowncourse"></ul>
                                </div>
                            </div>

                            <div class="col-lg-12 mb-4">
                                <div class="card shadow mb-4">
                                    <div class="card-header py-3">
                                        <h4 class="m-0 font-weight-bold text-dark">List of MOAs</h4> 
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive table-bordered">
                                            <table class="table"  width="100%" cellspacing="0">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Company Name</th>
                                                        <th scope="col">Jobrole</th>
                                                        <th scope="col">MOAs</th>
                                                        <th scope="col">Department-Course</th>
                                                        <th scope="col">Date Uploaded</th>
                                                        <th scope="col">Validity Period</th>
                                                        <th scope="col" width="10%">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $documentNames = ["Memorandum of Agreement", "Memorandum of Understanding", "MOA", "MOU"];

                                                        $documentQuery = "SELECT dc.*, ci.*
                                                            FROM documents dc
                                                            INNER JOIN company_info ci 
                                                            ON dc.student_ID = ci.studentID 
                                                                AND dc.semester = ci.semester 
                                                                AND dc.schoolYear = ci.schoolYear
                                                            WHERE dc.document = ?
                                                        ";
                                                        $documentStmt = $connect->prepare($documentQuery);

                                                        $groupedData = [];

                                                        // Step 1: Fetch and group all results
                                                        foreach ($documentNames as $docName) {
                                                            $documentStmt->bind_param("s", $docName);
                                                            $documentStmt->execute();
                                                            $result = $documentStmt->get_result();

                                                            while ($row = $result->fetch_assoc()) {
                                                                $key = $row['companyName'] . '|' . $row['jobrole'];
                                                                if (!isset($groupedData[$key])) {
                                                                    $groupedData[$key] = [
                                                                        'companyName' => $row['companyName'],
                                                                        'jobrole' => $row['jobrole'],
                                                                        'departmentCourse' => $row['department'] . ' - ' . $row['course'],
                                                                        'documents' => [],
                                                                        'date' => $row['date'],
                                                                        'validity' => $row['validity'],
                                                                        // Add any additional fields you want to preserve
                                                                    ];
                                                                }

                                                                // Add document info to the list
                                                                $groupedData[$key]['documents'][] = [
                                                                    'file_name' => $row['file_name'],
                                                                    'file_link' => $row['file_link'],
                                                                    'id' => $row['id'],
                                                                ];
                                                            }
                                                        }

                                                        // Step 2: Display grouped rows
                                                        foreach ($groupedData as $group):
                                                    ?>
                                                        <tr 
                                                            data-department="<?= htmlspecialchars(explode(' - ', $group['departmentCourse'])[0]) ?>" 
                                                            data-course="<?= htmlspecialchars(explode(' - ', $group['departmentCourse'])[1]) ?>"
                                                        >
                                                            <td><?= htmlspecialchars($group['companyName']) ?></td>
                                                            <td><?= htmlspecialchars($group['jobrole']) ?></td>
                                                            <td>
                                                                <?php foreach ($group['documents'] as $doc): ?>
                                                                    <div>
                                                                        <a href="<?= htmlspecialchars($doc['file_link']) ?>" target="_blank" class="file-link" data-link="<?= htmlspecialchars($doc['file_link']) ?>">
                                                                            <?= htmlspecialchars($doc['file_name']) ?>
                                                                        </a>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </td>
                                                            <td><?= htmlspecialchars($group['departmentCourse']) ?></td>
                                                            <td><?= htmlspecialchars($group['date']) ?></td>
                                                            <td>
                                                                <?= !empty($group['validity']) ? htmlspecialchars($group['validity'] . ' Months') : 'Not set' ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                    $documentIDs = array_column($group['documents'], 'id');
                                                                    $documentIDsAttr = htmlspecialchars(implode(',', $documentIDs));
                                                                ?>
                                                                <button 
                                                                    class="btn btn-primary btn-sm assign-due-btn" 
                                                                    data-toggle="modal" 
                                                                    data-target="#validityModal" 
                                                                    data-documentids="<?= $documentIDsAttr ?>"
                                                                >
                                                                    Assign Due
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

        <!-- VALIDITY MODAL -->
        <div class="modal fade" id="validityModal" tabindex="-1" role="dialog" aria-labelledby="validityModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form id="validityForm" action="functions/update_validity.php" method="post" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="validityModalLabel">Add Validity Period</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <span class="text-muted">Note: Duration will be applied to <strong>all</strong> documents in this row.</span>
                        <hr>
                        <input type="hidden" id="documentIDs" name="documentIDs">
                        <div class="form-group">
                            <label class="d-block mb-1" for="validityDuration">Validity Duration</label>
                            <div class="input-group">
                                <input type="number" min="1" name="validityDuration" id="validityDuration" class="form-control" placeholder="Duration" required>
                                <span class="input-group-text">Months</span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../assets/js/sidebarscript.js"></script>

        <script>
            $(function () {
                // 1) When user clicks “Assign Due”
                $(document).on('click', '.assign-due-btn', function () {
                    const ids = $(this).data('documentids');     // e.g. "12,15,18"
                    $('#documentIDs').val(ids);
                    $('#validityDuration').val('');             // clear previous value
                });

                // 2) AJAX submit
                $('#validityForm').on('submit', function (e) {
                    e.preventDefault();                          // stop normal post
                    const $form   = $(this);
                    const formData = $form.serialize();          // doc IDs + duration

                    // basic front-end validation
                    const duration = $('#validityDuration').val();
                    if (!duration || duration <= 0) {
                        alert('Please enter a valid duration (in months).');
                        return;
                    }

                    $.post($form.attr('action'), formData, function (response) {
                        // --- success callback ---
                        // You can return JSON from PHP and check for errors here.
                        // Example assumes success if PHP echoes "OK".
                        if (response.trim() === 'OK') {
                            alert('Validity period saved!');
                            // Optionally refresh just the table or the whole page:
                            location.reload();
                        } else {
                            alert('Server reply: ' + response);
                        }
                        $('#validityModal').modal('hide');
                    }).fail(function (jqXHR, textStatus) {
                        alert('Request failed: ' + textStatus);
                    });
                });
            });

        </script>

        <script>
            function filterTable() {
                const selectedDept = document.getElementById('selectedDept').innerText.trim();
                const selectedCourse = document.getElementById('selectedCourse').innerText.trim();
                const rows = document.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    const rowDept = row.getAttribute('data-department');
                    const rowCourse = row.getAttribute('data-course');

                    const matchDept = (selectedDept === 'Select Department' || rowDept === selectedDept);
                    const matchCourse = (selectedCourse === 'Select Course' || rowCourse === selectedCourse);

                    if (matchDept && matchCourse) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Modify existing dropdown updates to call filter
            function updateButtonDept(department) {
                document.getElementById('selectedDept').innerText = department;
                fetchCourses(department);
                filterTable();  // filter after update
            }

            function updateButtonCourse(course) {
                document.getElementById('selectedCourse').innerText = course;
                filterTable();  // filter after update
            }

            function fetchCourses(department) {
                fetch('functions/get_course.php?department=' + encodeURIComponent(department))
                .then(response => response.json())
                .then(data => {
                    const courseDropdown = document.querySelector('.course-buttons');
                    courseDropdown.innerHTML = '';

                    if (data.length === 0) {
                        courseDropdown.innerHTML = '<li><span class="dropdown-item disabled">No courses available</span></li>';
                        return;
                    }

                    data.forEach(course => {
                        const li = document.createElement('li');
                        li.innerHTML = `<button type="button" class="dropdown-item" onclick="updateButtonCourse('${course}')">${course}</button>`;
                        courseDropdown.appendChild(li);
                    });
                })
                .catch(error => {
                    console.error('Error fetching courses:', error);
                });
            }
        </script>

    </body>
</html>