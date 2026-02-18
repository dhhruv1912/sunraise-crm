@php
    use App\Models\Settings;
    $staff_role = json_decode(Settings::getValue('staff_roles'));
@endphp

<div id="employeeFormWrapper">

    <form id="employeeForm">

        <input type="hidden" name="id" id="empID" value="{{ !$is_new ? session('staff.id') : '' }}">

        <div class="row">

            {{-- First Name --}}
            <div class="col mb-4 mt-2">
                <div class="form-floating form-floating-outline">
                    <input type="text"
                           id="firstname"
                           name="firstname"
                           class="form-control required"
                           placeholder="Enter employee first name"
                           value="{{ !$is_new ? session('staff.fname') : '' }}">
                    <label for="firstname">First Name</label>
                    <div class="invalid-feedback text-danger invalid-feedback-firstname"></div>
                </div>
            </div>

            {{-- Last Name --}}
            <div class="col mb-4 mt-2">
                <div class="form-floating form-floating-outline">
                    <input type="text"
                           id="lastname"
                           name="lastname"
                           class="form-control required"
                           placeholder="Enter employee last name"
                           value="{{ !$is_new ? session('staff.lname') : '' }}">
                    <label for="lastname">Last Name</label>
                    <div class="invalid-feedback text-danger invalid-feedback-lastname"></div>
                </div>
            </div>

        </div>

        <div class="row">

            {{-- Mobile --}}
            <div class="col mb-4 mt-2">
                <div class="form-floating form-floating-outline">
                    <input type="number"
                           id="mobile"
                           name="mobile"
                           class="form-control required"
                           placeholder="Enter mobile number"
                           value="{{ !$is_new ? session('staff.mobile') : '' }}">
                    <label for="mobile">Mobile No.</label>
                    <div class="invalid-feedback text-danger invalid-feedback-mobile"></div>
                </div>
            </div>

            {{-- Email --}}
            <div class="col mb-4 mt-2">
                <div class="form-floating form-floating-outline">
                    <input type="text"
                           id="email"
                           name="email"
                           class="form-control"
                           placeholder="Enter email"
                           value="{{ !$is_new ? session('staff.email') : '' }}">
                    <label for="email">Email Address</label>
                    <div class="invalid-feedback text-danger invalid-feedback-email"></div>
                </div>
            </div>

        </div>

        {{-- SALARY + PASSWORD only for new employee --}}
        @if ($is_new)
        <div class="row">

            {{-- Salary --}}
            <div class="col mb-4 mt-2">
                <div class="form-floating form-floating-outline">
                    <input type="number"
                           id="salary"
                           name="salary"
                           class="form-control required"
                           placeholder="Enter salary">
                    <label for="salary">Salary</label>
                    <div class="invalid-feedback text-danger invalid-feedback-salary"></div>
                </div>
            </div>

            {{-- Password --}}
            <div class="col mb-4 mt-2">
                <div class="form-floating form-floating-outline">
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-control required"
                           placeholder="Enter password">
                    <label for="password">Password</label>
                    <div class="invalid-feedback text-danger invalid-feedback-password"></div>
                </div>
            </div>

        </div>
        @endif

        <div class="row">

            {{-- Role --}}
            <div class="col mb-4 mt-2">
                <div class="form-floating form-floating-outline">
                    <select class="form-select required" id="role" name="role">
                        <option value="">Select Role</option>

                        @foreach ($staff_role as $id => $role)
                            <option value="{{ $id }}"
                                {{ !$is_new && session('staff.role_key') == $id ? 'selected' : '' }}>
                                {{ $role }}
                            </option>
                        @endforeach

                    </select>
                    <label for="role">Role</label>
                    <div class="invalid-feedback text-danger invalid-feedback-role"></div>
                </div>
            </div>

            {{-- Status --}}
            <div class="col mb-4 mt-4 align-content-center">
                <div class="form-check form-switch mb-2">
                    <label class="form-check-label" for="status">Status</label>
                    <input class="form-check-input"
                           type="checkbox"
                           id="status"
                           name="status"
                           {{ !$is_new && session('staff.status') ? 'checked' : '' }}>
                </div>
            </div>

        </div>

        {{-- Buttons --}}
        @if ($is_new)
            <button type="button" id="close-emp-modal"
                    class="btn btn-outline-secondary float-end mx-1">
                Close
            </button>

            <button type="button" id="employee-save"
                    class="btn btn-primary float-end mx-1">
                Save
            </button>
        @else
            <button type="button"
                    id="employee-update"
                    class="btn btn-primary float-end mx-1"
                    data-id="{{ session('staff.id') }}">
                Update
            </button>
        @endif

    </form>
</div>
