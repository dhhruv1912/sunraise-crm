@extends('temp.common')

@section('title', 'Attendance')

@section('content')
    <div class="container-fluid">
        <div class="crm-page">

            {{-- ================= HEADER ================= --}}
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fa-solid fa-calendar-check me-2"></i>
                        Attendance
                    </h4>
                    <div class="text-muted small">
                        Daily staff attendance logs
                    </div>
                </div>

                <input type="date" id="filterDate" class="form-control" style="max-width:180px"
                    value="{{ now()->toDateString() }}">

            </div>
            {{-- ================= WIDGETS ================= --}}
            <div class="row g-3 mt-2 position-relative" id="attendanceWidgets" style="min-height: 100px">
                <div class="crm-loader-overlay">
                    <div class="crm-spinner"></div>
                </div>
            </div>

            <form method="GET" id="attendanceReportForm" class="d-flex gap-2 align-items-center">

                <div class="d-flex align-items-center gap-2">
                    <label class="small text-muted mb-0">Rows</label>
                    <select id="attendancePerPage"
                            class="form-select"
                            style="width:80px"
                            onchange="changeAttendancePerPage()">
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>

                {{-- User Dropdown --}}
                <select name="user" id="reportUser" class="form-select" required style="min-width:200px">
                    <option value="">Select User</option>
                    @foreach (\App\Models\User::orderBy('fname')->get() as $u)
                        <option value="{{ $u->id }}">
                            {{ $u->fname }} {{ $u->lname }}
                        </option>
                    @endforeach
                </select>

                {{-- Month Picker --}}
                <input type="month" name="month" class="form-control" required>

                {{-- Export --}}
                <button type="submit" class="btn btn-success col-2" id="exportBtn">
                    <i class="fa-solid fa-file-excel me-1"></i>
                    Export
                </button>
                <button type="submit" formaction="" formmethod="GET" id="salarySlipBtn"
                    class="btn btn-outline-primary col-2">
                    <i class="fa-solid fa-file-pdf me-1"></i>
                    Salary Slip
                </button>
            </form>
            {{-- ================= TABLE ================= --}}
            <div class="crm-section mt-3">
                <div class="crm-table-wrapper position-relative">

                    <table class="table crm-table mb-0">
                        <thead>
                            <tr>
                                <th>Staff</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Duration</th>
                                <th>Device</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTable">
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td colspan="6">
                                        <div class="crm-skeleton"></div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>

                    <div id="attendanceLoader" class="crm-loader-overlay d-none">
                        <div class="crm-spinner"></div>
                    </div>

                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted small" id="attendancePaginationInfo"></div>

                    <div class="btn-group" id="attendancePaginationBtns"></div>
                </div>
            </div>

        </div>
    </div>
@endsection


@push('scripts')
    <script>
        const LIST_URL = "{{ route('attendance.ajax.list') }}";
        const WIDGET_URL = "{{ route('attendance.ajax.widgets') }}";

        document.addEventListener('DOMContentLoaded', () => {
            loadWidgets();
            loadAttendance();

            document.getElementById('filterDate').addEventListener('change', () => {
                loadWidgets();
                loadAttendance();
            });
        });

        /* ================= WIDGETS ================= */
        function loadWidgets() {
            crmFetch(WIDGET_URL)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('attendanceWidgets').innerHTML = html;
                });
        }
        function changeAttendancePerPage() {
            attendancePerPage =
                document.getElementById('attendancePerPage').value;

            attendancePage = 1; // reset to first page
            loadAttendance(1);
        }
        /* ================= LIST ================= */
        let attendancePage = 1;
        let attendancePerPage = 10;

        function loadAttendance(page = 1) {
            attendancePage = page;

            const date   = document.getElementById('filterDate').value;
            const loader = document.getElementById('attendanceLoader');
            loader.classList.remove('d-none');

            const params = new URLSearchParams({
                date,
                page: attendancePage,
                per_page: attendancePerPage
            });

            crmFetch(`${LIST_URL}?${params}`)
                .then(res => res.json())
                .then(res => {
                    renderRows(res.data);
                    renderAttendancePagination(res.pagination);
                })
                .finally(() => loader.classList.add('d-none'));
        }

        function renderRows(rows) {
            const tbody = document.getElementById('attendanceTable');
            tbody.innerHTML = '';

            if (!rows.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No attendance records found
                        </td>
                    </tr>`;
                return;
            }

            rows.forEach(r => {
                tbody.innerHTML += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="crm-avatar">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Staff #${r.id}</div>
                                <div class="text-muted small">—</div>
                            </div>
                        </div>
                    </td>
                    <td>${new Date(r.created_at).toLocaleTimeString()}</td>
                    <td>${new Date(r.updated_at).toLocaleTimeString()}</td>
                    <td>
                        <span class="badge bg-info-subtle text-info">
                            —
                        </span>
                    </td>
                    <td>${r.device ?? '-'}</td>
                    <td>${r.ip ?? '-'}</td>
                </tr>`;
            });
        }

        function renderAttendancePagination(meta) {
            const info = document.getElementById('attendancePaginationInfo');
            const btns = document.getElementById('attendancePaginationBtns');

            info.innerText =
                `Page ${meta.current_page} of ${meta.last_page} • ${meta.total} records`;

            btns.innerHTML = '';

            if (meta.current_page > 1) {
                btns.innerHTML += `
                    <button class="btn btn-sm btn-light"
                            onclick="loadAttendance(${meta.current_page - 1})">
                        ‹ Prev
                    </button>`;
            }

            if (meta.current_page < meta.last_page) {
                btns.innerHTML += `
                    <button class="btn btn-sm btn-light"
                            onclick="loadAttendance(${meta.current_page + 1})">
                        Next ›
                    </button>`;
            }
        }



        document.getElementById('attendanceReportForm').addEventListener('submit', function(e) {
            const userId = document.getElementById('reportUser').value;
            const btn = document.getElementById('exportBtn');

            if (!userId) {
                e.preventDefault();
                showToast('warning', 'Please select a user');
                return;
            }

            this.action = `/attendance/report/${userId}`;

            // button loader
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Generating';
            btn.disabled = true;
        });
        document.getElementById('salarySlipBtn').addEventListener('click', function(e) {
            const userId = document.getElementById('reportUser').value;

            if (!userId) {
                e.preventDefault();
                showToast('warning', 'Select a user before generating salary slip');
                return;
            }

            this.formAction = `/attendance/salary-slip/${userId}`;
        });
    </script>
@endpush
