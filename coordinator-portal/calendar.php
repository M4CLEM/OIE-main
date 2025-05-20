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
        $datePosted = $_POST['postDate'];

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
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            width: 14.28%;
            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;
            vertical-align: top;
            position: relative;
            height: 45px;
            font-size: 13px;
            overflow: visible;
        }
        td:hover {
            background-color: #f0f0f0;
            cursor: pointer;
        }

        .event-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 1px;
            margin-top: 10px;
        }
        .event-info {
            font-size: 11px;
            margin-top: 5px;
            white-space: nowrap;
            overflow: visible; /* allow overflow to show outside */
            text-overflow: ellipsis;
            display: inline-block;
            max-width: none;
            position: absolute;
            top: 16px;
            left: 5px;
            z-index: 2;
            background-color: transparent;
            pointer-events: auto; /* optional: allows clicks to pass through */
        }
        .event-dot-container {
            margin-bottom: 2px;
            white-space: nowrap;
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

    </style>

    <div class="calendar-header">
        <button id="prevMonth" aria-label="Previous month">&lt;</button>
        <span id="calendarTitle" aria-live="polite"></span>
        <button id="nextMonth" aria-label="Next month">&gt;</button>
    </div>

    <table role="grid" aria-label="Calendar">
        <thead>
            <tr>
                <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th>
                <th>Thu</th><th>Fri</th><th>Sat</th>
            </tr>
        </thead>
        <tbody id="calendarBody"></tbody>
    </table>
    <button id="addEventBtn" data-bs-toggle="modal" data-bs-target="#addEventModal">Add Reminder</button>
    



    <script>
        const events = <?php echo json_encode($events); ?>;
        const colors = ["#e74c3c", "#3498db", "#2ecc71", "#9b59b6", "#f39c12", "#1abc9c"];
        let currentDate = new Date();

        function parseDate(str) {
            const [y, m, d] = str.split("-");
            return new Date(y, m - 1, d);
        }

        function formatDate(date) {
            return date.toISOString().split('T')[0];
        }

        function updateCalendar() {
            const month = currentDate.getMonth();
            const year = currentDate.getFullYear();
            const firstDay = new Date(year, month, 1).getDay();
            const totalDays = new Date(year, month + 1, 0).getDate();

            document.getElementById('calendarTitle').innerText =
                `${currentDate.toLocaleString('default', { month: 'long' })} ${year}`;

            const calendarBody = document.getElementById('calendarBody');
            calendarBody.innerHTML = "";

            const dateToEvents = {};

            events.forEach((event, index) => {
                const start = parseDate(event.datePosted);
                const end = parseDate(event.endDate);
                let d = new Date(start);
                while (d <= end) {
                    const key = formatDate(d);
                    if (!dateToEvents[key]) dateToEvents[key] = [];
                    dateToEvents[key].push({ ...event, color: colors[index % colors.length], startDate: formatDate(start) });
                    d.setDate(d.getDate() + 1);
                }
            });

            let row = document.createElement("tr");
            const today = new Date();

            // Collect all unique start dates of all events (sorted) - used for title logic
            const allStartDatesSet = new Set();
            Object.keys(dateToEvents).forEach(key => {
                dateToEvents[key].forEach(ev => allStartDatesSet.add(ev.startDate));
            });
            const allStartDates = [...allStartDatesSet].sort();

            function parseYMD(d) {
                const [y,m,day] = d.split("-").map(Number);
                return new Date(y,m-1,day);
            }

            for (let i = 0; i < firstDay; i++) {
                row.appendChild(document.createElement("td"));
            }

            for (let day = 1; day <= totalDays; day++) {
                const dateObj = new Date(year, month, day);
                const dateStr = formatDate(dateObj);

                const td = document.createElement("td");
                td.dataset.date = dateStr;
                td.innerHTML = `<div class="day-number">${day}</div>`;
                td.style.position = "relative";

                if (
                    today.getDate() === day &&
                    today.getMonth() === month &&
                    today.getFullYear() === year
                ) {
                    td.classList.add("today");
                }

                if (dateToEvents[dateStr]) {
                    const eventsToday = dateToEvents[dateStr];

                    // Append dots for all events today
                    eventsToday.forEach(ev => {
                        const dot = document.createElement("span");
                        dot.className = "event-dot";
                        dot.style.backgroundColor = ev.color;
                        td.appendChild(dot);
                    });

                    // Index of this cell's date in allStartDates array
                    const currentIndex = allStartDates.indexOf(dateStr);

                    // Events starting today
                    const eventsStartingToday = eventsToday.filter(ev => ev.startDate === dateStr);
                    const countStartingToday = eventsStartingToday.length;

                    const eventInfo = document.createElement("div");
                    eventInfo.className = "event-info";

                    // Show logic:
                    // If this is the earliest start date (lowest index), show only +count
                    // If this is the next start date, show full combined titles of all ongoing events on this date (unique)
                    // Else, show no title (just dots)
                    // Clear previous event-info span only (not date number or dots)
                    const existingEventInfo = td.querySelector(".event-info");
                    if (existingEventInfo) {
                        existingEventInfo.remove();
                    }

                    const uniqueTitlesSet = new Set(eventsToday.map(ev => ev.title));
                    const allTitles = [...uniqueTitlesSet];
                    const displayLimit = 3;
                    const maxTitleLength = 15;

                    if (allTitles.length > 0) {
                        eventInfo.classList.add("event-info");

                        if (currentIndex === 0 && allTitles.length > 1) {
                            // Multi-event start: just show count
                            eventInfo.innerText = `+${allTitles.length}`;
                            eventInfo.title = allTitles.join(", ");
                            td.appendChild(eventInfo);
                        } else if (currentIndex === 1 && allTitles.length > 1) {
                            // Multi-event middle date
                            const hasLongTitle = allTitles.some(title => title.length > maxTitleLength);
                            const fullText = `+${allTitles.length} ` + allTitles.join(", ");
                            eventInfo.title = fullText;

                            if (allTitles.length >= 3 && !hasLongTitle) {
                                const displayTitles = allTitles.slice(0, displayLimit).join(", ");
                                eventInfo.innerText = `+${allTitles.length} ${displayTitles}, ...`;
                            } else {
                                eventInfo.innerText = `+${allTitles.length}`;
                            }

                            td.appendChild(eventInfo);
                        } else if (allTitles.length === 1) {
                            // Isolated single event: show title directly ONLY if this date is the event's start date
                            if (dateStr === eventsToday[0].startDate) {
                                const title = allTitles[0];
                                eventInfo.innerText = title.length > maxTitleLength ? title.slice(0, maxTitleLength) + "..." : title;
                                eventInfo.title = title; // full title on hover
                                td.appendChild(eventInfo);
                            }
                            // Else: no eventInfo appended (only dots) on subsequent days
                        }
                    }
                    // else no eventInfo appended (only dots)

                    // Highlight background for event days
                    td.style.backgroundColor = eventsToday[0].color + '22';

                    // Modal on click
                    td.addEventListener("click", () => {
                        const modalBody = document.getElementById("eventDescription");
                        modalBody.innerHTML = ""; // Clear previous content

                        eventsToday.forEach(ev => {
                            // Create a card div for each event
                            const card = document.createElement("div");
                            card.style.borderLeft = `6px solid ${ev.color}`;
                            card.style.backgroundColor = `${ev.color}22`; // light transparent bg
                            card.style.padding = "10px";
                            card.style.marginBottom = "10px";
                            card.style.borderRadius = "4px";
                            card.style.boxShadow = "0 1px 3px rgba(0,0,0,0.1)";

                            // Title
                            const title = document.createElement("h6");
                            title.innerText = ev.title;
                            title.style.margin = "0 0 5px 0";

                            // Description
                            const desc = document.createElement("p");
                            desc.innerText = ev.description;
                            desc.style.margin = "0 0 8px 0";
                            desc.style.whiteSpace = "pre-wrap";

                            // Dates posted and end date
                            const dates = document.createElement("small");
                            dates.innerText = `Posted: ${ev.datePosted} | Ends: ${ev.endDate}`;
                            dates.style.color = "#555";

                            card.appendChild(title);
                            card.appendChild(desc);
                            card.appendChild(dates);

                            modalBody.appendChild(card);
                        });

                        const modal = new bootstrap.Modal(document.getElementById('eventModal'));
                        modal.show();
                    });
                }

                row.appendChild(td);

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

<!-- Modal for Event Details -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Event Details</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close"> 
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <pre id="eventDescription" style="white-space: pre-wrap;"></pre>
            </div>
            <div class="modal-footer" style="display: flex; justify-content: center;">
                <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

