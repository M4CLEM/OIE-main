<?php
// Start a PHP session to access session variables
session_start();

// Include the database connection file from the parent directory
include_once("../includes/connection.php");

// Get the department value stored in the session
$department = $_SESSION['department'];
$activeSemester = $_SESSION['semester'];
$activeSchoolYear = $_SESSION['schoolYear'];

// Query the database to get criteria lists for the current department
// Includes company and job role information from the view
$companyResult = mysqli_query($connect, "SELECT * FROM criteria_list_view WHERE department = '$department' AND semester = '$activeSemester' AND schoolYear = '$activeSchoolYear'");
$adviserResult = mysqli_query($connect, "SELECT * FROM adviser_criteria WHERE department = '$department' AND semester = '$activeSemester' AND schoolYear = '$activeSchoolYear'");


// Query preset criteria templates from the database
$criteriaPresetsQuery = mysqli_query($connect, "SELECT * FROM criteria_presets");
$criteriaPresets = [];
// Convert preset results into an array of [criteria, description] pairs
while ($row = mysqli_fetch_assoc($criteriaPresetsQuery)) {
    $criteriaPresets[] = [
        'criteria' => $row['criteria'],
        'description' => $row['description']
    ];
}

// Initialize an array to group criteria by their ID
$companyCriteriaGrouped = [];
// Process each row from the company criteria results
while ($row = mysqli_fetch_assoc($companyResult)) {
    // Decode the JSON-formatted criteria string into an array
    $criteriaData = json_decode($row['criteria'], true);
    
    // Create a new group entry if it doesn't exist
    if (!isset($companyCriteriaGrouped[$row['id']])) {
        $companyCriteriaGrouped[$row['id']] = [
            'company' => $row['company'],       // Store company name
            'jobrole' => $row['jobrole'],       // Store job role
            'companyCriteria' => []             // Initialize company criteria array
        ];
    }
    
    // Add each criteria item to the group's company criteria array
    foreach ($criteriaData as $companyCriteriaItem) {
        $companyCriteriaGrouped[$row['id']]['companyCriteria'][] = [
            'companyCriteria' => $companyCriteriaItem['companyCriteria'],
            'companyPercentage' => $companyCriteriaItem['companyPercentage'],
            'companyDescription' => $companyCriteriaItem['companyDescription']
        ];
    }
}

$adviserCriteriaGrouped = [];

