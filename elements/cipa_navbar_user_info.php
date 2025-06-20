<?php
    $cipa = $_SESSION['CIPA'];

    $departmentQuery = $connect->prepare("SELECT * FROM department_list");
    $departmentQuery->execute();
    $departmentResult = $departmentQuery->get_result();
?>

<style> /* For the settings Modal */
    .modal-body.no-padding {
        padding: 0;
        margin: 0;
        min-height: 400px;
    }

    /* Smaller, tighter nav pills */
    .nav-pills.modal-header-pills .nav-link {
        padding: 2px 8px !important;        /* Less padding */
        font-size: 1rem !important;       /* Smaller font */
        line-height: 1 !important;          /* Tighter line spacing */
        border: 1px solid #dee2e6;
        border-radius: 0;
        margin: 0;
        height: auto !important;           /* Prevent forced height */
    }

    .nav-pills.modal-header-pills .nav-link + .nav-link {
        border-left: none;
    }

    .nav-pills.modal-header-pills .nav-link.active {
        background-color: #007bff;
        color: white;
    }

    .tab-content {
        padding: 10px;
        padding-left: 20px;
        padding-right: 20px;
    }

    .modal-header {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        padding-bottom: 0.5rem;
        position: relative;
    }

    .modal-title {
        margin-bottom: 0.5rem;
        font-size: 1rem;
    }

    .modal-header .close {
        position: absolute;
        top: 0.5rem;
        right: 1rem;
    }
    .modal-body .tab-pane {
        border: none !important;
    }
</style>

<ul class="navbar-nav ml-auto">
    <div class="topbar-divider d-none d-sm-block"></div>
    <!-- Nav Item - User Information -->
    <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  
            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                <?php echo $cipa?>
            </span>
            <div class="position-relative d-inline-block">
                <img class="img-profile rounded-circle"src="../img/undraw_profile.svg">
                <!-- Notification badge placeholder -->
                <span id="notifBadge" class="badge badge-danger badge-counter" 
                      style="position: absolute; top: 0; right: 0; font-size: 10px; padding: 3px 6px; border-radius: 50%; display:none;">
                    0
                </span>
            </div>
        </a>
        <!-- Dropdown - User Information -->
        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="#">
                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                Profile
            </a>
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#settingsModal">
                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                Settings
            </a>
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#notificationModal">
                <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                Notification
                <span id="notifBadgeDropdown" class="badge badge-danger" style="display:none;">0</span>
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="../logout.php" data-toggle="modal" data-target="#logoutModal">
                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                Logout
            </a>
        </div>
    </li>
</ul>

<!-- NOTIFICATION MODAL -->
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Notifications</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6" style="height: 70vh; overflow-y: auto;">
                        <div class="card">
                            <form id="createAnnouncement">
                                <div class="card-header">
                                    Create Announcement
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="col-md">
                                            <div>
                                                <span>Title</span>
                                            </div>
                                            <input class="form-control" type="text" name="title" id="title" placeholder="Title" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md">
                                            <div>
                                                <span>Description</span>
                                            </div>
                                            <textarea class="form-control" name="description" id="description" placeholder="Description..." rows="5" required></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md">
                                            <div>
                                                <span>College</span>
                                            </div>
                                            <select class="form-control" name="department" id="department">
                                                <option value="All">All Department</option>
                                                <?php while ($row = $departmentResult->fetch_assoc()): ?>
                                                    <option value="<?= htmlspecialchars($row['department']) ?>">
                                                        <?= htmlspecialchars($row['department']) ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md">
                                            <div class="row"  style="display: flex; justify-content: center; align-items: center;">
                                                <span>Event/Reminder Duration</span>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col">
                                                    <span>Starting Date</span>
                                                    <input class="form-control" type="date" name="postDate" id="postDate" required>
                                                </div>
                                                <div class="col">
                                                    <span>Ending Date</span>
                                                    <input class="form-control" type="date" name="endDate" id="endDate" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-primary btn-sm" name="save" type="submit"><span class="fa fa-save fw-fa"></span> Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="notificationContainer col-md-6" style="height: 70vh; overflow-y: auto;">
                        <!-- ANNOUNCEMENT, REMINDERS AND EVENTS LOADS HERE -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmSubmitModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirm Submission</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to submit this announcement?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmSubmitBtn" class="btn btn-primary">Yes, Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- LOG OUT MODAL-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <button class="btn btn-primary" onclick="window.location.href='../logout.php';">Logout</button>
            </div>
        </div>
    </div>
