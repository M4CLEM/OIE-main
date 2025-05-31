<?php
session_start();
include("../../includes/connection.php");

$email = $_SESSION['student'] ?? null;

if (!$email) {
    echo "<p>User not authenticated.</p>";
    exit;
}

$query = "SELECT studentID, department FROM studentinfo WHERE email = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$studentID = $user['studentID'];
$department = $user['department'];

$notificationQuery = "SELECT * FROM event_reminder WHERE department = ? OR department = 'All' ORDER BY datePosted DESC";
$notificationStmt = $connect->prepare($notificationQuery);
$notificationStmt->bind_param("s", $department);
$notificationStmt->execute();
$notificationResult = $notificationStmt->get_result();

if ($notificationResult->num_rows > 0): ?>
    <form class="mark-all-read-form mb-3 text-right" data-student-id="<?= $studentID ?>" data-department="<?= $department ?>">
        <button type="submit" class="btn btn-sm btn-success">Mark All as Read</button>
    </form>


    <?php while ($row = $notificationResult->fetch_assoc()): 
        $viewers = json_decode($row['viewer'], true) ?? [];
        $hasRead = in_array($studentID, $viewers);
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
                    <form class="mark-as-read-form" data-notification-id="<?php echo $row['id']; ?>" data-student-id="<?php echo $studentID; ?>">
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
