// public/assets/js/page/user.js
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

    // --- Open Edit modal (populate) ---
    document.querySelector(tbodySel)?.addEventListener("click", async (e) => {
        const btn = e.target.closest(".edit-employee");
        if (!btn) return;

        const id = btn.dataset.id;
        if (!id) return;

        resetForm();
        showLoader();

        document.getElementById("addEmployeeModalLabel").innerText = "Edit Employee";

        // hide save, show update
        saveBtn.classList.add("d-none");
        updateBtn.classList.remove("d-none");
        updateBtn.dataset.id = id;

        // Hide salary/password on edit
        toggleNewOnly(false);

        modal?.show();

        try {
            const res = await fetch(`/users/${id}`);
            if (!res.ok) throw new Error("Fetch failed");
            const data = await res.json();
            hideLoader();

            setVal("empID", data.id ?? "");
            setVal("firstname", data.fname ?? "");
            setVal("lastname", data.lname ?? "");
            setVal("mobile", data.mobile ?? "");
            setVal("email", data.email ?? "");
            setVal("role", data.role ?? "");
            setChecked("status", data.status == 1);
        } catch (err) {
            hideLoader();
            showAlert("Unable to load employee data");
        }
    });

    // --- Save (Create) ---
    // saveBtn?.addEventListener("click", async () => {
    //     showLoader();
    //     const payload = {
    //         firstname: getVal("firstname"),
    //         lastname: getVal("lastname"),
    //         mobile: getVal("mobile"),
    //         email: getVal("email"),
    //         password: getVal("password"),
    //         salary: getVal("salary"),
    //         role: getVal("role"),
    //         status: document.getElementById("status")?.checked ? 1 : 0
    //     };

    //     try {
    //         const res = await fetch("/users", {
    //             method: "POST",
    //             headers: {
    //                 "Content-Type": "application/json",
    //                 "X-CSRF-TOKEN": TOKEN
    //             },
    //             body: JSON.stringify(payload)
    //         });

    //         const json = await res.json();
    //         hideLoader();

    //         if (res.status === 201) {
    //             showDismissible("Employee added", "alert-success");
    //             modal?.hide();
    //             loadStaff();
    //         } else if (res.status === 422) {
    //             validateErrors(json);
    //         } else {
    //             showAlert(json.message || "Save failed");
    //         }
    //     } catch (err) {
    //         hideLoader();
    //         showAlert("Something went wrong");
    //     }
    // });
    saveBtn?.addEventListener("click", async () => {
        showLoader();

        const payload = {
            firstname: getVal("firstname").trim(),
            lastname: getVal("lastname").trim(),
            mobile: getVal("mobile").trim(),
            email: getVal("email").trim(),
            password: getVal("password"),
            salary: Number(getVal("salary")) || null,
            role: Number(getVal("role")) || null,
            status: document.getElementById("status")?.checked ? 1 : 0
        };

        try {
            const res = await fetch("/users", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": TOKEN,
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: JSON.stringify(payload)
            });

            const json = await res.json();
            hideLoader();

            if (res.status === 201) {
                showDismissible("Employee added successfully!", "alert-success");
                modal?.hide();
                loadStaff();
            }
            else if (res.status === 422) {
                validateErrors(json.errors);
            }
            else {
                showAlert(json.message || "Save failed", "alert-danger");
            }

        } catch (err) {
            hideLoader();
            showAlert("Something went wrong", "alert-danger");
        }
    });


    // --- Update ---
    updateBtn?.addEventListener("click", async () => {
        const id = updateBtn.dataset.id;
        if (!id) return;
        showLoader();

        const payload = {
            firstname: getVal("firstname"),
            lastname: getVal("lastname"),
            mobile: getVal("mobile"),
            email: getVal("email"),
            role: getVal("role"),
            status: document.getElementById("status")?.checked ? 1 : 0
        };

        try {
            const res = await fetch(`/users/${id}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": TOKEN
                },
                body: JSON.stringify(payload)
            });

            const json = await res.json();
            hideLoader();

            if (res.status === 200) {
                showDismissible("Employee updated", "alert-success");
                modal?.hide();
                loadStaff();
            } else if (res.status === 422) {
                validateErrors(json);
            } else {
                showAlert(json.message || "Update failed");
            }
        } catch (err) {
            hideLoader();
            showAlert("Unable to update");
        }
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
            const res = await fetch(`/users/${id}`, {
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
    // const roleNames = {
    //     1: "Admin",
    //     2: "Devloper",
    //     3: "CMO",
    //     4: "Marketing Head",
    //     5: "Marketing Exicutive",
    //     6: "Project Head",
    //     7: "Project Superviser",
    //     8: "Lisoner",
    //     9: "Site Engineer",
    // };
    function getRoleName(id) {
        return roleNames[parseInt(id) - 1] ?? "Unknown";
    }

    function getRoleBadge(id) {
        return roleColors[parseInt(id) - 1] ?? "badge bg-secondary";
    }

    // --- Render table ---
    function renderStaffTable(list = []) {
        const tbody = document.querySelector(tbodySel);
        if (!tbody) return;

        if (!Array.isArray(list) || list.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4">No data found</td></tr>`;
            return;
        }

        tbody.innerHTML = list.map(u => `
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
          <button class="btn btn-sm btn-primary edit-employee" data-id="${u.id}">Edit</button>
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
            const res = await fetch(`/users?page=${page}`);
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
