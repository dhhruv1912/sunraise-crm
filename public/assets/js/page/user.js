// public/assets/js/page/user.js Not Used
document.addEventListener("DOMContentLoaded", () => {
    // Elements
    const roleColors = {
        1: "badge bg-danger",       // Admin
        2: "badge bg-primary",      // Developer
        3: "badge bg-info",         // CMO
        4: "badge bg-warning text-dark",  // Marketing Head
        5: "badge bg-warning",      // Marketing Executive
        6: "badge bg-success",      // Project Head
        7: "badge bg-secondary",    // Project Supervisor
        8: "badge bg-dark",         // Lisoner
        9: "badge bg-purple",       // Site Engineer (custom class if needed)
    };



    const modalEl = document.getElementById("addEmployeeModal");
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const loader = modalEl?.querySelector(".loader-line");

    const saveBtn = document.getElementById("employee-save");
    const updateBtn = document.getElementById("employee-update");
    const closeBtn = document.getElementById("close-emp-modal");

    const form = document.getElementById("employeeForm");
    const newOnlyWrapper = document.getElementById("new-only-fields");

    // List / pagination selectors
    const tbodySel = "#user-body";
    const paginationContainer = "#user-pagination";
    const paginationUL = "#pagination-ul";

    // CSRF
    const TOKEN = document.querySelector('meta[name="csrf_token"]')?.getAttribute("content") ?? "";

    // Helpers
    const getVal = id => document.getElementById(id)?.value ?? "";
    const setVal = (id, value) => { const el = document.getElementById(id); if (el) el.value = value; };
    const setChecked = (id, val) => { const el = document.getElementById(id); if (el) el.checked = !!val; };
    const toggleNewOnly = (show) => {
        if (!newOnlyWrapper) return;
        // store intention on wrapper
        newOnlyWrapper.dataset.new = show ? '1' : '0';
        newOnlyWrapper.style.display = show ? '' : 'none';
    };

    // Loader
    const showLoader = () => loader?.classList.remove("d-none");
    const hideLoader = () => loader?.classList.add("d-none");

    // Reset
    function resetForm() {
        if (form) form.reset();
        setVal("empID", "");
        document.querySelectorAll("[class^='invalid-feedback']").forEach(el => el.innerHTML = "");
    }

    // Validation errors (Laravel style)
    function validateErrors(errors = {}) {
        Object.entries(errors).forEach(([k, v]) => {
            const msg = Array.isArray(v) ? v.join("<br>") : v;
            const el = document.querySelector(`.invalid-feedback-${k}`);
            if (el) el.innerHTML = msg;
        });
    }

    // Small UI fallback helpers (use existing project helpers if present)
    function showAlert(msg, cls = "alert-danger") {
        if (typeof window.showAlert === "function") return window.showAlert(msg, cls);
        alert(msg);
    }
    function showDismissible(msg, cls = "alert-success") {
        if (typeof window.showDismissible === "function") return window.showDismissible(msg, cls);
        console.log(msg);
    }

    // --- Open Add modal (unified modal) ---
    document.getElementById("add-employee")?.addEventListener("click", () => {
        resetForm();
        document.getElementById("addEmployeeModalLabel").innerText = "Add Employee";

        // Show Save, hide Update
        saveBtn.classList.remove("d-none");
        updateBtn.classList.add("d-none");

        // Show salary/password for new
        toggleNewOnly(true);

        modal?.show();
    });

    // --- Close button (modal) ---
    closeBtn?.addEventListener("click", () => {
        modal?.hide();
    });

    // --- Delete ---
    document.querySelector(tbodySel)?.addEventListener("click", async (e) => {
        const btn = e.target.closest(".delete-employee");
        if (!btn) return;
        const id = btn.dataset.id;
        if (!id) return;
        if (!confirm("Are you sure?")) return;

        try {
            const res = await fetch(`/user/${id}`, {
                method: "DELETE",
                headers: { "X-CSRF-TOKEN": TOKEN }
            });

            if (res.status === 200) {
                showDismissible("Employee deleted");
                loadStaff();
            } else {
                const json = await res.json();
                showAlert(json.message || "Delete failed");
            }
        } catch (err) {
            showAlert("Delete failed");
        }
    });
    function getRoleName(id) {
        return window.roleNames[parseInt(id)] ?? "Unknown";
    }

    function getRoleBadge(id) {
        return roleColors[parseInt(id)] ?? "badge bg-secondary";
    }

    // --- Render table ---
    function renderStaffTable(list = []) {
        console.log(list);
        
        const tbody = document.querySelector(tbodySel);
        if (!tbody) return;

        if (!Array.isArray(list.data) || list.data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4">No data found</td></tr>`;
            return;
        }

        tbody.innerHTML = list.data.map(u => `
            <tr>
                <td>${u.fname} ${u.lname}</td>
                <td>
                    <span class="${getRoleBadge(u.role)}">
                        ${getRoleName(u.role)}
                    </span>
                </td>
                <td><span class="badge ${u.status ? 'bg-success' : 'bg-danger'}">${u.status ? 'Active' : 'Inactive'}</span></td>
                <td>${u.mobile ?? '-'}</td>
                <td>${u.email ?? '-'}</td>
                <td>
                    <a class="btn btn-sm btn-primary edit-employee" href="/user/${u.id}/edit">Edit</a>
                    <button class="btn btn-sm btn-danger delete-employee" data-id="${u.id}">Delete</button>
                </td>
            </tr>
            `).join('');
        }

    // --- Pagination renderer & click handler ---
    function renderStaffPagination(p = {}) {
        const ul = document.getElementById("pagination-ul");
        if (!ul) return;

        const current = p.current_page || 1;
        const last = p.last_page || 1;

        if (last <= 1) {
            ul.innerHTML = '';
            return;
        }

        let html = `<li class="page-item ${!p.prev_page_url ? 'disabled' : ''}">
      <a href="#" class="page-link" data-page="${Math.max(1, current - 1)}">Prev</a></li>`;

        for (let i = 1; i <= last; i++) {
            html += `<li class="page-item ${i === current ? 'active' : ''}">
        <a href="#" class="page-link" data-page="${i}">${i}</a></li>`;
        }

        html += `<li class="page-item ${!p.next_page_url ? 'disabled' : ''}">
      <a href="#" class="page-link" data-page="${Math.min(last, current + 1)}">Next</a></li>`;

        ul.innerHTML = html;
    }

    document.querySelector(paginationContainer)?.addEventListener("click", (e) => {
        const a = e.target.closest("a[data-page]");
        if (!a) return;
        e.preventDefault();
        const page = Number(a.dataset.page) || 1;
        loadStaff(page);
    });

    // --- Load staff list ---
    async function loadStaff(page = 1) {
        const tbody = document.querySelector(tbodySel);
        if (tbody) tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4">Loading...</td></tr>`;

        try {
            const res = await fetch(`/user/list?page=${page}`);
            const data = await res.json();

            renderStaffTable(data.data || []);
            renderStaffPagination(data);
        } catch (err) {
            if (tbody) tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Failed to load</td></tr>`;
            console.error(err);
        }
    }

    // initial
    loadStaff();
});
