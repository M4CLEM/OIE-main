<?php
    // Fetch events for the current department
    $events = [];
    $query = "SELECT * FROM event_reminder WHERE department = ?";
    $stmt = $connect->prepare($query);
    $stmt->bind_param("s", $department);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
        // Assumes $connect and $department are already available from the main file
        $title = mysqli_real_escape_string($connect, $_POST['title']);
        $description = mysqli_real_escape_string($connect, $_POST['description']);
        $endDate = $_POST['endDate'];
        $datePosted = date('Y-m-d'); // current date

        $insertQuery = "INSERT INTO event_reminder (title, description, department, datePosted, endDate)
                    VALUES ('$title', '$description', '$department', '$datePosted', '$endDate')";
    
        if (mysqli_query($connect, $insertQuery)) {
            echo "<script>
                alert('Reminder added successfully!');
                window.location.href = window.location.href; // Refresh calendar
            </script>";
        } else {
            echo "<script>
                alert('Error: " . mysqli_error($connect) . "');
            </script>";
        }
    }
?>

<div id="calendarContainer">
    <style>
        #calendarContainer {
            width: 100%;
            max-width: 650px;
            margin: 0 auto;
            padding: 10px 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .calendar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .calendar-header button {
            padding: 4px 8px;
            font-size: 14px;
        }
        #calendarTitle {
            flex-grow: 1;
            text-align: center;
            font-weight: bold;
            min-width: 140px;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            width: 14.28%;
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #ddd;
            vertical-align: top;
            position: relative;
            font-size: 13px;
            height: 55px;
        }
        td:hover {
            background-color: #f0f0f0;
            cursor: pointer;
        }
        .today {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            border-radius: 0;
        }
        .event-title {
            display: block;
            margin-top: 3px;
            font-size: 0.75rem;
            background: #ffc107;
            border-radius: 4px;
            padding: 1.5px 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }
        .event-title.more {
            background-color: #6c757d;
            cursor: default;
        }
        .event-span {
            background-color: #fff3cd;
            border-radius: 4px;
            margin-top: 3px;
            padding: 2px 3px;
            font-size: 11px;
            color: #856404;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            cursor: default;
        }
        #addEventBtn {
            margin-top: 10px;
            padding: 6px 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }
        #addEventBtn:hover {
            background-color: #218838;
        }
    </style>

    <div class="calendar-header">
        <button id="prevMonth" aria-label="Previous month">&lt;</button>
        <span id="calendarTitle" aria-live="polite"></span>
        <button id="nextMonth" aria-label="Next month">&gt;</button>
    </div>
    <table role="grid" aria-label="Calendar">
        <thead>
            <tr>
                <th scope="col">Sun</th><th scope="col">Mon</th><th scope="col">Tue</th><th scope="col">Wed</th>
                <th scope="col">Thu</th><th scope="col">Fri</th><th scope="col">Sat</th>
            </tr>
        </thead>
        <tbody id="calendarBody"></tbody>
    </table>
    <button id="addEventBtn" data-bs-toggle="modal" data-bs-target="#addEventModal">Add Reminder</button>

    <script>
        const events = <?php echo json_encode($events); ?>;
        let currentDate = new Date();

        // Helper to parse YYYY-MM-DD to Date
        function parseDate(dateStr) {
            const parts = dateStr.split("-");
            return new Date(parts[0], parts[1]-1, parts[2]);
        }

        // Helper to format Date to YYYY-MM-DD
        function formatDate(date) {
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const dd = String(date.getDate()).padStart(2, '0');
            return `${date.getFullYear()}-${mm}-${dd}`;
        }

        // Check if date1 <= date2
        function dateLE(date1, date2) {
            return date1.getTime() <= date2.getTime();
        }

        // Main calendar update
        function updateCalendar() {
            const month = currentDate.getMonth();
            const year = currentDate.getFullYear();

            const monthNames = ["January", "February", "March", "April", "May", "June", "July",
                "August", "September", "October", "November", "December"];
            document.getElementById('calendarTitle').innerText = `${monthNames[month]} ${year}`;

            const firstDay = new Date(year, month, 1).getDay();
            const totalDays = new Date(year, month + 1, 0).getDate();

            const calendarBody = document.getElementById('calendarBody');
            calendarBody.innerHTML = "";

            let row = document.createElement("tr");
            let today = new Date();

            // Track event spans so we only show "+N title1, title2" once per event span
            // We'll create a Map: key = event id, value = boolean shown or not
            // If your events do not have unique ids, use index as id here.
            // Assume events have "id" property; if not, add index as id:
            events.forEach((e,i) => { if(!e.id) e.id = i; });

            let eventShownInSpan = new Set();

            // Empty cells before first day
            for (let i = 0; i < firstDay; i++) {
                row.appendChild(document.createElement("td"));
            }

            for (let day = 1; day <= totalDays; day++) {
                const cell = document.createElement("td");
                const cellDate = new Date(year, month, day);
                const formatted = formatDate(cellDate);

                if (today.getDate() === day && today.getMonth() === month && today.getFullYear() === year) {
                    cell.classList.add("today");
                }

                // Find all events active this date
                const activeEvents = events.filter(e => {
                    const start = parseDate(e.datePosted);
                    const end = parseDate(e.endDate);
                    return dateLE(start, cellDate) && dateLE(cellDate, end);
                });

                cell.innerHTML = `<div class="day-number">${day}</div>`;

                // For each active event, decide if this is the *start* cell of event span in this month
                // If yes, display "+N title1, title2" (or event titles)
                // Otherwise just mark cell as ongoing (small dot or subtle highlight)

                activeEvents.forEach(event => {
                    const start = parseDate(event.datePosted);
                    const end = parseDate(event.endDate);

                    // Only show summary on first visible date of event in calendar
                    if (eventShownInSpan.has(event.id)) {
                        // Already shown, so just mark with a subtle dot
                        // Create a small dot or subtle background
                        const dot = document.createElement('span');
                        dot.style.display = 'inline-block';
                        dot.style.width = '6px';
                        dot.style.height = '6px';
                        dot.style.borderRadius = '50%';
                        dot.style.backgroundColor = '#ffc107';
                        dot.style.marginRight = '2px';
                        dot.title = event.title;
                        cell.appendChild(dot);
                    } else {
                        // Check if this cellDate is the earliest cell in current calendar month that is in the event duration
                        // If event started before this month, the first visible date is the 1st of month or later
                        let visibleStart = new Date(year, month, 1);
                        if (dateLE(start, visibleStart)) visibleStart = visibleStart;
                        else visibleStart = start;

                        if (cellDate.getTime() === visibleStart.getTime()) {
                            // Show the summary text "+N title1, title2"
                            // But what if multiple events on same start day? We'll handle after

                            // For simplicity, just mark eventShownInSpan so we don't show multiple summaries for same event
                            eventShownInSpan.add(event.id);
                        } else {
                            // Not first visible day, so just dot
                            const dot = document.createElement('span');
                            dot.style.display = 'inline-block';
                            dot.style.width = '6px';
                            dot.style.height = '6px';
                            dot.style.borderRadius = '50%';
                            dot.style.backgroundColor = '#ffc107';
                            dot.style.marginRight = '2px';
                            dot.title = event.title;
                            cell.appendChild(dot);
                        }
                    }
                });

                // Now after loop, check if any active events start today in this calendar, and if so show summary label for all those events on this cell.

                const startingEvents = activeEvents.filter(e => {
                    const start = parseDate(e.datePosted);
                    let visibleStart = new Date(year, month, 1);
                    if (dateLE(start, visibleStart)) visibleStart = visibleStart;
                    else visibleStart = start;
                    return cellDate.getTime() === visibleStart.getTime();
                });

                if (startingEvents.length > 0) {
                    // Show summary text with +N and titles comma separated
                    // The "+N" is count of events starting today in calendar
                    const count = startingEvents.length;
                    const titles = startingEvents.map(e => e.title).join(', ');

                    const summary = document.createElement('span');
                    summary.className = 'event-span';
                    summary.innerText = `+${count} ${titles}`;
                    summary.style.cursor = 'default';
                    cell.appendChild(summary);

                    // Clicking summary or cell shows modal with descriptions of all events on that date
                    cell.addEventListener('click', () => {
                        const descriptions = startingEvents.map(e => `${e.title}:\n${e.description}`).join("\n\n");
                        document.getElementById("eventDescription").innerText = descriptions;
                        const modal = new bootstrap.Modal(document.getElementById('eventModal'));
                        modal.show();
                    });
                } else if (activeEvents.length > 0 && startingEvents.length === 0) {
                    // If active but no event starting today, clicking cell shows modal with all events descriptions
                    cell.addEventListener('click', () => {
                        const descriptions = activeEvents.map(e => `${e.title}:\n${e.description}`).join("\n\n");
                        document.getElementById("eventDescription").innerText = descriptions;
                        const modal = new bootstrap.Modal(document.getElementById('eventModal'));
                        modal.show();
                    });
                }

                row.appendChild(cell);

                if ((firstDay + day) % 7 === 0) {
                    calendarBody.appendChild(row);
                    row = document.createElement("tr");
                }
            }

            if (row.children.length > 0) {
                calendarBody.appendChild(row);
            }
        }

        document.getElementById('prevMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            updateCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            updateCalendar();
        });

        document.addEventListener('DOMContentLoaded', updateCalendar);
    </script>
</div>

<div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEventReminder">Add Reminder</h5>
                        <button class="close" type="button" data-bs-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="">
                            <div class="form-group md-5">
                                <div class="col-md">
                                    <div>
                                        <span>Title</span>
                                    </div>
                                    <input class="form-control" type="text" name="title" id="title" placeholder="Title" required>
                                </div>
                            </div>

                            <div class="form-group md-5">
                                <div class="col-md">
                                    <div>
                                        <span>Description</span>
                                    </div>
                                    <textarea class="form-control" name="description" id="description" placeholder="Description..." rows="5" required></textarea>
                                </div>
                            </div>

                            <div class="form-group md-5">
                                <div class="col-md">
                                    <div>
                                        <span>Event/Reminder Duration</span>
                                    </div>
                                    <input class="form-control" type="date" name="endDate" id="endDate" required>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary btn-sm" name="save" type="submit"><span class="fa fa-save fw-fa"></span> Save</button>
                        <button class="btn btn-secondary btn-sm" type="button" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="eventModalLabel" class="modal-title">Event Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="eventDescription"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
