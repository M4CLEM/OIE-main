<style>
    .modal-body.no-padding {
        padding: 0;
        margin: 0;
        min-height: 400px;
    }

    /* Smaller, tighter nav pills */
    .nav-pills.modal-header-pills .nav-link {
        padding: 2px 8px !important;
        /* Less padding */
        font-size: 1rem !important;
        /* Smaller font */
        line-height: 1 !important;
        /* Tighter line spacing */
        border: 1px solid #dee2e6;
        border-radius: 0;
        margin: 0;
        height: auto !important;
        /* Prevent forced height */
    }

    .nav-pills.modal-header-pills .nav-link+.nav-link {
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
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                <?php echo $_SESSION['coordinator']; ?>
            </span>
            <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
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
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="../logout.php" data-toggle="modal" data-target="#logoutModal">
                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                Logout
            </a>
        </div>
    </li>
</ul>

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
                            <p class="small">Active Academic Year: <?php echo $_SESSION['semester'] ?> - <?php echo $_SESSION['schoolYear'] ?></p>
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
                                        <input class="form-control" id="userEmail" name="userEmail" type="email" value="<?php echo $_SESSION['coordinator']; ?>" readonly>
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