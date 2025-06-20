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
                        <form action="" method="POST">
                            <div class="form-group">
                                <div class="row mb-1">
                                    <div class="col-md-3">
                                        <p>Email:</h5>
                                    </div>
                                    <div class="col-md-9">
                                        <input class="form-control" id="userEmail" name="userEmail" type="text" value="<?php echo $_SESSION['student'];?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row mb-1">
                                    <div class="col-md-3">
                                        <p>New Password:</h5>
                                    </div>
                                    <div class="col-md-9">
                                        <input class="form-control" id="userPassword" name="userPassword" type="password" placeholder="New Password">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row mb-1">
                                    <div class="col-md-3">
                                        <p>Confirm Password:</h5>
                                    </div>
                                    <div class="col-md-9">
                                        <input class="form-control" id="userConfirmPassword" name="userConfirmPassword" type="password" placeholder="Confirm Password">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <div class="row mb-1">
                                    <div class="col-md-3">
                                        <p>Send OTP</h5>
                                    </div>
                                    <div class="col-md-9">
                                        <input class="form-control" id="userOTP" name="userOTP" type="text" placeholder="Enter OTP">
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    
                                </div>
                            </div>
                            <div class="form-group text-center mt-3">
                                <button type="button" class="btn btn-primary" name="sendOTP" id="sendOTP">Send OTP</button>
                                <button type="button" class="btn btn-success" name="verifyOTP" id="verifyOTP">Verify OTP</button>
                            </div>
                            <hr>
                            <div class="form-group text-right mt-3">
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

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

