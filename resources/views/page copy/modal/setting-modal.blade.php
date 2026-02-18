<div class="modal fade" id="addSettingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">

            {{-- Modal Header --}}
            <div class="modal-header d-block">
                <h4 class="modal-title" id="addSettingModalLabel">Add Setting</h4>
                <div class="loader-line mt-3 d-none"></div>
            </div>

            {{-- Modal Body --}}
            <div class="modal-body">

                {{-- Inline Alerts --}}
                @include('temp.alert-inline')

                {{-- Setting Form --}}
                @include('page.edit-setting', [
                    'is_new' => true,
                    'module' => $module
                ])

            </div>

        </div>
    </div>
</div>
