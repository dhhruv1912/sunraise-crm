document.addEventListener("DOMContentLoaded", () => {

    const modal = new bootstrap.Modal(document.getElementById("addEmployeeModal"));
    const loader = document.querySelector("#addEmployeeModal .loader-line");

    const saveBtn = document.getElementById("employee-save");
    const updateBtn = document.getElementById("employee-update");
    const closeBtn = document.getElementById("close-emp-modal");

    const form = document.getElementById("employeeForm");

    // Clear Form Function
    function resetForm() {
        form.reset();
        document.getElementById("empID").value = "";
        document.querySelectorAll(".invalid-feedback").forEach(el => el.innerHTML = "");
    }

    // Loader toggle
    const showLoader = () => loader.classList.remove("d-none");
    const hideLoader = () => loader.classList.add("d-none");

    // ================================
    // OPEN ADD EMPLOYEE MODAL
    // ================================
    document.getElementById("add-employee")?.addEventListener("click", () => {

        resetForm();

        // Change modal title
        document.getElementById("addEmployeeModalLabel").innerHTML = "Add Employee";

        // Show correct buttons
        saveBtn.classList.remove("d-none");
        closeBtn?.classList.remove("d-none");
        updateBtn.classList.add("d-none");

        modal.show();
    });

    // ================================
    // OPEN EDIT EMPLOYEE MODAL
    // ================================
    document.querySelector("#staff-body")?.addEventListener("click", async (e) => {
        const editBtn = e.target.closest(".edit-employee");
        if (!editBtn) return;

        const id = editBtn.getAttribute("data-id");
        if (!id) return;

        resetForm();
        showLoader();

        document.getElementById("addEmployeeModalLabel").innerHTML = "Edit Employee";

        saveBtn.classList.add("d-none");
        closeBtn?.classList.add("d-none");
        updateBtn.classList.remove("d-none");

        // Set update button ID
        updateBtn.dataset.id = id;

        modal.show();

        // Fetch employee data
        try {
            let response = await fetch(`/api/user/${id}`);
            let data = await response.json();

            hideLoader();

            // Fill the form
            document.getElementById("firstname").value = data.fname ?? "";
            document.getElementById("lastname").value = data.lname ?? "";
            document.getElementById("mobile").value = data.mobile ?? "";
            document.getElementById("email").value = data.email ?? "";

            document.getElementById("role").value = data.role_key ?? "";

            document.getElementById("status").checked = data.status == 1;

        } catch (err) {
            hideLoader();
            showAlert("Unable to load employee data", "alert-danger");
        }
    });

    // ================================
    // SAVE EMPLOYEE
    // ================================
    saveBtn?.addEventListener("click", async () => {

        showLoader();

        let payload = {
            firstname: firstname.value,
            lastname: lastname.value,
            mobile: mobile.value,
            email: email.value,
            password: password?.value,
            salary: salary?.value,
            role: role.value,
            status: status.checked ? 1 : 0
        };

        try {
            let response = await fetch("/api/user", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": TOKEN
                },
                body: JSON.stringify(payload)
            });

            let r = await response.json();
            hideLoader();

            if (response.status === 201) {
                showDismissible("Employee added successfully!", "alert-success");
                modal.hide();
                loadStaff(); // reload table
            } else {
                validateErrors(r);
            }

        } catch (err) {
            hideLoader();
            showAlert("Something went wrong!", "alert-danger");
        }
    });

    // ================================
    // UPDATE EMPLOYEE
    // ================================
    updateBtn?.addEventListener("click", async () => {

        let id = updateBtn.dataset.id;
        showLoader();

        let payload = {
            firstname: firstname.value,
            lastname: lastname.value,
            mobile: mobile.value,
            email: email.value,
            role: role.value,
            status: status.checked ? 1 : 0
        };

        try {
            let response = await fetch(`/api/user/${id}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": TOKEN
                },
                body: JSON.stringify(payload)
            });

            let r = await response.json();
            hideLoader();

            if (response.status === 200) {
                showDismissible("Employee updated successfully!", "alert-success");
                modal.hide();
                loadStaff();
            } else {
                validateErrors(r);
            }

        } catch (err) {
            hideLoader();
            showAlert("Unable to update employee!", "alert-danger");
        }
    });

    // ================================
    // VALIDATION ERROR HANDLER
    // ================================
    function validateErrors(errors) {
        Object.entries(errors).forEach(([key, val]) => {
            let el = document.querySelector(`.invalid-feedback-${key}`);
            if (el) el.innerHTML = val;
        });
    }

});
