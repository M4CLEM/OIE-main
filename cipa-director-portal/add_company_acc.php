<?php
session_start();
include_once("../includes/connection.php");
$query = "SELECT * FROM users WHERE role ='IndustryPartner'";
$result = mysqli_query($connect, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include("../elements/meta.php"); ?>
    <title>CIPA ADMIN</title>
    <?php include("embed.php"); ?>
    <link rel="stylesheet" href="../assets/css/new-style.css">
    <style>
        .modal-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .form-group {
            width: 100%;
            max-width: 400px;
        }

        .form-control {
            width: 100%;
        }

        select.form-control {
            width: 100%;
            min-width: 100%;
        }

        select.form-control option {
            border-bottom: 1px solid #ccc;
            padding: 10px;
            white-space: nowrap;
        }
    </style>
</head>

<body id="page-top">
    <div class="wrapper">
        <aside id="sidebar" class="expand">
            <?php include('../elements/cipa_sidebar.php') ?>
        </aside>
        <div class="main">
            <nav class="navbar navbar-expand navbar-light bg-white topbar static-top shadow mb-3">
                <h2 class="my-0 mr-auto font-weight-bold text-dark ml-3">Company Accounts</h2>
                <?php include('../elements/cipa_navbar_user_info.php') ?>
            </nav>
            <div id="content" class="py-2">
                <div class="col-lg-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal"> <i class="fa fa-plus-circle fw-fa"></i> Add </a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Company Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Password</th>
                                            <th width="15%" align="center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($rows = mysqli_fetch_assoc($result)) { ?>
                                            <tr>
                                                <td><?php echo $rows['companyName']; ?></td>
                                                <td><?php echo $rows['username']; ?></td>
                                                <td><?php echo $rows['password']; ?></td>
                                                <td>
                                                    <div class="d-flex justify-content-start align-items-center gap-2">
                                                        <a href="#" class="btn btn-primary btn-sm editBtn"
                                                            data-toggle="modal"
                                                            data-target="#editModal"
                                                            data-id="<?php echo $rows['id']; ?>"
                                                            data-companyname="<?php echo $rows['companyName']; ?>"
                                                            data-email="<?php echo $rows['username']; ?>"
                                                            data-password="<?php echo $rows['password']; ?>">
                                                            <i class="fa fa-edit fw-fa"></i> Edit
                                                        </a>
                                                        <button type="button" class="btn btn-danger btn-sm deleteBtn"
                                                            data-toggle="modal"
                                                            data-target="#deleteModal"
                                                            data-id="<?php echo $rows['id']; ?>">
                                                            <i class="fa fa-trash fw-fa"></i> Delete
                                                        </button>
                                                    </div>
                                                </td>

                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADD MODAL -->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addAdvisers" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="add_ip.php" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addAdvisers">Add Company</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" class="form-control" id="searchCompany" placeholder="Search Company">
                            </div>
                            <div class="form-group">
                                <select class="form-control" id="companyName" name="companyName" required>
                                    <option value="">Select Company</option>
                                    <?php
                                    $query = "SELECT DISTINCT companyName FROM companylist";
                                    $companies = mysqli_query($connect, $query);
                                    while ($company = mysqli_fetch_assoc($companies)) {
                                        echo "<option value='" . $company['companyName'] . "'>" . $company['companyName'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <input class="form-control" id="email" name="email" type="text" placeholder="Email" required>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" id="confirm" name="confirm" placeholder="Confirm Password" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary btn-sm" name="save" type="submit"><span class="fa fa-save fw-fa"></span> Save</button>
                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!--EDIT MODAL -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editCompany" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="editForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCompany">Edit Company</h5>
                            <input type="hidden" id="editID" name="id">
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div id="editMessage"></div> <!-- AJAX feedback -->

                            <div class="form-group">
                                <input type="text" class="form-control" id="editSearchCompany" placeholder="Search Company">
                            </div>
                            <div class="form-group">
                                <select class="form-control" id="editCompanyName" name="editCompanyName" required>
                                    <option value="">Select Company</option>
                                    <?php
                                    $query = "SELECT DISTINCT companyName FROM companylist";
                                    $companies = mysqli_query($connect, $query);
                                    while ($company = mysqli_fetch_assoc($companies)) {
                                        echo "<option value='" . $company['companyName'] . "'>" . $company['companyName'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <input class="form-control" id="editEmail" name="editEmail" type="text" placeholder="Email" required>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" id="editPassword" name="editPassword" placeholder="New Password">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" id="editConfirmPassword" name="editConfirmPassword" placeholder="Confirm Password">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="saveEditBtn" class="btn btn-primary btn-sm">
                                <span class="fa fa-save fw-fa"></span> Save
                            </button>
                            <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- DELETE CONFIRMATION MODAL -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Confirmation</h5>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this company account?
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger btn-sm" id="confirmDelete">Delete</button>
                    </div>
                </div>
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../assets/js/sidebarscript.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const searchInput = document.getElementById("searchCompany");
                const companyDropdown = document.getElementById("companyName");
                const companySet = new Set();
                const companyList = [];

                for (let i = companyDropdown.options.length - 1; i > 0; i--) {
                    const companyName = companyDropdown.options[i].text;
                    const companyValue = companyDropdown.options[i].value;
                    if (companySet.has(companyName)) {
                        companyDropdown.remove(i);
                    } else {
                        companySet.add(companyName);
                        companyList.push({
                            name: companyName,
                            value: companyValue
                        });
                    }
                }

                const suggestionBox = document.createElement("div");
                suggestionBox.setAttribute("id", "suggestionBox");
                suggestionBox.style.position = "absolute";
                suggestionBox.style.zIndex = "1000";
                suggestionBox.style.background = "#fff";
                suggestionBox.style.border = "1px solid #ccc";
                suggestionBox.style.color = "black";
                suggestionBox.style.padding = "5px";
                suggestionBox.style.boxShadow = "0px 4px 6px rgba(0, 0, 0, 0.1)";
                suggestionBox.style.maxHeight = "200px";
                suggestionBox.style.overflowY = "auto";
                suggestionBox.style.fontSize = "14px";
                suggestionBox.style.fontWeight = "500";
                suggestionBox.style.display = "none";
                suggestionBox.style.borderRadius = "5px";
                suggestionBox.style.width = searchInput.clientWidth + "px";

                searchInput.parentNode.style.position = "relative";
                searchInput.parentNode.appendChild(suggestionBox);

                function updateSuggestionBoxPosition() {
                    const rect = searchInput.getBoundingClientRect();
                    suggestionBox.style.top = searchInput.offsetHeight + "px";
                    suggestionBox.style.left = "0px";
                    suggestionBox.style.width = searchInput.clientWidth + "px";
                }

                searchInput.addEventListener("input", function() {
                    const searchValue = this.value.toLowerCase();
                    suggestionBox.innerHTML = "";
                    if (searchValue.length > 0) {
                        const filteredCompanies = companyList.filter(company =>
                            company.name.toLowerCase().includes(searchValue)
                        );

                        filteredCompanies.forEach(company => {
                            const suggestionItem = document.createElement("div");
                            suggestionItem.textContent = company.name;
                            suggestionItem.style.padding = "10px";
                            suggestionItem.style.cursor = "pointer";
                            suggestionItem.style.background = "#fff";
                            suggestionItem.style.borderBottom = "1px solid #ddd";
                            suggestionItem.addEventListener("click", function() {
                                searchInput.value = company.name;
                                companyDropdown.value = company.value;
                                suggestionBox.style.display = "none";
                            });
                            suggestionBox.appendChild(suggestionItem);
                        });

                        updateSuggestionBoxPosition();
                        suggestionBox.style.display = "block";
                    } else {
                        suggestionBox.style.display = "none";
                    }
                });

                document.addEventListener("click", function(e) {
                    if (!searchInput.contains(e.target) && !suggestionBox.contains(e.target)) {
                        suggestionBox.style.display = "none";
                    }
                });

                companyDropdown.addEventListener("change", function() {
                    const selectedOption = this.options[this.selectedIndex];
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                // === Populate Edit Modal Fields ===
                $('.editBtn').click(function() {
                    $('#editID').val($(this).data('id'));
                    $('#editCompanyName').val($(this).data('companyname'));
                    $('#editEmail').val($(this).data('email'));
                    $('#editPassword').val('');
                    $('#editConfirmPassword').val('');
                    $('#editSearchCompany').val('');
                });

                // === Save Edit via AJAX ===
                $('#saveEditBtn').click(function() {
                    const password = $('#editPassword').val();
                    const confirm = $('#editConfirmPassword').val();

                    if (password && password !== confirm) {
                        alert("Passwords do not match!");
                        $('#editConfirmPassword').focus();
                        return;
                    }

                    var formData = $('#editForm').serialize();
                    $.ajax({
                        url: "functions/edit_company_acc.php",
                        type: "POST",
                        data: formData,
                        dataType: "json",
                        beforeSend: function() {
                            $('#editMessage').html('<div class="alert alert-info">Processing...</div>');
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                $('#editMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                                setTimeout(() => {
                                    $('#editModal').modal('hide');
                                    location.reload();
                                }, 1500);
                            } else {
                                $('#editMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                            }
                        },
                        error: function() {
                            $('#editMessage').html('<div class="alert alert-danger">An error occurred.</div>');
                        }
                    });
                });

                // === Setup Delete Functionality ===
                $('.deleteBtn').click(function() {
                    $('#confirmDelete').data('id', $(this).data('id'));
                });

                $('#confirmDelete').click(function() {
                    var id = $(this).data('id');
                    $.ajax({
                        url: "functions/delete_company_acc.php",
                        type: "POST",
                        data: {
                            id: id
                        },
                        success: function() {
                            $('#deleteModal').modal('hide');
                            location.reload();
                        },
                        error: function() {
                            alert('Failed to delete. Try again.');
                        }
                    });
                });

                // === Company Search Logic for Edit Modal ===
                const editSearchInput = document.getElementById("editSearchCompany");
                const editCompanyDropdown = document.getElementById("editCompanyName");
                const editSuggestionBox = document.createElement("div");

                const companySet = new Set();
                const companyList = [];

                // Collect all dropdown options for search
                for (let i = 1; i < editCompanyDropdown.options.length; i++) {
                    const companyName = editCompanyDropdown.options[i].text;
                    const companyValue = editCompanyDropdown.options[i].value;
                    if (!companySet.has(companyName)) {
                        companySet.add(companyName);
                        companyList.push({
                            name: companyName,
                            value: companyValue
                        });
                    }
                }

                // Setup suggestion box
                editSuggestionBox.setAttribute("id", "editSuggestionBox");
                editSuggestionBox.style.position = "absolute";
                editSuggestionBox.style.zIndex = "1000";
                editSuggestionBox.style.background = "#fff";
                editSuggestionBox.style.border = "1px solid #ccc";
                editSuggestionBox.style.color = "black";
                editSuggestionBox.style.padding = "5px";
                editSuggestionBox.style.boxShadow = "0px 4px 6px rgba(0, 0, 0, 0.1)";
                editSuggestionBox.style.maxHeight = "200px";
                editSuggestionBox.style.overflowY = "auto";
                editSuggestionBox.style.fontSize = "14px";
                editSuggestionBox.style.fontWeight = "500";
                editSuggestionBox.style.display = "none";
                editSuggestionBox.style.borderRadius = "5px";

                editSearchInput.parentNode.style.position = "relative";
                editSearchInput.parentNode.appendChild(editSuggestionBox);

                function updateEditSuggestionBoxPosition() {
                    editSuggestionBox.style.top = editSearchInput.offsetHeight + "px";
                    editSuggestionBox.style.left = "0px";
                    editSuggestionBox.style.width = editSearchInput.clientWidth + "px";
                }

                editSearchInput.addEventListener("input", function() {
                    const searchValue = this.value.toLowerCase();
                    editSuggestionBox.innerHTML = "";
                    if (searchValue.length > 0) {
                        const filteredCompanies = companyList.filter(company =>
                            company.name.toLowerCase().includes(searchValue)
                        );

                        filteredCompanies.forEach(company => {
                            const suggestionItem = document.createElement("div");
                            suggestionItem.textContent = company.name;
                            suggestionItem.style.padding = "10px";
                            suggestionItem.style.cursor = "pointer";
                            suggestionItem.style.background = "#fff";
                            suggestionItem.style.borderBottom = "1px solid #ddd";
                            suggestionItem.addEventListener("click", function() {
                                editSearchInput.value = company.name;
                                editCompanyDropdown.value = company.value;
                                editSuggestionBox.style.display = "none";
                            });
                            editSuggestionBox.appendChild(suggestionItem);
                        });

                        updateEditSuggestionBoxPosition();
                        editSuggestionBox.style.display = "block";
                    } else {
                        editSuggestionBox.style.display = "none";
                    }
                });

                document.addEventListener("click", function(e) {
                    if (!editSearchInput.contains(e.target) && !editSuggestionBox.contains(e.target)) {
                        editSuggestionBox.style.display = "none";
                    }
                });
            });
        </script>
</body>
</html>