<div id="calendarContainer">
    <style>
        /* Calendar styling */
        #calendarContainer {
            width: 100%;
            padding: 20px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .calendar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .calendar-header button {
            padding: 5px 10px;
        }

        #calendarTitle {
            flex-grow: 1;
            text-align: center;
            font-weight: bold;
            min-width: 160px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            width: 14.28%;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        td {
            cursor: pointer;
        }

        td:hover {
            background-color: #f0f0f0;
        }

        .today {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
            border-radius: 50%;
        }

        #addEventBtn {
            margin-top: 15px;
            padding: 8px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        #addEventBtn:hover {
            background-color: #218838;
        }
    </style>

    <script>
        function renderCalendar(containerId) {
            let currentDate = new Date();

            function updateCalendar() {
                const month = currentDate.getMonth();
                const year = currentDate.getFullYear();

                const today = new Date();
                const isCurrentMonth = today.getMonth() === month && today.getFullYear() === year;
                const todayDate = today.getDate();

                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                const titleElement = document.getElementById('calendarTitle');
                const calendarBody = document.getElementById('calendarBody');

                titleElement.innerText = `${monthNames[month]} ${year}`;

                const firstDay = new Date(year, month, 1).getDay();
                const totalDays = new Date(year, month + 1, 0).getDate();

                const daysArray = [];
                for (let i = 0; i < firstDay; i++) {
                    daysArray.push("");
                }
                for (let i = 1; i <= totalDays; i++) {
                    daysArray.push(i);
                }

                calendarBody.innerHTML = "";

                let row = document.createElement("tr");
                for (let i = 0; i < daysArray.length; i++) {
                    const cell = document.createElement("td");

                    if (daysArray[i] === "") {
                        cell.innerText = "";
                    } else {
                        cell.innerText = daysArray[i];
                        if (isCurrentMonth && daysArray[i] === todayDate) {
                            cell.classList.add("today");
                        }
                    }

                    row.appendChild(cell);

                    if ((i + 1) % 7 === 0) {
                        calendarBody.appendChild(row);
                        row = document.createElement("tr");
                    }
                }
                if (daysArray.length % 7 !== 0) {
                    calendarBody.appendChild(row);
                }
            }

            const container = document.getElementById(containerId);

            const header = document.createElement('div');
            header.className = 'calendar-header';

            const prevButton = document.createElement('button');
            prevButton.innerText = 'Previous';
            prevButton.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() - 1);
                updateCalendar();
            });

            const nextButton = document.createElement('button');
            nextButton.innerText = 'Next';
            nextButton.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() + 1);
                updateCalendar();
            });

            const titleElement = document.createElement('span');
            titleElement.id = 'calendarTitle';

            header.appendChild(prevButton);
            header.appendChild(titleElement);
            header.appendChild(nextButton);

            container.appendChild(header);

            const table = document.createElement('table');
            table.innerHTML = `
                <thead>
                    <tr>
                        <th>Sun</th>
                        <th>Mon</th>
                        <th>Tue</th>
                        <th>Wed</th>
                        <th>Thu</th>
                        <th>Fri</th>
                        <th>Sat</th>
                    </tr>
                </thead>
                <tbody id="calendarBody"></tbody>
            `;
            container.appendChild(table);

            //ADD BUTTON
            const addEventBtn = document.createElement('button');
            addEventBtn.id = 'addEventBtn';
            addEventBtn.innerText = 'Add Reminder';

            // Bootstrap modal trigger attributes
            addEventBtn.setAttribute('data-bs-toggle', 'modal');
            addEventBtn.setAttribute('data-bs-target', '#addEventModal'); // must match your modal's ID

            container.appendChild(addEventBtn);

            updateCalendar();
        }

        document.addEventListener('DOMContentLoaded', () => {
            renderCalendar('calendarContainer');
        });
    </script>
</div>

<div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEventReminder">Add Reminder</h5>
                        <button class="close" type="button" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">

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
