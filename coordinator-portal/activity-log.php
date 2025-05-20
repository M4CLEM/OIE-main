<?php
// Make sure session is started if this file is accessed standalone
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the existing database connection
include_once("../includes/connection.php");

// Grab department, semester, and schoolYear from session for filtering
$department = $_SESSION['department'] ?? '';
$activeSemester = $_SESSION['semester'] ?? '';
$activeSchoolYear = $_SESSION['schoolYear'] ?? '';

// Prepare query to fetch latest 2 logs filtered by department, semester, and schoolYear
$query = "SELECT description, timestamp 
          FROM activitylog 
          WHERE department = ? AND semester = ? AND schoolYear = ?
          ORDER BY timestamp DESC 
          LIMIT 2";

$stmt = $connect->prepare($query);
$stmt->bind_param("sss", $department, $activeSemester, $activeSchoolYear);
$stmt->execute();
$result = $stmt->get_result();

?>

<style>
    #activityLogContainer {
        width: 100%;
        max-height: 100px; /* Show 1–2 entries */
        overflow-y: auto;
        margin-top: 10px;
        padding: 10px;
        background-color: #ffffff;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 0.85em;
    }

    .activity-item {
        border-bottom: 1px solid #eee;
        padding: 6px 0;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-time {
        font-size: 0.75em;
        color: #888;
    }

    .activity-desc {
        font-weight: 500;
    }
</style>

<div id="activityLogContainer">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($activity = $result->fetch_assoc()): ?>
            <div class="activity-item">
                <div class="activity-desc"><?php echo htmlspecialchars($activity['description']); ?></div>
                <div class="activity-time"><?php echo date("M d, Y - h:i A", strtotime($activity['timestamp'])); ?></div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="activity-item">No activity logs found.</div>
    <?php endif; ?>
</div>
