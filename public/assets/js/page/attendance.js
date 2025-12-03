document.addEventListener("DOMContentLoaded", function () {

    // -------------------------------
    // Set date max = today
    // -------------------------------
    const today = new Date().toISOString().split("T")[0];
    document.querySelector("#start-date").setAttribute("max", today);
    document.querySelector("#end-date").setAttribute("max", today);

    load_attendance(1, true);
});

// -------------------------------
// Date Change Listeners
// -------------------------------
document.querySelector("#start-date").addEventListener("change", function () {
    const startDate = this.value;
    document.querySelector("#end-date").setAttribute("min", startDate);
    load_attendance(1, true);
});

document.querySelector("#end-date").addEventListener("change", function () {
    load_attendance(1, true);
});

// -------------------------------
// Change value handler
// -------------------------------
function changeValue(value, field) {
    document.querySelector(field).value = value;
    document.querySelector('#navbarDropdown').innerHTML = document.querySelector(`.user-list#id${value}`)?.innerText || "";
    // const userName = document.querySelector(`.user-list#id${value}`)?.innerText || "";
    load_attendance(1, true);
}

// -------------------------------
// Load Attendance List
// -------------------------------
async function load_attendance(page, rewrite = false) {
    const user = document.querySelector("#staff").value;
    const startdate = document.querySelector("#start-date").value;
    const enddate = document.querySelector("#end-date").value;

    const url = new URL("/user/attendance/load",window.location.origin);

    url.searchParams.append("perPage", 10);
    url.searchParams.append("page", page);
    if (user) url.searchParams.append("user", user);
    if (startdate) url.searchParams.append("startdate", startdate);
    if (enddate) url.searchParams.append("enddate", enddate);

    const response = await fetch(url);
    const data = await response.json();

    const tbody = document.querySelector("#attandance-log-datatable tbody");

    if (rewrite) tbody.innerHTML = "";

    if (data.status) {
        data.data.forEach(row => {

            const d = new Date(row.created_at);
            const DD = String(d.getDate()).padStart(2, "0");
            const MM = String(d.getMonth() + 1).padStart(2, "0");
            const YYYY = d.getFullYear();
            const hh = String(d.getHours()).padStart(2, "0");
            const mm = String(d.getMinutes()).padStart(2, "0");
            const ss = String(d.getSeconds()).padStart(2, "0");

            const formattedDate = `${DD}-${MM}-${YYYY} ${hh}:${mm}:${ss}`;

            const userName = document.querySelector(`.user-list#id${row.staffId}`)?.innerText || "";

            tbody.insertAdjacentHTML("beforeend", `
                <tr>
                    <td>
                        <i class="mdi mdi-wallet-travel mdi-20px text-danger me-3"></i>
                        <span class="fw-medium">${userName}</span>
                    </td>
                    <td>${formattedDate}</td>
                    <td>${row.message}</td>
                    <td>${row.lendmark}</td>
                    <td>${row.device}</td>
                    <td>
                        <a href="https://maps.google.com/?q=${row.location}"
                           class="edit-employee btn btn-outline-primary waves-effect px-2">
                            <span class="mdi mdi-map-marker-radius"></span>
                        </a>
                    </td>
                </tr>
            `);
        });
    }

    container.dataset.nextPage = data.nextPage;
}

// -------------------------------
// Infinite Scroll
// -------------------------------
let container;

document.addEventListener("DOMContentLoaded", function () {

    container = document.querySelector("#Attandance-wrapper");

    window.addEventListener("scroll", function () {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight) {

            const nextPage = container.dataset.nextPage;

            if (nextPage !== undefined && nextPage !== "null") {
                load_attendance(parseInt(nextPage));
                container.dataset.nextPage = parseInt(nextPage) + 1;
            }
        }
    });

});


// -------------------------------
// Generate Attendance Report
// -------------------------------
function GenerateAttandenceReport() {
    const user = document.querySelector("#AttendanceStaff").value;
    const month = document.querySelector("#month").value;

    let url = "/user/attendance/report";

    if (user && month) {
        url = `/user/attendance/${user}/report?month=${month}`;
    }

    window.location.href = url;
}
