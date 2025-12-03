@php
    use App\Models\Settings;
    $types = Settings::getTypes();
@endphp

<div id="addSettingBody">

    <form id="addSettingForm">

        <input type="hidden" name="id" id="setting_id" value="" />
        <input type="hidden" name="setting_module" id="setting_module" value="{{ $module }}" />

        {{-- Row 1: Name + Label --}}
        <div class="row">
            <div class="col mb-4">
                <div class="form-floating form-floating-outline">
                    <input type="text" id="setting_name" name="setting_name" class="form-control required"
                        placeholder="Enter Setting Name">

                    <label for="setting_name">Name</label>
                    <div class="text-danger invalid-feedback-setting_name"></div>
                </div>
            </div>

            <div class="col mb-4">
                <div class="form-floating form-floating-outline">
                    <input type="text" id="setting_label" name="setting_label" class="form-control required"
                        placeholder="Enter Setting Label">

                    <label for="setting_label">Label</label>
                    <div class="text-danger invalid-feedback-setting_label"></div>
                </div>
            </div>
        </div>

        {{-- Row 2: Type + Attribute --}}
        <div class="row">
            <div class="col mb-4">
                <div class="form-floating form-floating-outline">

                    <select class="form-select required" id="setting_type" name="setting_type">
                        <option value="">Select Setting Type...</option>
                        @foreach ($types as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>

                    <label for="setting_type">Type</label>
                </div>
            </div>

            <div class="col mb-4">
                <div class="form-floating form-floating-outline">
                    <input type="text" id="setting_attr" name="setting_attr" class="form-control"
                        placeholder="Enter Input Attributes (optional)">

                    <label for="setting_attr">Attributes</label>
                    <div class="text-danger invalid-feedback-setting_attr"></div>
                </div>
            </div>
        </div>

        {{-- Row 3: Options (select / radio / checkbox) --}}
        <div class="row">
            <div class="col mb-4">
                <div class="form-floating form-floating-outline">
                    <textarea class="form-control h-px-100" id="setting_option" name="setting_option"
                        placeholder="Enter options (key:value OR value)
Example:
1:Active || 0:Inactive" disabled></textarea>

                    <label for="setting_option">Options</label>
                </div>
            </div>
        </div>

        {{-- Footer Buttons --}}
        <div class="d-flex justify-content-end mt-2">
            <button type="button" id="close-set-modal" class="btn btn-outline-secondary me-2">
                Close
            </button>

            <button type="button" id="setting-update" data-id="" data-module="{{ $module }}"
                class="btn btn-primary">
                @if ($is_new)
                    Save
                @else
                    Update
                @endif
            </button>
        </div>

    </form>

</div>