// Process each row from the adviser criteria results
while ($row = mysqli_fetch_assoc($adviserResult)) {
    $criteriaData = json_decode($row['criteria'], true);

    // Create a new group entry if it doesn't exist
    if (!isset($adviserCriteriaGrouped[$row['id']])) {
        $adviserCriteriaGrouped[$row['id']] = [
            'company' => $row['company'],
            'jobrole' => $row['jobrole'],
            'adviserCriteria' => []             // Initialize adviser criteria array
        ];
    }

    // Add each criteria item to the adviser's criteria array
    foreach ($criteriaData as $adviserCriteriaItem) {
        $adviserCriteriaGrouped[$row['id']]['adviserCriteria'][] = [
            'adviserCriteria' => $adviserCriteriaItem['adviserCriteria'],
            'adviserPercentage' => $adviserCriteriaItem['adviserPercentage'],
            'adviserDescription' => $adviserCriteriaItem['adviserDescription']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include("../elements/meta.php"); ?>
    <title>OJT COORDINATOR PORTAL</title>
    <?php include("embed.php"); ?>
    <style>
        .card-custom {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px;
            margin-bottom: 15px;
        }
        .card-custom h5 {
            margin-bottom: 10px;
            font-weight: bold;
        }
        .jobrole {
            font-size: 14px;
            font-weight: normal;
            color: gray;
        }
        .criteria-item {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .card-custom .actions {
            display: flex;
            justify-content: flex-start;
            gap: 5px;
        }
        .modal-body {
            padding: 20px;
            max-height: 400px;
            overflow-y: auto;
        }

        /* Optional additional styling for textareas */
        .description-textarea {
            min-height: 50px;
            resize: none; /* Disable manual resizing */
        }

        /* Additional styling for individual criterion cards in the modal */
        .card {
            margin-bottom: 15px;
        }

        /* Styling for selected cards */
        .criteria-card.selected-card {
            border: 2px solid #007bff; /* Highlight selected card */
            background-color: #e9f7fe;
        }
    </style>
</head>
<body id="page-top">
    <div id="wrapper">
        <aside id="sidebar" class="expand">
            <?php include('../elements/cood_sidebar.php') ?>
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
                                <?php echo $_SESSION['coordinator']; ?>
                            </span>
                            <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                            aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="#"><i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profile</a>
                            <a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i> Settings</a>
                            <a class="dropdown-item" href="#"><i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i> Activity Log</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout</a>
                        </div>
                    </li>
                </ul>
            </nav>

            <div id="content" class="py-2">
                <div class="col-lg-13 m-3">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <a class="btn btn-primary btn-sm" href="grading.php" style="font-size: 13px;">+ Add Criteria</a> 
                            <a class="btn btn-primary btn-sm" href="grading-rubics.php" style="font-size: 13px;">Grading Rubrics</a>
                        </div>
                        <div class="card-body mb-1">
                            <?php foreach ($companyCriteriaGrouped as $id => $companyData) { ?>
                        <div class="card card-custom">
                            <h5><?php echo $companyData['company']; ?> <span class="jobrole">(<?php echo $companyData['jobrole']; ?>)</span></h5>
    
                    <div class="row">
                        <!-- Company Card -->
                        <div class="col-md-6">
                            <div class="card mb-2">
                                <div class="card-header">
                                    <h6>Company Criteria</h6>
                                </div>
                                <div class="card-body" id="companyCriteria">
                                    <?php foreach ($companyData['companyCriteria'] as $companyCriteriaItem) { ?>
                                        <p class="criteria-item"><strong><?php echo $companyCriteriaItem['companyCriteria']; ?></strong> - <?php echo $companyCriteriaItem['companyPercentage']; ?>%</p>
                                        <p class="text-muted"> <?php echo $companyCriteriaItem['companyDescription']; ?> </p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <!-- Adviser Card -->
                        <div class="col-md-6">
                            <div class="card mb-2">
                                <div class="card-header">
                                    <h6>Adviser Criteria</h6>
                                </div>
                                <div class="card-body" id="adviserCriteria">
                                    <?php if (isset($adviserCriteriaGrouped[$id]['adviserCriteria']) && !empty($adviserCriteriaGrouped[$id]['adviserCriteria'])) { ?>
                                        <?php foreach ($adviserCriteriaGrouped[$id]['adviserCriteria'] as $criteriaItem) { ?>
                                            <p class="criteria-item"><strong><?php echo $criteriaItem['adviserCriteria']; ?></strong> - <?php echo $criteriaItem['adviserPercentage']; ?>%</p>
                                            <p class="text-muted"><?php echo $criteriaItem['adviserDescription']; ?></p>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <p>No adviser criteria found.</p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                                <div class = "card-footer actions">
                                    <a href="modal.php" class="btn btn-primary btn-sm editBtn" 
                                        data-toggle="modal" 
                                        data-target="#editModal"
                                        data-id="<?php echo $id; ?>"
                                        data-company="<?php echo $companyData['company']; ?>"> 
                                        <i class="fa fa-edit fw-fa"></i> Edit
                                    </a> 
                                    <button type="button" class="btn btn-danger btn-sm deleteBtn" 
                                        data-toggle="modal" 
                                        data-target="#deleteModal" 
                                        data-id="<?php echo $id; ?>">
                                        <i class="fa fa-trash fw-fa"></i> Delete
                                    </button>
                                </div>
                        </div>

                            <?php } ?>

                    </div>
                </div>
            </div>
        </div>

            <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this row?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- Larger modal -->
        <div class="modal-content">
            <form id="editForm" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Criteria - <span id="companyName"></span> - <span id="jobrole"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 500px; overflow-y: auto;"> <!-- Scrollable -->
                    <input type="hidden" id="editId" name="editId">
                    
                    <div class="row"> <!-- Two columns -->
                        <div class="col-md-6">
                            <h6>Company Criteria</h6>
                            <div id="companyCriteriaContainer"></div>
                            <button type="button" class="btn btn-sm btn-primary mt-2" id="addCompanyCriteriaBtn">Add Criteria</button>
                        </div>
                        <div class="col-md-6">
                            <h6>Adviser Criteria</h6>
                            <div id="adviserCriteriaContainer"></div>
                            <button type="button" class="btn btn-sm btn-primary mt-2" id="addAdviserCriteriaBtn">Add Criteria</button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="deleteCriteriaBtn" disabled>Delete Selected</button>
                    <button class="btn btn-primary" type="submit">Save</button>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>





<script>
    $(document).ready(function() {
    // Track selected cards for deletion
    var selectedCards = [];

    // PHP-generated criteria presets filtered by department
    var criteriaPresets = <?php
        // Server-side code to fetch criteria presets for current department
        $department = $_SESSION['department'];
        $result = mysqli_query($connect, "SELECT * FROM criteria_presets WHERE department = '$department'");
        $criteriaData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $criteriaData[] = $row;
        }
        echo json_encode($criteriaData, JSON_PRETTY_PRINT);
    ?>;

    // Prevent card selection when interacting with form controls
    $(document).on('click', '.criteria-card .form-control', function(event) {
        event.stopPropagation(); // Stop event bubbling to parent elements
    });

    // Edit button click handler
    $('.editBtn').click(function() {
        // Get data attributes from clicked button
        var id = $(this).data('id');
        var company = $(this).data('company');
        var jobrole = $(this).data('jobrole'); // Adding job role
        var card = $(this).closest('.card-custom');
        
        // Extract existing criteria details for Company
        var companyCriteriaItems = card.find('#companyCriteria .criteria-item');
        var companyDescriptionItems = card.find('#companyCriteria .text-muted');
        
        // Extract existing criteria details for Adviser
        var adviserCriteriaItems = card.find('#adviserCriteria .criteria-item');
        var adviserDescriptionItems = card.find('#adviserCriteria .text-muted');
        
        // Prepare edit containers
        $('#companyCriteriaContainer').empty();
        $('#adviserCriteriaContainer').empty();
        selectedCards = [];

        // Set company and job role in the modal
        $('#companyName').text(company);
        $('#jobrole').text(jobrole);

        // Process company criteria items
        companyCriteriaItems.each(function(index) {
            var title = $(this).find('strong').text();
            var percentage = $(this).text().match(/(\d+)%/)[1];
            var description = companyDescriptionItems.eq(index).text().trim();

            // Generate percentage dropdown options
            var percentageOptions = '';
            for (var i = 5; i <= 100; i += 5) {
                var selected = (i == percentage) ? 'selected' : '';
                percentageOptions += `<option value="${i}" ${selected}>${i}%</option>`;
            }

            // Filter criteria options by department
            var criteriaOptions = criteriaPresets.filter(function(item) {
                return item.department === '<?php echo $department; ?>';
            });

            // Build criteria dropdown options
            var criteriaSelectOptions = '';
            criteriaOptions.forEach(function(option) {
                criteriaSelectOptions += `<option value="${option.criteria}" ${option.criteria === title ? 'selected' : ''}>${option.criteria}</option>`;
            });

            // Build description dropdown options
            var descriptionSelectOptions = '';
            criteriaOptions.forEach(function(option){
                descriptionSelectOptions += `<option value="${option.description}" ${option.description === description ? 'selected' : ''}>${option.description}</option>`;
            });

            // Append company criteria to container
            $('#companyCriteriaContainer').append(`
                <div class="card mb-3 criteria-card" data-index="${index}">
                    <div class="card-body">
                        <div class="form-group">
                            <label><strong>Criteria Title</strong></label>
                            <select class="form-control criteria-dropdown" name="companyCriteria[${index}]" data-index="${index}">
                                ${criteriaSelectOptions}
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label><strong>Percentage</strong></label>
                            <select class="form-control" name="companyPercentage[${index}]">
                                ${percentageOptions}
                            </select>
                        </div>

                        <div class="form-group">
                            <label><strong>Description</strong></label>
                            <textarea class="form-control description-textarea" name="companyDescription[${index}]" rows="1">${description}</textarea>
                        </div>
                    </div>
                </div>
            `);
        });

        // Process adviser criteria items (same as company criteria)
        adviserCriteriaItems.each(function(index) {
            var title = $(this).find('strong').text();
            var percentage = $(this).text().match(/(\d+)%/)[1];
            var description = adviserDescriptionItems.eq(index).text().trim();

            // Generate percentage dropdown options
            var percentageOptions = '';
            for (var i = 5; i <= 100; i += 5) {
                var selected = (i == percentage) ? 'selected' : '';
                percentageOptions += `<option value="${i}" ${selected}>${i}%</option>`;
            }

            // Build criteria dropdown options
            var criteriaSelectOptions = '';
            criteriaPresets.forEach(function(option) {
                criteriaSelectOptions += `<option value="${option.criteria}" ${option.criteria === title ? 'selected' : ''}>${option.criteria}</option>`;
            });

            // Build description dropdown options
            var descriptionSelectOptions = '';
            criteriaPresets.forEach(function(option){
                descriptionSelectOptions += `<option value="${option.description}" ${option.description === description ? 'selected' : ''}>${option.description}</option>`;
            });

            // Append adviser criteria to container
            $('#adviserCriteriaContainer').append(`
                <div class="card mb-3 criteria-card" data-index="${index}">
                    <div class="card-body">
                        <div class="form-group">
                            <label><strong>Criteria Title</strong></label>
                            <select class="form-control criteria-dropdown" name="adviserCriteria[${index}]" data-index="${index}">
                                ${criteriaSelectOptions}
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label><strong>Percentage</strong></label>
                            <select class="form-control" name="adviserPercentage[${index}]">
                                ${percentageOptions}
                            </select>
                        </div>

                        <div class="form-group">
                            <label><strong>Description</strong></label>
                            <textarea class="form-control description-textarea" name="adviserDescription[${index}]" rows="1">${description}</textarea>
                        </div>
                    </div>
                </div>
            `);
        });

        // Set form values and action
        $('#editId').val(id);
        $('#editForm').attr('action', 'functions/grading-edit.php?id=' + id);

        // Adjust textarea heights
        $('.description-textarea').each(function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });

    $(document).ready(function() {
    var selectedCompanyCards = [];
    var selectedAdviserCards = [];

    function updateIndices(containerSelector, prefix) {
        $(containerSelector + ' .criteria-card').each(function(i) {
            $(this).attr('data-index', i);
            $(this).find('.criteria-dropdown').attr('name', prefix + 'Criteria[' + i + ']');
            $(this).find('select[name^="' + prefix + 'Percentage"]').attr('name', prefix + 'Percentage[' + i + ']');
            $(this).find('.description-textarea').attr('name', prefix + 'Description[' + i + ']');
        });
    }

    // Add company criteria card
    $('#addCompanyCriteriaBtn').click(function() {
        var newIndex = $('#companyCriteriaContainer .criteria-card').length;

        var percentageOptions = '<option value="" selected disabled>Select percentage</option>';
        for (var i = 5; i <= 100; i += 5) {
            percentageOptions += `<option value="${i}">${i}%</option>`;
        }

        var criteriaOptions = criteriaPresets.filter(function(item) {
            return item.department === '<?php echo $department; ?>';
        });

        var criteriaSelectOptions = '<option value="" selected disabled>Select criteria</option>';
        criteriaOptions.forEach(function(option) {
            criteriaSelectOptions += `<option value="${option.criteria}">${option.criteria}</option>`;
        });

        $('#companyCriteriaContainer').append(`
            <div class="card mb-3 criteria-card" data-index="${newIndex}">
                <div class="card-body">
                    <div class="form-group">
                        <label><strong>Criteria Title</strong></label>
                        <select class="form-control criteria-dropdown" name="companyCriteria[${newIndex}]" data-index="${newIndex}">
                            ${criteriaSelectOptions}
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><strong>Percentage</strong></label>
                        <select class="form-control" name="companyPercentage[${newIndex}]">
                            ${percentageOptions}
                        </select>
                    </div>

                    <div class="form-group">
                        <label><strong>Description</strong></label>
                        <textarea class="form-control description-textarea" name="companyDescription[${newIndex}]" rows="1"></textarea>
                    </div>
                </div>
            </div>
        `);
    });

    // Add adviser criteria card
    $('#addAdviserCriteriaBtn').click(function() {
        var newIndex = $('#adviserCriteriaContainer .criteria-card').length;

        var percentageOptions = '<option value="" selected disabled>Select percentage</option>';
        for (var i = 5; i <= 100; i += 5) {
            percentageOptions += `<option value="${i}">${i}%</option>`;
        }

        var criteriaOptions = criteriaPresets.filter(function(item) {
            return item.department === '<?php echo $department; ?>';
        });

        var criteriaSelectOptions = '<option value="" selected disabled>Select criteria</option>';
        criteriaOptions.forEach(function(option) {
            criteriaSelectOptions += `<option value="${option.criteria}">${option.criteria}</option>`;
        });

        $('#adviserCriteriaContainer').append(`
            <div class="card mb-3 criteria-card" data-index="${newIndex}">
                <div class="card-body">
                    <div class="form-group">
                        <label><strong>Criteria Title</strong></label>
                        <select class="form-control criteria-dropdown" name="adviserCriteria[${newIndex}]" data-index="${newIndex}">
                            ${criteriaSelectOptions}
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label><strong>Percentage</strong></label>
                        <select class="form-control" name="adviserPercentage[${newIndex}]">
                            ${percentageOptions}
                        </select>
                    </div>

                    <div class="form-group">
                        <label><strong>Description</strong></label>
                        <textarea class="form-control description-textarea" name="adviserDescription[${newIndex}]" rows="1"></textarea>
                    </div>
                </div>
            </div>
        `);
    });

    // Select company criteria card
    $(document).on('click', '#companyCriteriaContainer .criteria-card', function() {
        var card = $(this);
        var index = card.data('index');

        if (card.hasClass('selected-card')) {
            card.removeClass('selected-card');
            selectedCompanyCards = selectedCompanyCards.filter(i => i !== index);
        } else {
            card.addClass('selected-card');
            selectedCompanyCards.push(index);
        }

        $('#deleteCriteriaBtn').prop('disabled', selectedCompanyCards.length === 0 && selectedAdviserCards.length === 0);
    });

    // Select adviser criteria card
    $(document).on('click', '#adviserCriteriaContainer .criteria-card', function() {
        var card = $(this);
        var index = card.data('index');

        if (card.hasClass('selected-card')) {
            card.removeClass('selected-card');
            selectedAdviserCards = selectedAdviserCards.filter(i => i !== index);
        } else {
            card.addClass('selected-card');
            selectedAdviserCards.push(index);
        }

        $('#deleteCriteriaBtn').prop('disabled', selectedCompanyCards.length === 0 && selectedAdviserCards.length === 0);
    });

    // Delete selected criteria cards
    $(document).on('click', '#deleteCriteriaBtn', function() {
        if (selectedCompanyCards.length === 0 && selectedAdviserCards.length === 0) return;

        // Delete company cards
        if (selectedCompanyCards.length > 0) {
            selectedCompanyCards.sort((a, b) => b - a);
            selectedCompanyCards.forEach(function(index) {
                $('#companyCriteriaContainer .criteria-card[data-index="' + index + '"]').remove();
            });

            updateIndices('#companyCriteriaContainer', 'company');
            selectedCompanyCards = [];
        }

        // Delete adviser cards
        if (selectedAdviserCards.length > 0) {
            selectedAdviserCards.sort((a, b) => b - a);
            selectedAdviserCards.forEach(function(index) {
                $('#adviserCriteriaContainer .criteria-card[data-index="' + index + '"]').remove();
            });

            updateIndices('#adviserCriteriaContainer', 'adviser');
            selectedAdviserCards = [];
        }

        $('input, select, textarea').trigger('change');
        $('#deleteCriteriaBtn').prop('disabled', true);
    });
});

});

    // Auto-resize textareas
    $(document).on('input', '.description-textarea', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Delete confirmation handler
    $('.deleteBtn').click(function() {
        var id = $(this).data('id');
        $('#confirmDelete').data('id', id);
    });

    // Actual delete operation
    $('#confirmDelete').click(function() {
        var id = $(this).data('id');
        $.ajax({
            url: 'functions/grading-delete.php',
            type: 'POST',
            data: { id: id },
            success: function(response) {
                alert(response);
                location.reload();
            },
            error: function(xhr, status, error) {
                alert('An error occurred: ' + error);
            }
        });
    });

    // Criteria dropdown change handler
    $(document).ready(function() {
    var selectedCards = [];

    var criteriaPresets = <?php
        // Server-side code to fetch criteria presets for current department
        $department = $_SESSION['department'];
        $result = mysqli_query($connect, "SELECT * FROM criteria_presets WHERE department = '$department'");
        $criteriaData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $criteriaData[] = $row;
        }
        echo json_encode($criteriaData, JSON_PRETTY_PRINT);
    ?>;

    function updateDescription(container) {
        $(document).on('change', `${container} .criteria-dropdown`, function() {
            var index = $(this).data('index');
            var selectedCriteria = $(this).val();

            console.log(`Dropdown changed in ${container}:`, selectedCriteria); // Debugging

            // Ensure criteriaPresets is defined
            if (typeof criteriaPresets === 'undefined' || !Array.isArray(criteriaPresets)) {
                console.error("criteriaPresets is undefined or not an array.");
                return;
            }

            // Find matching description
            var selectedDescription = '';
            criteriaPresets.forEach(function(option) {
                if (option.criteria === selectedCriteria) {
                    selectedDescription = option.description;
                }
            });

            console.log(`Updating description for index ${index}:`, selectedDescription); // Debugging

            // Update corresponding description field
            var descriptionField = $(`${container} .criteria-card[data-index="${index}"] .description-textarea`);
            if (descriptionField.length > 0) {
                descriptionField.val(selectedDescription);
            } else {
                console.error(`Description field not found for index ${index} in ${container}`);
            }
        });
    }

    // Apply update function to both company and adviser criteria
    updateDescription('#companyCriteriaContainer');
    updateDescription('#adviserCriteriaContainer');
});


</script>
        </div>
    </div>
</body>
</html>
