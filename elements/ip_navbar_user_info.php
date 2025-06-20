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
                <?php (isset($_SESSION['IndustryPartner'])) ?> <?php echo $_SESSION['IndustryPartner']; ?></span>
            <img class="img-profile rounded-circle" src="../img/undraw_profile.svg">
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
                                        <input class="form-control" id="userEmail" name="userEmail" type="text" value="<?php echo $_SESSION['IndustryPartner'];?>" readonly>
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