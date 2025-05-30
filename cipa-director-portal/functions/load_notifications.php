<?php
    session_start();
    include("../../includes/connection.php");

    $cipa = $_SESSION['CIPA'];

    if (!$cipa) {
        echo json_encode(['unreadCount' => 0]);
        exit;
    }

    $employeeQuery = "SELECT employeeNumber FROM staff_list WHERE email = ?";
    $employeeStmt = $connect->prepare($employeeQuery);
    $employeeStmt->bind_param("s", $cipa);
    $employeeStmt->execute();
    $result = $employeeStmt->get_result();
    $employee = $result->fetch_assoc();

    $employeeNumber = $employee['employeeNumber'];

    $notificationQuery = "SELECT * FROM event_reminder ORDER BY datePosted DESC";
    $notificationStmt = $connect->prepare($notificationQuery);
    $notificationStmt->execute();
    $notificationResult = $notificationStmt->get_result();

    if ($notificationResult->num_rows > 0): ?>
    <form class="mark-all-read-form mb-3 text-right" data-employee-number="<?= $employeeNumber?>">
        <button type="submit" class="btn btn-sm btn-success">Mark All as Read</button>
    </form>

    <?php while ($row = $notificationResult->fetch_assoc()): 
        $viewers = json_decode($row['viewer'], true) ?? [];
        $hasRead = in_array($employeeNumber, $viewers);
    ?>

        <div class="card mb-3 border-<?php echo $hasRead ? 'secondary' : 'primary'; ?>">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                <p class="card-text"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                <p class="card-text">
                    <small class="text-muted">
                        From: <?php echo date("F j, Y", strtotime($row['datePosted'])); ?>
                        <?php if (!empty($row['endDate'])): ?>
                            – Until: <?php echo date("F j, Y", strtotime($row['endDate'])); ?>
                        <?php endif; ?>
                    </small>
                </p>
                <?php if (!$hasRead): ?>
                    <form class="mark-as-read-form" data-notification-id="<?php echo $row['id']; ?>" data-employee-number="<?php echo $employeeNumber; ?>">
                        <button type="submit" class="btn btn-sm btn-outline-primary">Mark as Read</button>
                    </form>
                <?php else: ?>
                    <span class="badge badge-secondary">Read</span>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No notifications available.</p>
<?php endif; ?>