</div>

<!-- SETTINGS MODAL -->
<div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="settingsModalLabel">Settings</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body no-padding">
                <ul class="nav nav-pills modal-header-pills" id="settingsPillTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="system-pill" data-toggle="pill" href="#system" role="tab" aria-controls="system" aria-selected="true">System</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="account-pill" data-toggle="pill" href="#account" role="tab" aria-controls="account" aria-selected="false">Account</a>
                    </li>
                </ul>

                <div class="tab-content" id="settingsPillContent">
                    <div class="tab-pane fade show active" id="system" role="tabpanel" aria-labelledby="system-pill">
                        <form action="functions/update_academic_session.php" method="POST">
                            <h3>Academic Calendar</h3>
                            <p class="small">Active Academic Year: <?php echo $_SESSION['semester']?> - <?php echo $_SESSION['schoolYear']?></p>
                            <br>
                            <div class="form-group">
                                <div class="row mb-1">
                                    <div class="col-md-4">
                                        <h5 for="semester">Semester:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" name="semester" id="semester" required>
                                            <option value="" disabled selected>Select Semester</option>
                                            <option value="1st Semester">1st Semester</option>
                                            <option value="2nd Semester">2nd Semester</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row mb-1">
                                    <div class="col-md-4">
                                        <h5 for="schoolYear">School Year:</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <select class="form-control" name="schoolYear" id="schoolYear" required>
                                            <option value="" disabled selected>Select school Year</option>
                                            <?php
                                                $changeAcademicYear = "SELECT schoolYear FROM academic_year ORDER BY schoolYear DESC";
                                                $academicStmt = $connect->prepare($changeAcademicYear);

                                                if ($academicStmt) {
                                                    $academicStmt->execute();
                                                    $academicStmt->bind_result($schoolYear);

                                                    while ($academicStmt->fetch()) {
                                                        echo "<option value='$schoolYear'>$schoolYear</option>"; // ✅ VALID

                                                    }

                                                    $academicStmt->close();
                                                } else {
                                                    echo "<option disabled>Error loading years</option>";
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- Save Button -->
                            <div class="form-group text-right mt-3">
                                <button type="submit" class="btn btn-primary">Change Academic Year</button>
                            </div>
                        </form>
                        <hr>
                        <p class="small">More settings coming soon..</p>
                    </div>
                                        <div class="tab-pane fade" id="account" role="tabpanel" aria-labelledby="account-pill">
                        <form id="updatePasswordForm">
                            <!-- Email -->
                            <div class="form-group">
                                <div class="row mb-1">
                                    <div class="col-md-3">
                                        <p>Email:</p>
                                    </div>
                                    <div class="col-md-9">
                                        <input class="form-control" id="userEmail" name="userEmail" type="email" value="<?php echo $_SESSION['CIPA']; ?>" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- New Password -->
                            <div class="form-group">
                                <div class="row mb-1">
                                    <div class="col-md-3">
                                        <p>New Password:</p>
                                    </div>
                                    <div class="col-md-9">
                                        <input class="form-control" id="userPassword" name="userPassword" type="password" placeholder="New Password" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-group">
                                <div class="row mb-1">
                                    <div class="col-md-3">
                                        <p>Confirm Password:</p>
                                    </div>
                                    <div class="col-md-9">
                                        <input class="form-control" id="userConfirmPassword" name="userConfirmPassword" type="password" placeholder="Confirm Password" required>
                                        <small id="passwordMatchMessage" class="text-danger d-none">Passwords do not match.</small>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- OTP Section (Initially Hidden) -->
                            <div id="otpSection" class="d-none">
                                <div class="form-group">
                                    <div class="row mb-1">
                                        <div class="col-md-3">
                                            <p>OTP Code:</p>
                                        </div>
                                        <div class="col-md-9">
                                            <input class="form-control" id="userOTP" name="userOTP" type="text" placeholder="Enter OTP">
                                            <small id="otpTimer" class="text-danger mt-1"></small>
                                        </div>
                                    </div>
                                </div>

                                <!-- OTP Action Buttons -->
                                <div class="form-group text-center mt-3">
                                    <button type="button" id="sendOTP" class="btn btn-primary">
                                        <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Send OTP
                                    </button>
                                    <button type="button" id="verifyOTP" class="btn btn-success" disabled>
                                        <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Verify OTP
                                    </button>
                                </div>
                            </div>

                            <hr>

                            <!-- Save Button -->
                            <div class="form-group text-right mt-3">
                                <button type="submit" id="saveBtn" class="btn btn-primary" disabled>
                                    <span class="spinner-border spinner-border-sm d-none me-1" role="status"></span> Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
    <div id="toastContainer"></div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const password = document.getElementById("userPassword");
        const confirmPassword = document.getElementById("userConfirmPassword");
        const passwordMatchMessage = document.getElementById("passwordMatchMessage");
        const otpSection = document.getElementById("otpSection");
        const sendOTPBtn = document.getElementById("sendOTP");
        const verifyOTPBtn = document.getElementById("verifyOTP");
        const saveBtn = document.getElementById("saveBtn");
        const otpTimerDisplay = document.getElementById("otpTimer");

        let countdown;
        let timeLeft = 0;

        function showToast(message, type = "success") {
            const toastId = `toast-${Date.now()}`;
            const icon = type === "success" ? "✅" : type === "error" ? "❌" : "⚠️";
            const toast = `
        <div id="${toastId}" class="toast align-items-center text-white bg-${type} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body">
              ${icon} ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>`;

            const container = document.getElementById("toastContainer");
            container.insertAdjacentHTML("beforeend", toast);

            const toastElement = new bootstrap.Toast(document.getElementById(toastId), {
                delay: 4000
            });
            toastElement.show();

            setTimeout(() => {
                const el = document.getElementById(toastId);
                if (el) el.remove();
            }, 5000);
        }

        function checkPasswords() {
            const passVal = password.value.trim();
            const confirmVal = confirmPassword.value.trim();

            if (passVal && confirmVal && passVal === confirmVal) {
                passwordMatchMessage.classList.add("d-none");
                otpSection.classList.remove("d-none");
                sendOTPBtn.disabled = false;
            } else if (passVal && confirmVal && passVal !== confirmVal) {
                passwordMatchMessage.classList.remove("d-none");
                otpSection.classList.add("d-none");
                sendOTPBtn.disabled = true;
            } else {
                passwordMatchMessage.classList.add("d-none");
                otpSection.classList.add("d-none");
                sendOTPBtn.disabled = true;
            }
        }

        password.addEventListener("input", checkPasswords);
        confirmPassword.addEventListener("input", checkPasswords);

        function startOTPTimer(duration) {
            clearInterval(countdown);
            timeLeft = duration;
            updateTimerDisplay();

            countdown = setInterval(() => {
                timeLeft--;
                updateTimerDisplay();

                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    otpTimerDisplay.textContent = "OTP expired. Please resend.";
                    verifyOTPBtn.disabled = true;
                    saveBtn.disabled = true;
                    sendOTPBtn.disabled = false;
                }
            }, 1000);
        }

        function updateTimerDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            otpTimerDisplay.textContent = `OTP will expire in ${minutes}:${seconds.toString().padStart(2, '0')}`;
        }

        // Send OTP
        sendOTPBtn.addEventListener("click", function() {
            const email = document.getElementById("userEmail").value;
            const spinner = sendOTPBtn.querySelector(".spinner-border");

            spinner.classList.remove("d-none");
            sendOTPBtn.disabled = true;

            fetch("/OIE-main/elements/functions/send_otp.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "email=" + encodeURIComponent(email)
                })
                .then(res => res.text())
                .then(data => {
                    showToast("OTP sent! Check your email.", "success");
                    verifyOTPBtn.disabled = false;
                    saveBtn.disabled = true;
                    startOTPTimer(120);
                })
                .catch(() => {
                    showToast("Failed to send OTP. Please try again.", "danger");
                })
                .finally(() => {
                    spinner.classList.add("d-none");
                    sendOTPBtn.disabled = false;
                });
        });

        // Verify OTP
        verifyOTPBtn.addEventListener("click", function() {
            const otp = document.getElementById("userOTP").value;
            const spinner = verifyOTPBtn.querySelector(".spinner-border");

            spinner.classList.remove("d-none");
            verifyOTPBtn.disabled = true;

            fetch("/OIE-main/elements/functions/verify_otp.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "otp=" + encodeURIComponent(otp)
                })
                .then(res => res.text())
                .then(data => {
                    data = data.trim();
                    if (data === "verified") {
                        showToast("OTP verified successfully!", "success");
                        saveBtn.disabled = false;

                        verifyOTPBtn.disabled = true;
                        verifyOTPBtn.classList.add("disabled");

                        sendOTPBtn.disabled = true;
                        clearInterval(countdown);
                        otpTimerDisplay.textContent = "OTP verified! Please press save button";
                        otpTimerDisplay.classList.remove("text-danger");
                        otpTimerDisplay.classList.add("text-success");
                    } else if (data === "expired") {
                        showToast("OTP expired. Please resend.", "warning");
                        saveBtn.disabled = true;
                        sendOTPBtn.disabled = false;
                        clearInterval(countdown);
                        otpTimerDisplay.textContent = "OTP expired. Please resend.";
                    } else if (data === "no_otp") {
                        showToast("No OTP found. Please request a new one.", "warning");
                        saveBtn.disabled = true;
                        sendOTPBtn.disabled = false;
                        clearInterval(countdown);
                        otpTimerDisplay.textContent = "";
                    } else {
                        showToast("Invalid OTP. Please try again.", "danger");
                    }
                })
                .catch(() => {
                    showToast("Verification failed. Try again.", "danger");
                })
                .finally(() => {
                    spinner.classList.add("d-none");
                    verifyOTPBtn.disabled = false;
                });
        });

        // Save Password
        document.getElementById("updatePasswordForm").addEventListener("submit", function(e) {
            e.preventDefault();
            const newPass = password.value.trim();
            const confirmPass = confirmPassword.value.trim();
            const spinner = saveBtn.querySelector(".spinner-border");

            spinner.classList.remove("d-none");
            saveBtn.disabled = true;

            fetch("/OIE-main/elements/functions/update_password.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "password=" + encodeURIComponent(newPass) + "&confirm=" + encodeURIComponent(confirmPass)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        showToast("Password updated successfully!", "success");
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showToast(data.message, "danger");
                    }
                })
                .catch(() => {
                    showToast("Something went wrong. Please try again.", "danger");
                })
                .finally(() => {
                    spinner.classList.add("d-none");
                    saveBtn.disabled = false;
                });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Function to update unread notification count badges
        function updateNotificationBadge() {
            fetch('functions/fetch_unread_count.php')
            .then(response => response.json())
            .then(data => {
                const count = data.unreadCount || 0;
                const badge = document.getElementById('notifBadge');
                const badgeDropdown = document.getElementById('notifBadgeDropdown');
                
                if (count > 0) {
                    badge.style.display = 'inline-block';
                    badge.textContent = count;
                    badgeDropdown.style.display = 'inline-block';
                    badgeDropdown.textContent = count;
                } else {
                    badge.style.display = 'none';
                    badgeDropdown.style.display = 'none';
                }
            })
            .catch(console.error);
        }

        // Function to load notifications into the modal
        function loadNotificationModal() {
            const notificationContainer = document.querySelector('#notificationModal .notificationContainer');
            if (notificationContainer) {
                notificationContainer.innerHTML = '<p>Loading notifications...</p>';
                fetch('functions/load_notifications.php')
                .then(response => response.text())
                .then(html => {
                    notificationContainer.innerHTML = html;
                    updateNotificationBadge(); // Refresh badge after loading notifications
                })
                .catch(() => {
                    notificationContainer.innerHTML = '<p>Failed to load notifications.</p>';
                });
            }
        }

        // Initial badge update
        updateNotificationBadge();

        // Reload notifications each time modal opens
        $('#notificationModal').on('show.bs.modal', loadNotificationModal);

        // Event delegation: handle submit on any .mark-as-read-form inside the body
        document.body.addEventListener('submit', function(e) {
            if (e.target.classList.contains('mark-as-read-form')) {
                e.preventDefault();

                const form = e.target;
                const notificationId = form.getAttribute('data-notification-id');
                const employeeNumber = form.getAttribute('data-employee-number');

                if (!notificationId || !employeeNumber) {
                    alert('Missing required data.');
                    console.error('Missing data:', { notificationId, employeeNumber });
                    return;
                }

                console.log('Sending mark as read request:', { notificationId, employeeNumber });

                // Prepare data to send
                const formData = new URLSearchParams();
                formData.append('notification_id', notificationId);
                formData.append('employeeNumber', employeeNumber);

                fetch('functions/mark_single_read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData.toString()
                })
                .then(response => {
                    console.log('Fetch response status:', response.status);
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response JSON:', data);

                    if (data.status === 'success') {
                        // Mark button disabled & change text
                        const btn = form.querySelector('button');
                        if (btn) {
                            btn.disabled = true;
                            btn.textContent = 'Read';
                        } else {
                            console.warn('Button not found inside form.');
                        }

                        // Optionally update badge count and reload notifications
                        updateNotificationBadge();
                        loadNotificationModal();
                    } else {
                        alert('Error: ' + (data.message || 'Failed to mark notification as read.'));
                        console.error('Backend error:', data);
                    }
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    alert('An error occurred: ' + err.message);
                });
            }
        });

        //MARK ALL AS READ
        document.body.addEventListener('submit', function(e) {
            if (e.target.classList.contains('mark-all-read-form')) {
                e.preventDefault();

                const form = e.target;
                const employeeNumber = form.getAttribute('data-employee-number');

                if (!employeeNumber) {
                    alert('Missing Employee Number');
                    return;
                }

                const formData = new URLSearchParams();
                formData.append('employeeNumber', employeeNumber);

                fetch('functions/mark_all_read.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData.toString()
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network error: ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        updateNotificationBadge();
                        loadNotificationModal();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error('AJAX error:', err);
                    alert('An error occurred: ' + err.message);
                });
            }
        });

        const announcementForm = document.getElementById('createAnnouncement');
        const confirmBtn = document.getElementById('confirmSubmitBtn');

        if (announcementForm && confirmBtn) {
            announcementForm.addEventListener('submit', function (e) {
                e.preventDefault();
                $('#confirmSubmitModal').modal('show');
            });

            confirmBtn.addEventListener('click', function () {
                $('#confirmSubmitModal').modal('hide');
                const formData = new FormData(announcementForm);

                fetch('functions/submit_announcement.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network error: ' + response.status);
                    return response.text();
                })
                .then(data => {
                    alert('Announcement submitted successfully!');
                    updateNotificationBadge();
                    loadNotificationModal();
                    announcementForm.reset();
                })
                .catch(err => {
                    alert('Error submitting announcement: ' + err.message);
                });
            });
        }
    })
</script>