<?php
    session_start();
    // Include the database connection file from the parent directory
    include_once("../includes/connection.php");

    // Get the department value stored in the session
    $department = $_SESSION['department'];
    $activeSemester = $_SESSION['semester'];
    $activeSchoolYear = $_SESSION['schoolYear'];

    $courseQuery = "SELECT * FROM course_list WHERE department = ?";
    $courseStmt = $connect->prepare($courseQuery);

    $allCourses = [];

    if ($courseStmt) {
        $courseStmt->bind_param("s", $department);
        $courseStmt->execute();
        $result = $courseStmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $allCourses[] = $row;
        }

        $courseStmt->close();
    } else {
        echo "Error preparing statement: " . $connect->error;
    }

    $totalEnrolled = 0;
    $enrolledCounts = [];
    $enrolledQuery = "SELECT course, COUNT(DISTINCT studentID) AS total 
                      FROM student_masterlist 
                      WHERE course IN (
                          SELECT course FROM course_list WHERE department = ? AND semester = ? AND schoolYear = ?) 
                      GROUP BY course";
    $enrolledStmt = $connect->prepare($enrolledQuery);
    if ($enrolledStmt) {
        $enrolledStmt->bind_param("sss", $department, $activeSemester, $activeSchoolYear);
        $enrolledStmt->execute();
        $result = $enrolledStmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $enrolledCounts[$row['course']] = $row['total'];
            $totalEnrolled += $row['total'];
        }
        $enrolledStmt->close();
    }

    $totalDeployed = 0;
    $deployedCounts = [];
    $deployedQuery = "SELECT course, COUNT(DISTINCT studentID) AS total 
                      FROM studentinfo 
                      WHERE status = 'Deployed' AND course IN (
                          SELECT course FROM course_list WHERE department = ? AND semester = ? AND school_year = ?) 
                      GROUP BY course";
    $deployedStmt = $connect->prepare($deployedQuery);
    if ($deployedStmt) {
        $deployedStmt->bind_param("sss", $department, $activeSemester, $activeSchoolYear);
        $deployedStmt->execute();
        $result = $deployedStmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $deployedCounts[$row['course']] = $row['total'];
            $totalDeployed += $row['total'];
        }
        $deployedStmt->close();
    }

    $totalNotDeployed = $totalEnrolled - $totalDeployed;

    $deployRateCount = [];
    $deployRateQuery = "
        SELECT course, dateStarted, COUNT(DISTINCT studentID) AS total
            FROM company_info
        WHERE status = 'Approved' AND course IN (
            SELECT course FROM course_list WHERE department = ? AND semester = ? AND schoolYear = ?
        )
        GROUP BY course, dateStarted
    ";
    $deployRateStmt = $connect->prepare($deployRateQuery);
    if ($deployRateStmt) {
        $deployRateStmt->bind_param("sss", $department, $activeSemester, $activeSchoolYear);
        $deployRateStmt->execute();
        $result = $deployRateStmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $course = trim($row['course']); // ✅ Trim to ensure consistency
            $date = date('Y-m-d', strtotime($row['dateStarted']));
            $total = (int)$row['total'];
            $deployRateCount[$course][$date] = $total;
        }

        $deployRateStmt->close();
    } else {
        echo "Error preparing deploy rate statement: " . $connect->error;
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <?php include("../elements/meta.php"); ?>
        <title>OJT COORDINATOR PORTAL</title>
        <?php include("embed.php"); ?>
        <link rel="stylesheet" href="../assets/css/new-style.css">
    </head>
    <body id="page-top">
        <div id="wrapper">
            <aside id="sidebar" class="expand">
                <?php include('../elements/cood_sidebar.php') ?>
            </aside>

            <div class="main">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-2 static-top shadow">
                    <h4 class="my-0 mr-auto font-weight-bold text-dark ml-3">Dashboard</h4>
                    <?php include('../elements/cood_navbar_user_info.php')?>
                </nav>

                <div id="content" class="py-2">
                    <div class="row m-1">
                        <div class="col-md-8">
                            <div class="row mb-4">
                                <h4>Total Enrolled: <?= $totalEnrolled ?></h4>
                                <?php foreach ($allCourses as $course): ?>
                                    <?php
                                        // Count enrolled students per course
                                        $countQuery = "SELECT COUNT(DISTINCT studentID) AS total FROM student_masterlist WHERE course = ? AND semester = ? AND schoolYear = ?";
                                        $countStmt = $connect->prepare($countQuery);
                                        $count = 0;

                                        if ($countStmt) {
                                            $countStmt->bind_param("sss", $course['course'], $activeSemester, $activeSchoolYear);
                                            $countStmt->execute();
                                            $countResult = $countStmt->get_result();
                                            if ($row = $countResult->fetch_assoc()) {
                                                $count = $row['total'];
                                            }
                                            $countStmt->close();
                                        }
                                    ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card shadow border-primary">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= htmlspecialchars($course['course']) ?></h5>
                                                <p class="card-text">Enrolled Students: <?= $count ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="row mb-4">
                                <h4>Deployed: <?= $totalDeployed ?></h4>
                                <?php foreach ($allCourses as $course): ?>
                                    <?php
                                        // Count deployed students per course
                                        $deployedCountQuery = "SELECT COUNT(DISTINCT studentID) AS total 
                                            FROM studentinfo 
                                            WHERE course = ? AND status = 'Deployed' AND semester = ? AND school_year = ?";
                                        $deployedCountStmt = $connect->prepare($deployedCountQuery);
                                        $deployedCount = 0;

                                        if ($deployedCountStmt) {
                                            $deployedCountStmt->bind_param("sss", $course['course'], $activeSemester, $activeSchoolYear);
                                            $deployedCountStmt->execute();
                                            $deployedCountResult = $deployedCountStmt->get_result();
                                            if ($row = $deployedCountResult->fetch_assoc()) {
                                                $deployedCount = $row['total'];
                                            }
                                            $deployedCountStmt->close();
                                        }
                                    ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card shadow border-success">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= htmlspecialchars($course['course']) ?></h5>
                                                <p class="card-text">Deployed Students: <?= $deployedCount ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="row mb-4">
                                <h4>Not Deployed: <?= $totalNotDeployed ?></h4>
                                <?php foreach ($allCourses as $course): ?>
                                    <?php
                                        $courseName = $course['course'];
                                        $enrolled = $enrolledCounts[$courseName] ?? 0;
                                        $deployed = $deployedCounts[$courseName] ?? 0;
                                        $notDeployed = $enrolled - $deployed;
                                    ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card shadow border-danger">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= htmlspecialchars($courseName) ?></h5>
                                                <p class="card-text">Not Deployed: <?= $notDeployed ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="row">
                                <!-- Include the calendar here -->
                                <?php include('calendar.php'); ?>
                            </div>
                            <hr>
                            <div class="row">
                                <div>
                                    <span>Activity Log</span>
                                </div>
                                <div id="activityLogSection">
                                    <!-- Activity log will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row m-1 align-items-center">
                        <div class="col-md-10">
                            <h4>Analytics</h4>
                        </div>
                        
                        <div class="card shadow mb-2">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Deployment Count</h5>
                                <div class="row mb-3">
                                    <div class="col-md-9">
                                        <canvas id="deploymentLineChart" height="100"></canvas>
                                    </div>
                                    <div class="col-md-2 ms-auto">
                                        <div class="row mb-3">
                                            <label for="courseSelect" class="form-label">Select Course</label>
                                            <select id="courseSelect" class="form-select">
                                                <option value="">All Courses</option>
                                                <?php foreach ($allCourses as $course): ?>
                                                    <option value="<?= htmlspecialchars(trim($course['course'])) ?>">
                                                        <?= htmlspecialchars($course['course']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="row mb-3">
                                            <label for="rangeSelect" class="form-label">Select Time Range</label>
                                            <select id="rangeSelect" class="form-select">
                                                <option value="daily">Daily</option>
                                                <option value="weekly">Weekly</option>
                                                <option value="monthly">Monthly</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card shadow">
                            <div class="card-body">
                                <div class="col-md-2 ms-auto">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="chartTypeSwitch" style="margin-top: 10px;">
                                        <label class="form-check-label ms-3" for="chartTypeSwitch">Switch to Pie Chart</label>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Total Bar Chart (Grouped for BSCS) -->
                                    <div class="col-md-6 mb-4">
                                        <div class="card shadow">
                                            <div class="card-body">
                                                <h5 class="card-title">Overall Analytics</h5>
                                                <canvas id="totalChart" class="fixed-chart"></canvas>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Pie Charts: Enrolled, Deployed, Undeployed -->
                                    <?php
                                        $courseLabels = [];
                                        $enrolledData = [];
                                        $deployedData = [];
                                        $notDeployedData = [];

                                        foreach ($allCourses as $course) {
                                            $courseName = $course['course'];
                                            $courseLabels[] = $courseName;
                                            $enrolled = $enrolledCounts[$courseName] ?? 0;
                                            $deployed = $deployedCounts[$courseName] ?? 0;
                                            $notDeployed = $enrolled - $deployed;
                                            $enrolledData[] = $enrolled;
                                            $deployedData[] = $deployed;
                                            $notDeployedData[] = $notDeployed;
                                        }
                                    ?>

                                    <?php
                                        $pieCharts = [
                                            ['id' => 'enrolledChart', 'label' => 'Total Enrolled', 'data' => $enrolledData],
                                            ['id' => 'deployedChart', 'label' => 'Total Deployed', 'data' => $deployedData],
                                            ['id' => 'notDeployedChart', 'label' => 'Total Undeployed', 'data' => $notDeployedData],
                                        ];
                                        foreach ($pieCharts as $chart):
                                    ?>
                                        <div class="col-md-6 mb-4">
                                            <div class="card shadow">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?= $chart['label'] ?></h5>
                                                    <!-- Chart container for JS to append percentages -->
                                                    <div class="chart-container">
                                                        <canvas id="<?= $chart['id'] ?>" class="fixed-chart"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </body>

    <style>
        .form-switch .form-check-input {
            width: 50px;
            height: 25px;
        }
        .fixed-chart {
            height: 300px !important;
            max-height: 300px;
        }
    </style>

    <?php
        $courseLabels = array_column($allCourses, 'course');
        $enrolledData = array_map(function($c) use ($enrolledCounts) {
            return $enrolledCounts[$c] ?? 0;
        }, $courseLabels);

        $deployedData = array_map(function($c) use ($deployedCounts) {
            return $deployedCounts[$c] ?? 0;
        }, $courseLabels);

        $notDeployedData = [];
        foreach ($courseLabels as $c) {
            $e = $enrolledCounts[$c] ?? 0;
            $d = $deployedCounts[$c] ?? 0;
            $notDeployedData[] = $e - $d;
        }
    ?>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Bootstrap JS (v5+) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>

    <script>
        function getRandomColor() {
            const letters = '0123456789ABCDEF';
            return '#' + Array.from({ length: 6 }, () => letters[Math.floor(Math.random() * 16)]).join('');
        }

        function createChart(ctx, type, label, labels, data) {
            const isPie = type === 'pie';
            const isBar = type === 'bar';

            const chart = new Chart(ctx, {
                type: type,
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: isPie ? labels.map(() => getRandomColor()) : ['#007bff', '#28a745', '#dc3545'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        datalabels: {
                            display: false
                        },
                        legend: {
                            display: isPie,
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { size: 10 } }
                        },
                        title: {
                            display: true,
                            text: label
                        }
                    },
                    scales: isPie ? {} : {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                },
                plugins: [ChartDataLabels]
            });

            // Add percentage info below chart
            if (isPie || isBar) {
                const total = data.reduce((sum, val) => sum + val, 0);
                const container = ctx.canvas.parentNode;

                let percentDiv = container.querySelector('.percentage-info');
                if (!percentDiv) {
                    percentDiv = document.createElement('div');
                    percentDiv.className = 'percentage-info';
                    percentDiv.style.fontSize = '12px';
                    percentDiv.style.marginTop = '8px';
                    percentDiv.style.lineHeight = '1.4';
                    container.appendChild(percentDiv);
                } else {
                    percentDiv.innerHTML = '';
                }

                labels.forEach((lbl, i) => {
                    const percent = total ? ((data[i] / total) * 100).toFixed(1) : '0.0';
                    const entry = document.createElement('div');
                    entry.innerText = `${lbl}: ${percent}%`;
                    percentDiv.appendChild(entry);
                });
            }

            return chart;
        }

        // Overall Bar Chart
        const totalChartCtx = document.getElementById('totalChart').getContext('2d');
        const totalEnrolled = <?= $totalEnrolled ?>;
        const totalDeployed = <?= $totalDeployed ?>;
        const totalNotDeployed = <?= $totalNotDeployed ?>;
        createChart(totalChartCtx, 'bar', 'Overall', ['Deployed', 'Not Deployed'], [totalDeployed, totalNotDeployed]);

        // Course data
        const courseLabels = <?= json_encode($courseLabels) ?>;
        const enrolledData = <?= json_encode($enrolledData) ?>;
        const deployedData = <?= json_encode($deployedData) ?>;
        const notDeployedData = <?= json_encode($notDeployedData) ?>;

        // Chart switch preference
        const chartTypeSwitch = document.getElementById('chartTypeSwitch');
        let savedType = localStorage.getItem('chartType');

        if (!savedType) {
            savedType = 'bar'; // default to bar
            localStorage.setItem('chartType', 'bar');
        }

        chartTypeSwitch.checked = savedType === 'pie';
        chartTypeSwitch.nextElementSibling.innerText = savedType === 'pie' ? 'Switch to Bar Graph' : 'Switch to Pie Chart';

        const toggleableCharts = [
            {
                ctx: document.getElementById('enrolledChart').getContext('2d'),
                label: 'Total Enrolled by Course',
                labels: courseLabels,
                data: enrolledData,
                instance: null
            },
            {
                ctx: document.getElementById('deployedChart').getContext('2d'),
                label: 'Total Deployed by Course',
                labels: courseLabels,
                data: deployedData,
                instance: null
            },
            {
                ctx: document.getElementById('notDeployedChart').getContext('2d'),
                label: 'Total Undeployed by Course',
                labels: courseLabels,
                data: notDeployedData,
                instance: null
            }
        ];

        // Create charts with savedType
        toggleableCharts.forEach(chart => {
            chart.instance = createChart(chart.ctx, savedType, chart.label, chart.labels, chart.data);
        });

        // Toggle chart type and save preference
        chartTypeSwitch.addEventListener('change', function () {
            const newType = this.checked ? 'pie' : 'bar';
            localStorage.setItem('chartType', newType);
            this.nextElementSibling.innerText = newType === 'pie' ? 'Switch to Bar Graph' : 'Switch to Pie Chart';

            toggleableCharts.forEach(chart => {
                chart.instance.destroy();
                chart.instance = createChart(chart.ctx, newType, chart.label, chart.labels, chart.data);
            });
        });

        // Individual bar charts per course
        <?php foreach ($allCourses as $index => $course): ?>
            const ctx<?= $index ?> = document.getElementById('chart_<?= $index ?>').getContext('2d');
            createChart(ctx<?= $index ?>, 'bar', "<?= addslashes($course['course']) ?> Breakdown", ['Enrolled', 'Deployed', 'Not Deployed'], [
                <?= $enrolledCounts[$course['course']] ?? 0 ?>,
                <?= $deployedCounts[$course['course']] ?? 0 ?>,
                <?= ($enrolledCounts[$course['course']] ?? 0) - ($deployedCounts[$course['course']] ?? 0) ?>
            ]);
        <?php endforeach; ?>
    </script>

    <script>
        const rawData = <?= json_encode($deployRateCount); ?>;
        const ctx = document.getElementById('deploymentLineChart').getContext('2d');
        let chart = null;

        // Plugin to show "No data" message inside the chart area
        const noDataPlugin = {
            id: 'noDataPlugin',
            afterDraw(chart) {
                if (chart.data.datasets.length === 0 || chart.data.datasets[0].data.length === 0) {
                    const ctx = chart.ctx;
                    const width = chart.width;
                    const height = chart.height;
                    ctx.save();
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillStyle = '#666';
                    ctx.font = '16px sans-serif';
                    ctx.fillText('No deployment data found for the selected course/time range.', width / 2, height / 2);
                    ctx.restore();
                }
            }
        };

        function groupData(data, range) {
            const grouped = {};

            if (range === "weekly") {
                const dates = Object.keys(data).map(d => new Date(d)).sort((a, b) => a - b);
                if (dates.length === 0) return grouped;

                function getWeekStart(date) {
                    const d = new Date(date);
                    d.setHours(0, 0, 0, 0);
                    const day = d.getDay();
                    d.setDate(d.getDate() - day);
                    return d;
                }

                for (const dateStr in data) {
                    const dateObj = new Date(dateStr);
                    const weekStart = getWeekStart(dateObj);
                    const weekEnd = new Date(weekStart);
                    weekEnd.setDate(weekEnd.getDate() + 6);

                    const options = { month: 'short', day: 'numeric' };
                    const weekLabel = weekStart.toLocaleDateString(undefined, options) + " to " + weekEnd.toLocaleDateString(undefined, options);

                    grouped[weekLabel] = (grouped[weekLabel] || 0) + data[dateStr];
                }

                return grouped;
            }

            for (const dateStr in data) {
                const count = data[dateStr];
                const dateObj = new Date(dateStr);
                let key = "";

                if (range === "monthly") {
                    key = dateObj.getFullYear() + "-" + String(dateObj.getMonth() + 1).padStart(2, "0");
                } else {
                    key = dateStr;
                }

                grouped[key] = (grouped[key] || 0) + count;
            }

            return grouped;
        }

        function renderChart(course = "", timeRange = "daily") {
            let data = {};

            if (course && rawData.hasOwnProperty(course)) {
                data = groupData(rawData[course], timeRange);
            } else if (!course) {
                for (const c in rawData) {
                    const grouped = groupData(rawData[c], timeRange);
                    for (const date in grouped) {
                        data[date] = (data[date] || 0) + grouped[date];
                    }
                }
            } else {
                data = {};
            }

            const labels = Object.keys(data);

            if (timeRange === "weekly") {
                labels.sort((a, b) => {
                    const parseDate = str => {
                        const [start, ] = str.split(" to ");
                        return new Date(start + ", " + new Date().getFullYear());
                    };
                    return parseDate(a) - parseDate(b);
                });
            } else {
                labels.sort((a, b) => new Date(a) - new Date(b));
            }

            const values = labels.map(date => data[date]);

            const formattedData = labels.map(label => {
                if (timeRange === "weekly") {
                    const [startStr, endStr] = label.split(" to ");
                    const year = new Date().getFullYear();
                    const startDate = new Date(startStr + ", " + year);
                    const endDate = new Date(endStr + ", " + year);
                    const midTime = (startDate.getTime() + endDate.getTime()) / 2;
                    return { x: new Date(midTime), y: data[label] };
                } else if (timeRange === "monthly") {
                    return { x: new Date(label + "-01"), y: data[label] };
                } else {
                    return { x: new Date(label), y: data[label] };
                }
            });

            if (chart) chart.destroy();

            // If no data, create an empty dataset to trigger noDataPlugin message
            if (labels.length === 0) {
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        datasets: []
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: { enabled: false }
                        },
                        scales: {
                            x: { display: false },
                            y: { display: false }
                        }
                    },
                    plugins: [noDataPlugin]
                });
                return;
            }

            let minDate, maxDate;
            if (timeRange === "weekly") {
                const firstLabel = labels[0];
                const [startStr, ] = firstLabel.split(" to ");
                const year = new Date().getFullYear();
                minDate = new Date(startStr + ", " + year);
                minDate.setDate(minDate.getDate() - 7);

                const lastLabel = labels[labels.length - 1];
                const [, endStr] = lastLabel.split(" to ");
                maxDate = new Date(endStr + ", " + year);
                maxDate.setDate(maxDate.getDate() + 1);
            } else if (timeRange === "monthly") {
                minDate = new Date(labels[0] + "-01");
                minDate.setMonth(minDate.getMonth() - 1);
                maxDate = new Date(labels[labels.length - 1] + "-01");
                maxDate.setMonth(maxDate.getMonth() + 1);
            } else {
                minDate = new Date(labels[0]);
                maxDate = new Date(labels[labels.length - 1]);
                minDate.setDate(minDate.getDate() - 1);
                maxDate.setDate(maxDate.getDate() + 1);
            }

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: [{
                        label: 'Deployment Count',
                        data: formattedData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    parsing: false,
                    plugins: {
                        legend: { display: true },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                title: ctx => {
                                    const label = ctx[0].label;
                                    if (timeRange === "weekly") {
                                        return labels[ctx[0].dataIndex];
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: timeRange === "monthly" ? "month" :
                                    timeRange === "weekly" ? "week" : "day",
                                tooltipFormat: 'PP',
                                displayFormats: {
                                    day: 'MMM dd',
                                    week: 'MMM dd',
                                    month: 'MMM yyyy'
                                }
                            },
                            min: minDate,
                            max: maxDate,
                            title: {
                                display: true,
                                text: 'Date',
                                font: { weight: 'bold' }
                            },
                            ticks: { autoSkip: false },
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            suggestedMin: 0,
                            ticks: {
                                stepSize: 1,
                                callback: val => Number.isInteger(val) ? val : null
                            },
                            title: {
                                display: true,
                                text: 'Student Count',
                                font: { weight: 'bold' }
                            }
                        }
                    }
                },
                plugins: [noDataPlugin]
            });
        }

        document.getElementById('courseSelect').addEventListener('change', e => {
            renderChart(e.target.value, document.getElementById('rangeSelect').value);
        });

        document.getElementById('rangeSelect').addEventListener('change', e => {
            renderChart(document.getElementById('courseSelect').value, e.target.value);
        });

        // Initial render
        renderChart();

        // Reload page every hour (3600000 ms)
        setInterval(() => {
            location.reload();
        }, 3600000);
    </script>

    <script>
        function loadActivityLog() {
            fetch('activity-log.php')  // adjust the path if needed
            .then(response => response.text())
            .then(data => {
                document.getElementById('activityLogSection').innerHTML = data;
            })
            .catch(error => console.error('Error loading activity log:', error));
        }

        // Initial load
        loadActivityLog();

        // Reload every 5 seconds (5000 milliseconds) for real-time updates
        setInterval(loadActivityLog, 5000);
    </script>

</html>