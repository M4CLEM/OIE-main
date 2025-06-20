<?php
// Make sure session is started and DB connection ($connect) is available

$email = $_SESSION['student'] ?? null;

if (!$email) {
    // Redirect or handle error
    exit('No user logged in.');
}

$query = "SELECT * FROM studentinfo WHERE email = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$image = $row['image'] ?? null;
$studentID = $row['studentID'] ?? null;

function get_drive_image_url($image)
{
    if (strpos($image, 'drive.google.com') !== false) {
        preg_match('/(?:id=|\/d\/)([a-zA-Z0-9_-]{25,})/', $image, $matches);
        $image = $matches[1] ?? null;
    }
    if ($image && preg_match('/^[a-zA-Z0-9_-]{25,}$/', $image)) {
        return "https://lh3.googleusercontent.com/d/{$image}=w1000";
    }
    return $image;
}
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

    <li class="nav-item dropdown no-arrow">
        <a class="nav-link dropdown-toggle position-relative" href="#" id="userDropdown" role="button"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                <?php echo htmlspecialchars($email); ?>
            </span>
            <div class="position-relative d-inline-block">
                <img class="img-profile rounded-circle" src="<?php echo $image ? get_drive_image_url($image) : '../img/undraw_profile.svg'; ?>" style="width: 40px; height: 40px;">
                <!-- Notification badge placeholder -->
                <span id="notifBadge" class="badge badge-danger badge-counter" 
                      style="position: absolute; top: 0; right: 0; font-size: 10px; padding: 3px 6px; border-radius: 50%; display:none;">
                    0
                </span>
            </div>
        </a>

        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
            aria-labelledby="userDropdown">
            <a class="dropdown-item" href="#">
                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                Profile
            </a>
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#settingsModal">
                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                Settings
            </a>
            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#" data-toggle="modal" data-target="#notificationModal">
                <span>
                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                    Notifications
                </span>
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
<div class="modal fade" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notifications</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Notifications content will be loaded here by AJAX -->
                <p>Loading notifications...</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" type="button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ready to Leave?</h5>
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
                                        <input class="form-control" id="userEmail" name="userEmail" type="email" value="<?php echo $_SESSION['student']; ?>" readonly>
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
        const modalBody = document.querySelector('#notificationModal .modal-body');
        modalBody.innerHTML = '<p>Loading notifications...</p>';
        fetch('functions/load_notifications.php')
            .then(response => response.text())
            .then(html => {
                modalBody.innerHTML = html;
                updateNotificationBadge(); // Refresh badge after loading notifications
            })
            .catch(() => {
                modalBody.innerHTML = '<p>Failed to load notifications.</p>';
            });
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
            const studentID = form.getAttribute('data-student-id');

            if (!notificationId || !studentID) {
                alert('Missing required data.');
                console.error('Missing data:', { notificationId, studentID });
                return;
            }

            console.log('Sending mark as read request:', { notificationId, studentID });

            // Prepare data to send
            const formData = new URLSearchParams();
            formData.append('notification_id', notificationId);
            formData.append('studentID', studentID);

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

    document.body.addEventListener('submit', function(e) {
    if (e.target.classList.contains('mark-all-read-form')) {
        e.preventDefault();

        const form = e.target;
        const studentID = form.getAttribute('data-student-id');
        const department = form.getAttribute('data-department');

        if (!studentID || !department) {
            alert('Missing student ID or department.');
            return;
        }

        const formData = new URLSearchParams();
        formData.append('studentID', studentID);
        formData.append('department', department);

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

});
</script>

