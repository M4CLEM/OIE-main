<?php
    $cipa = $_SESSION['CIPA'];

    $departmentQuery = $connect->prepare("SELECT * FROM department_list");
    $departmentQuery->execute();
    $result = $departmentQuery->get_result();
?>

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
            <a class="dropdown-item" href="#">
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
                            <form action="">
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
                                                <?php while ($row = $result->fetch_assoc()): ?>
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
    })
</script>