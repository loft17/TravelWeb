document.addEventListener("DOMContentLoaded", function () {
    const calendarBody = document.getElementById("calendar-body");

    // Configuración del calendario
    const days = [
        { month: "octubre", totalDays: 31, startDay: 4, year: 2025 }, // 31 de octubre (viernes)
        { month: "noviembre", totalDays: 30, year: 2025 } // Todo noviembre
    ];

    let html = "";
    let currentMonthIndex = 0;
    let day = 31; // Empezamos en 31 de octubre

    for (let row = 0; row < 5; row++) {
        html += "<tr>";

        for (let col = 0; col < 7; col++) {
            if (row === 0 && col < days[currentMonthIndex].startDay) {
                html += `<td class="empty"></td>`; // Espacios vacíos antes del 31 de octubre
            } else {
                // Formato de fecha: yyyy-mm-dd
                const formattedDate = `${days[currentMonthIndex].year}-${(currentMonthIndex === 0 ? '10' : '11')}-${String(day).padStart(2, '0')}`;

                html += `<td><a href="planning/index.php?fecha=${formattedDate}">${day}</a></td>`;
                day++;

                if (day > days[currentMonthIndex].totalDays) {
                    currentMonthIndex++; // Pasamos a noviembre
                    day = 1;
                }
            }
        }

        html += "</tr>";
    }

    calendarBody.innerHTML = html;
});
