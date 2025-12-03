{{-- resources/views/page/common/add-employee.blade.php --}}
<div class="modal fade" id="addEmployeeModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            {{-- Header --}}
            <div class="modal-header d-block">
                <h4 class="modal-title" id="addEmployeeModalLabel">Add Employee</h4>
                <div class="loader-line mt-2 d-none" id="employee-loader"></div>
            </div>

            {{-- Body --}}
            <div class="modal-body">
                {{-- inline alerts (reuse your partial if exists) --}}
                @includeIf('temp.alert-inline')

                <form id="employeeForm" novalidate>
                    <input type="hidden" id="empID" name="id" value="">

                    <div class="row">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="firstname" name="firstname" class="form-control"
                                    placeholder="First name">
                                <label for="firstname">First Name</label>
                                <div class="invalid-feedback invalid-feedback-firstname"></div>
                            </div>
                        </div>

                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="lastname" name="lastname" class="form-control"
                                    placeholder="Last name">
                                <label for="lastname">Last Name</label>
                                <div class="invalid-feedback invalid-feedback-lastname"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="mobile" name="mobile" class="form-control"
                                    placeholder="Mobile">
                                <label for="mobile">Mobile No.</label>
                                <div class="invalid-feedback invalid-feedback-mobile"></div>
                            </div>
                        </div>

                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                <input type="email" id="email" name="email" class="form-control"
                                    placeholder="Email">
                                <label for="email">Email Address</label>
                                <div class="invalid-feedback invalid-feedback-email"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Salary + Password (we hide for edit via JS) --}}
                    <div id="new-only-fields" data-new="1">
                        <div class="row">
                            <div class="col mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" id="salary" name="salary" class="form-control"
                                        placeholder="Salary">
                                    <label for="salary">Salary</label>
                                    <div class="invalid-feedback invalid-feedback-salary"></div>
                                </div>
                            </div>

                            <div class="col mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="password" id="password" name="password" class="form-control"
                                        placeholder="Password">
                                    <label for="password">Password</label>
                                    <div class="invalid-feedback invalid-feedback-password"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col mb-3">
                            <div class="form-floating form-floating-outline">
                                @php
                                    $staff_role =
                                        json_decode(\App\Models\Settings::getValue('user_roles'), true) ?? [];
                                @endphp
                                <select id="role" name="role" class="form-select">
                                    <option value="">Select Role</option>
                                    @foreach ($staff_role as $rk => $rv)
                                        <option value="{{ $rk + 1 }}">{{ $rv }}</option>
                                    @endforeach
                                </select>
                                <label for="role">Role</label>
                                <div class="invalid-feedback invalid-feedback-role"></div>
                            </div>
                        </div>

                        <div class="col mb-3 d-flex align-items-center">
                            <div class="form-check form-switch ms-3">
                                <input class="form-check-input" type="checkbox" id="status" name="status" checked>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            {{-- Footer (buttons always present; JS will show/hide Save vs Update) --}}
            <div class="modal-footer">
                <button type="button" id="close-emp-modal" class="btn btn-outline-secondary">Close</button>
                <button type="button" id="employee-save" class="btn btn-primary">Save</button>
                <button type="button" id="employee-update" class="btn btn-primary d-none">Update</button>
            </div>

        </div>
    </div>
</div>
<script>

    const roleNames = @json($staff_role);
</script>
